<?php

namespace App\Support;

use App\Models\CrmFollowUp;
use App\Models\CrmLeadAssignment;
use App\Models\CrmLeadNote;
use App\Models\CrmLeadSource;
use App\Models\CrmStatus;
use App\Models\CrmStatusUpdate;
use App\Models\CrmTask;
use App\Models\Inquiry;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CrmReportService
{
    public function __construct(
        protected CrmDelayedLeadService $delayedLeadService
    ) {
    }

    public function build(Request $request, ?User $viewer): array
    {
        $filters = $this->normalizeFilters($request);
        $leadBaseQuery = $this->filteredLeadQuery($viewer, $filters);
        $leadIdSubquery = (clone $leadBaseQuery)->select('inquiries.id');

        $statuses = CrmStatus::query()->where('is_active', true)->orderBy('sort_order')->get()->keyBy('id');
        $sources = CrmLeadSource::query()->where('is_active', true)->orderBy('sort_order')->get()->keyBy('id');
        $users = $this->reportUsers($viewer)->keyBy('id');

        $statusSlugIds = [
            'no_answer' => (int) CrmStatus::query()->where('slug', 'no-answer')->value('id'),
            'complete_lead' => (int) CrmStatus::query()->where('slug', 'complete-lead')->value('id'),
            'documents_complete' => (int) CrmStatus::query()->where('slug', 'documents-complete')->value('id'),
            'merged' => (int) CrmStatus::query()->where('slug', 'merged')->value('id'),
        ];
        $conversionStatusIds = array_values(array_filter([
            $statusSlugIds['complete_lead'],
            $statusSlugIds['documents_complete'],
        ]));

        $notesQuery = $this->notesQuery($leadIdSubquery, $filters);
        $statusUpdatesQuery = $this->statusUpdatesQuery($leadIdSubquery, $filters);
        $assignmentChangesQuery = $this->assignmentChangesQuery($leadIdSubquery, $filters);
        $followUpsScheduledQuery = $this->followUpsScheduledQuery($leadIdSubquery, $filters);
        $followUpsCompletedQuery = $this->followUpsCompletedQuery($leadIdSubquery, $filters);
        $tasksCreatedQuery = $this->tasksCreatedQuery($leadIdSubquery, $filters);
        $tasksCompletedQuery = $this->tasksCompletedQuery($leadIdSubquery, $filters);

        $notesCount = (clone $notesQuery)->count();
        $statusChangesCount = (clone $statusUpdatesQuery)->count();
        $assignmentChangesCount = (clone $assignmentChangesQuery)->count();
        $followUpsScheduledCount = (clone $followUpsScheduledQuery)->count();
        $followUpsCompletedCount = (clone $followUpsCompletedQuery)->count();
        $tasksCreatedCount = (clone $tasksCreatedQuery)->count();
        $tasksCompletedCount = (clone $tasksCompletedQuery)->count();

        $handledLeadIds = $this->handledLeadIds(
            $notesQuery,
            $statusUpdatesQuery,
            $assignmentChangesQuery,
            $followUpsScheduledQuery,
            $followUpsCompletedQuery,
            $tasksCreatedQuery,
            $tasksCompletedQuery
        );

        $handledLeadsCount = $handledLeadIds->count();
        $newLeadsCount = (clone $leadBaseQuery)
            ->whereBetween('inquiries.created_at', [$filters['from_at'], $filters['to_at']])
            ->count();

        $assignedLeadsCount = (clone $this->assignmentsToOwnersQuery($leadIdSubquery, $filters))->count();
        $reassignedLeadsCount = (clone $assignmentChangesQuery)->whereNotNull('old_assigned_user_id')->count();
        $noAnswerCount = $statusSlugIds['no_answer']
            ? (clone $statusUpdatesQuery)->where('new_status_id', $statusSlugIds['no_answer'])->count()
            : 0;
        $convertedCount = ! empty($conversionStatusIds)
            ? (clone $statusUpdatesQuery)->whereIn('new_status_id', $conversionStatusIds)->count()
            : 0;
        $mergedCount = $statusSlugIds['merged']
            ? (clone $statusUpdatesQuery)->where('new_status_id', $statusSlugIds['merged'])->count()
            : 0;

        $currentOverdueFollowUpsQuery = $this->currentOverdueFollowUpsQuery($leadIdSubquery);
        $currentOverdueFollowUpsCount = (clone $currentOverdueFollowUpsQuery)->count();
        $overdueFollowUpsHandledCount = (clone $followUpsCompletedQuery)
            ->whereColumn('scheduled_at', '<', 'completed_at')
            ->count();
        $averageOverdueDays = round(
            (clone $currentOverdueFollowUpsQuery)
                ->pluck('scheduled_at')
                ->filter()
                ->map(fn ($scheduledAt) => Carbon::parse($scheduledAt)->diffInDays(now()))
                ->avg() ?: 0,
            1
        );

        $delayedMeta = $this->delayedMetaSummary($leadBaseQuery);
        $delayedLeadCount = $delayedMeta['total'];
        $delayedActedOnCount = $handledLeadIds->intersect($delayedMeta['ids'])->count();

        $totalActions = $notesCount
            + $statusChangesCount
            + $assignmentChangesCount
            + $followUpsScheduledCount
            + $followUpsCompletedCount
            + $tasksCreatedCount
            + $tasksCompletedCount;

        $employee = $filters['employee_id'] ? $users->get($filters['employee_id']) : null;

        return [
            'filters' => $filters,
            'summary' => [
                'handled_leads' => $handledLeadsCount,
                'new_leads' => $newLeadsCount,
                'assigned_leads' => $assignedLeadsCount,
                'status_changes' => $statusChangesCount,
                'notes_added' => $notesCount,
                'follow_ups_scheduled' => $followUpsScheduledCount,
                'follow_ups_completed' => $followUpsCompletedCount,
                'overdue_follow_ups' => $currentOverdueFollowUpsCount,
                'overdue_follow_ups_handled' => $overdueFollowUpsHandledCount,
                'tasks_created' => $tasksCreatedCount,
                'tasks_completed' => $tasksCompletedCount,
                'delayed_leads' => $delayedLeadCount,
                'delayed_leads_acted_on' => $delayedActedOnCount,
                'no_answer' => $noAnswerCount,
                'converted' => $convertedCount,
                'merged' => $mergedCount,
                'reassigned' => $reassignedLeadsCount,
                'total_actions' => $totalActions,
                'conversion_rate' => $this->percentage($convertedCount, $handledLeadsCount),
                'no_answer_rate' => $this->percentage($noAnswerCount, $handledLeadsCount),
                'actions_per_lead' => $handledLeadsCount > 0 ? round($totalActions / $handledLeadsCount, 1) : 0,
                'notes_per_lead' => $handledLeadsCount > 0 ? round($notesCount / $handledLeadsCount, 1) : 0,
                'avg_first_response_hours' => $this->averageFirstResponseHours($handledLeadIds, $filters),
                'average_overdue_days' => $averageOverdueDays,
            ],
            'selectedEmployee' => $employee,
            'employeeComparison' => $filters['employee_id'] ? collect() : $this->employeeComparison(
                $users->values(),
                $leadIdSubquery,
                $leadBaseQuery,
                $filters,
                $statusSlugIds,
                $conversionStatusIds,
                $delayedMeta['ids']
            ),
            'activityLog' => $this->activityLog($leadIdSubquery, $filters),
            'statusMovement' => $this->statusMovement($statusUpdatesQuery, $statuses),
            'statusTransitions' => $this->statusTransitions($statusUpdatesQuery, $statuses),
            'currentStatusBreakdown' => $this->currentStatusBreakdown($leadBaseQuery, $statuses),
            'sourceBreakdown' => $this->sourceBreakdown($leadBaseQuery, $leadIdSubquery, $sources, $statusSlugIds, $conversionStatusIds, $filters),
            'dailyBreakdown' => $this->dailyBreakdown(
                $notesQuery,
                $statusUpdatesQuery,
                $followUpsScheduledQuery,
                $followUpsCompletedQuery,
                $tasksCreatedQuery,
                $tasksCompletedQuery
            ),
            'warningInsights' => [
                'scheduled_untouched' => $delayedMeta['scheduled_untouched'],
                'inactive_five_days' => $delayedMeta['inactive'],
                'overdue_follow_ups' => $currentOverdueFollowUpsCount,
                'average_overdue_days' => $averageOverdueDays,
                'preview' => $delayedMeta['preview'],
            ],
            'supportedMetrics' => [
                'notes',
                'status changes',
                'lead assignments',
                'follow-up scheduling',
                'follow-up completion',
                'task creation',
                'task completion by assigned employee',
                'status transitions',
                'delayed lead insights',
            ],
            'skippedMetrics' => [
                'explicit call counts (no dedicated calls table found)',
                'answered/unanswered call metrics',
                'explicit email sent counts',
                'explicit WhatsApp sent counts',
                'exact lead-created-by-employee metric (lead created_by is not stored on inquiries)',
            ],
        ];
    }

    protected function normalizeFilters(Request $request): array
    {
        $day = $request->filled('day') ? Carbon::parse($request->string('day')->toString()) : null;

        if ($day) {
            $from = $day->copy()->startOfDay();
            $to = $day->copy()->endOfDay();
        } else {
            $from = $request->filled('from_date')
                ? Carbon::parse($request->string('from_date')->toString())->startOfDay()
                : today()->startOfDay();
            $to = $request->filled('to_date')
                ? Carbon::parse($request->string('to_date')->toString())->endOfDay()
                : ($request->filled('from_date') ? Carbon::parse($request->string('from_date')->toString())->endOfDay() : today()->endOfDay());
        }

        if ($from->greaterThan($to)) {
            [$from, $to] = [$to->copy()->startOfDay(), $from->copy()->endOfDay()];
        }

        return [
            'day' => $day?->toDateString(),
            'from_date' => $from->toDateString(),
            'to_date' => $to->toDateString(),
            'from_at' => $from,
            'to_at' => $to,
            'employee_id' => $request->filled('employee_id') ? $request->integer('employee_id') : null,
            'crm_status_id' => $request->filled('crm_status_id') ? $request->integer('crm_status_id') : null,
            'crm_source_id' => $request->filled('crm_source_id') ? $request->integer('crm_source_id') : null,
            'assigned_user_id' => $request->filled('assigned_user_id') ? $request->string('assigned_user_id')->toString() : null,
            'assignment_state' => $request->string('assignment_state')->toString() ?: 'all',
            'delayed_state' => $request->string('delayed_state')->toString() ?: 'all',
        ];
    }

    protected function filteredLeadQuery(?User $viewer, array $filters, bool $applyDelayedState = true): Builder
    {
        $query = CrmLeadAccess::applyVisibilityScope(Inquiry::query(), $viewer);

        if (! empty($filters['crm_status_id'])) {
            $query->where('inquiries.crm_status_id', $filters['crm_status_id']);
        }

        if (! empty($filters['crm_source_id'])) {
            $query->where('inquiries.crm_source_id', $filters['crm_source_id']);
        }

        if (($filters['assigned_user_id'] ?? null) === 'unassigned') {
            $query->whereNull('inquiries.assigned_user_id');
        } elseif (! empty($filters['assigned_user_id'])) {
            $query->where('inquiries.assigned_user_id', (int) $filters['assigned_user_id']);
        } elseif (($filters['assignment_state'] ?? 'all') === 'assigned') {
            $query->whereNotNull('inquiries.assigned_user_id');
        } elseif (($filters['assignment_state'] ?? 'all') === 'unassigned') {
            $query->whereNull('inquiries.assigned_user_id');
        }

        if ($applyDelayedState && ($filters['delayed_state'] ?? 'all') !== 'all') {
            $delayedIds = $this->delayedLeadService
                ->applyDelayedScope((clone $query))
                ->pluck('inquiries.id');

            if (($filters['delayed_state'] ?? 'all') === 'delayed') {
                $query->whereIn('inquiries.id', $delayedIds->isNotEmpty() ? $delayedIds : [-1]);
            } else {
                $query->whereNotIn('inquiries.id', $delayedIds->isNotEmpty() ? $delayedIds : [-1]);
            }
        }

        return $query;
    }

    protected function reportUsers(?User $viewer): Collection
    {
        $query = User::query()->where('is_active', true)->orderBy('name');

        if (! CrmLeadAccess::canViewAll($viewer)) {
            $query->whereKey($viewer?->id);
        }

        return $query->get();
    }

    protected function notesQuery($leadIdSubquery, array $filters)
    {
        return CrmLeadNote::query()
            ->whereIn('inquiry_id', $leadIdSubquery)
            ->whereBetween('created_at', [$filters['from_at'], $filters['to_at']])
            ->when($filters['employee_id'], fn ($query, $employeeId) => $query->where('user_id', $employeeId));
    }

    protected function statusUpdatesQuery($leadIdSubquery, array $filters)
    {
        return CrmStatusUpdate::query()
            ->whereIn('inquiry_id', $leadIdSubquery)
            ->whereBetween('changed_at', [$filters['from_at'], $filters['to_at']])
            ->when($filters['employee_id'], fn ($query, $employeeId) => $query->where('changed_by', $employeeId));
    }

    protected function assignmentChangesQuery($leadIdSubquery, array $filters)
    {
        return CrmLeadAssignment::query()
            ->whereIn('inquiry_id', $leadIdSubquery)
            ->whereBetween('changed_at', [$filters['from_at'], $filters['to_at']])
            ->when($filters['employee_id'], fn ($query, $employeeId) => $query->where('changed_by', $employeeId));
    }

    protected function assignmentsToOwnersQuery($leadIdSubquery, array $filters)
    {
        return CrmLeadAssignment::query()
            ->whereIn('inquiry_id', $leadIdSubquery)
            ->whereBetween('changed_at', [$filters['from_at'], $filters['to_at']])
            ->when($filters['employee_id'], fn ($query, $employeeId) => $query->where('new_assigned_user_id', $employeeId));
    }

    protected function followUpsScheduledQuery($leadIdSubquery, array $filters)
    {
        return CrmFollowUp::query()
            ->whereIn('inquiry_id', $leadIdSubquery)
            ->whereBetween('created_at', [$filters['from_at'], $filters['to_at']])
            ->when($filters['employee_id'], fn ($query, $employeeId) => $query->where('created_by', $employeeId));
    }

    protected function followUpsCompletedQuery($leadIdSubquery, array $filters)
    {
        return CrmFollowUp::query()
            ->whereIn('inquiry_id', $leadIdSubquery)
            ->whereNotNull('completed_at')
            ->whereBetween('completed_at', [$filters['from_at'], $filters['to_at']])
            ->when($filters['employee_id'], fn ($query, $employeeId) => $query->where('completed_by', $employeeId));
    }

    protected function tasksCreatedQuery($leadIdSubquery, array $filters)
    {
        return CrmTask::query()
            ->whereIn('inquiry_id', $leadIdSubquery)
            ->whereBetween('created_at', [$filters['from_at'], $filters['to_at']])
            ->when($filters['employee_id'], fn ($query, $employeeId) => $query->where('created_by', $employeeId));
    }

    protected function tasksCompletedQuery($leadIdSubquery, array $filters)
    {
        return CrmTask::query()
            ->whereIn('inquiry_id', $leadIdSubquery)
            ->whereNotNull('completed_at')
            ->whereBetween('completed_at', [$filters['from_at'], $filters['to_at']])
            ->when($filters['employee_id'], fn ($query, $employeeId) => $query->where('assigned_user_id', $employeeId));
    }

    protected function currentOverdueFollowUpsQuery($leadIdSubquery)
    {
        return CrmFollowUp::query()
            ->whereIn('inquiry_id', $leadIdSubquery)
            ->where('status', CrmFollowUp::STATUS_PENDING)
            ->where('scheduled_at', '<', now());
    }

    protected function handledLeadIds(...$queries): Collection
    {
        return collect($queries)
            ->flatMap(fn ($query) => (clone $query)->distinct()->pluck('inquiry_id'))
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values();
    }

    protected function delayedMetaSummary(Builder $leadBaseQuery): array
    {
        $delayedQuery = $this->delayedLeadService->applyDelayedScope((clone $leadBaseQuery));
        $delayedLeads = $this->delayedLeadService->annotate($delayedQuery->get());

        $preview = $this->delayedLeadService->annotate(
            $this->delayedLeadService
                ->applyDelayedScope((clone $leadBaseQuery)->with(['crmStatus', 'crmSource', 'assignedUser']))
                ->limit(8)
                ->get()
        );

        return [
            'total' => $delayedLeads->count(),
            'ids' => $delayedLeads->pluck('id')->map(fn ($id) => (int) $id)->values(),
            'scheduled_untouched' => $delayedLeads->where('delay_reason_type', 'overdue_follow_up')->count(),
            'inactive' => $delayedLeads->where('delay_reason_type', 'inactive')->count(),
            'preview' => $preview,
        ];
    }

    protected function averageFirstResponseHours(Collection $handledLeadIds, array $filters): float
    {
        if ($handledLeadIds->isEmpty()) {
            return 0;
        }

        $leadIds = $handledLeadIds->take(300);
        $actionTimes = [];

        $this->mergeFirstActionTimes(
            $actionTimes,
            CrmLeadNote::query()
                ->select('inquiry_id', DB::raw('MIN(created_at) as first_at'))
                ->whereIn('inquiry_id', $leadIds)
                ->when($filters['employee_id'], fn ($query, $employeeId) => $query->where('user_id', $employeeId))
                ->groupBy('inquiry_id')
                ->get()
        );
        $this->mergeFirstActionTimes(
            $actionTimes,
            CrmStatusUpdate::query()
                ->select('inquiry_id', DB::raw('MIN(changed_at) as first_at'))
                ->whereIn('inquiry_id', $leadIds)
                ->when($filters['employee_id'], fn ($query, $employeeId) => $query->where('changed_by', $employeeId))
                ->groupBy('inquiry_id')
                ->get()
        );
        $this->mergeFirstActionTimes(
            $actionTimes,
            CrmLeadAssignment::query()
                ->select('inquiry_id', DB::raw('MIN(changed_at) as first_at'))
                ->whereIn('inquiry_id', $leadIds)
                ->when($filters['employee_id'], fn ($query, $employeeId) => $query->where('changed_by', $employeeId))
                ->groupBy('inquiry_id')
                ->get()
        );
        $this->mergeFirstActionTimes(
            $actionTimes,
            CrmFollowUp::query()
                ->select('inquiry_id', DB::raw('MIN(created_at) as first_at'))
                ->whereIn('inquiry_id', $leadIds)
                ->when($filters['employee_id'], fn ($query, $employeeId) => $query->where('created_by', $employeeId))
                ->groupBy('inquiry_id')
                ->get()
        );
        $this->mergeFirstActionTimes(
            $actionTimes,
            CrmTask::query()
                ->select('inquiry_id', DB::raw('MIN(created_at) as first_at'))
                ->whereIn('inquiry_id', $leadIds)
                ->when($filters['employee_id'], fn ($query, $employeeId) => $query->where('created_by', $employeeId))
                ->groupBy('inquiry_id')
                ->get()
        );

        $leads = Inquiry::query()->whereIn('id', array_keys($actionTimes))->get(['id', 'created_at']);
        $durations = $leads->map(function (Inquiry $lead) use ($actionTimes) {
            $firstActionAt = $actionTimes[$lead->id] ?? null;

            if (! $lead->created_at || ! $firstActionAt) {
                return null;
            }

            return round(Carbon::parse($lead->created_at)->diffInMinutes(Carbon::parse($firstActionAt)) / 60, 1);
        })->filter();

        return $durations->isNotEmpty() ? round($durations->avg(), 1) : 0;
    }

    protected function mergeFirstActionTimes(array &$actionTimes, Collection $rows): void
    {
        foreach ($rows as $row) {
            if (blank($row->first_at)) {
                continue;
            }

            $inquiryId = (int) $row->inquiry_id;
            $timestamp = Carbon::parse($row->first_at);

            if (! isset($actionTimes[$inquiryId]) || $timestamp->lt(Carbon::parse($actionTimes[$inquiryId]))) {
                $actionTimes[$inquiryId] = $timestamp->toDateTimeString();
            }
        }
    }

    protected function employeeComparison(
        Collection $users,
        $leadIdSubquery,
        Builder $leadBaseQuery,
        array $filters,
        array $statusSlugIds,
        array $conversionStatusIds,
        Collection $delayedIds
    ): Collection {
        $notesByUser = $this->groupCount($this->notesQuery($leadIdSubquery, $filters), 'user_id');
        $statusByUser = $this->groupCount($this->statusUpdatesQuery($leadIdSubquery, $filters), 'changed_by');
        $followUpsByUser = $this->groupCount($this->followUpsScheduledQuery($leadIdSubquery, $filters), 'created_by');
        $followUpsCompletedByUser = $this->groupCount($this->followUpsCompletedQuery($leadIdSubquery, $filters), 'completed_by');
        $tasksCreatedByUser = $this->groupCount($this->tasksCreatedQuery($leadIdSubquery, $filters), 'created_by');
        $tasksCompletedByUser = $this->groupCount($this->tasksCompletedQuery($leadIdSubquery, $filters), 'assigned_user_id');
        $assignmentsChangedByUser = $this->groupCount($this->assignmentChangesQuery($leadIdSubquery, $filters), 'changed_by');
        $assignedToUser = $this->groupCount($this->assignmentsToOwnersQuery($leadIdSubquery, $filters), 'new_assigned_user_id');
        $noAnswerByUser = $statusSlugIds['no_answer']
            ? $this->groupCount((clone $this->statusUpdatesQuery($leadIdSubquery, $filters))->where('new_status_id', $statusSlugIds['no_answer']), 'changed_by')
            : collect();
        $convertedByUser = ! empty($conversionStatusIds)
            ? $this->groupCount((clone $this->statusUpdatesQuery($leadIdSubquery, $filters))->whereIn('new_status_id', $conversionStatusIds), 'changed_by')
            : collect();

        $handledPairs = collect()
            ->concat((clone $this->notesQuery($leadIdSubquery, $filters))->get(['user_id as actor_id', 'inquiry_id']))
            ->concat((clone $this->statusUpdatesQuery($leadIdSubquery, $filters))->get(['changed_by as actor_id', 'inquiry_id']))
            ->concat((clone $this->assignmentChangesQuery($leadIdSubquery, $filters))->get(['changed_by as actor_id', 'inquiry_id']))
            ->concat((clone $this->followUpsScheduledQuery($leadIdSubquery, $filters))->get(['created_by as actor_id', 'inquiry_id']))
            ->concat((clone $this->followUpsCompletedQuery($leadIdSubquery, $filters))->get(['completed_by as actor_id', 'inquiry_id']))
            ->concat((clone $this->tasksCreatedQuery($leadIdSubquery, $filters))->get(['created_by as actor_id', 'inquiry_id']))
            ->concat((clone $this->tasksCompletedQuery($leadIdSubquery, $filters))->get(['assigned_user_id as actor_id', 'inquiry_id']))
            ->filter(fn ($row) => ! blank($row->actor_id) && ! blank($row->inquiry_id));

        $handledByUser = $handledPairs
            ->groupBy('actor_id')
            ->map(fn (Collection $rows) => $rows->pluck('inquiry_id')->unique()->count());

        $lastActivityByUser = $this->lastActivityByUser($leadIdSubquery, $filters);
        $overdueByUser = (clone $this->currentOverdueFollowUpsQuery($leadIdSubquery))
            ->select('assigned_user_id', DB::raw('COUNT(*) as total'))
            ->whereNotNull('assigned_user_id')
            ->groupBy('assigned_user_id')
            ->pluck('total', 'assigned_user_id');
        $delayedByUser = (clone $leadBaseQuery)
            ->when($delayedIds->isNotEmpty(), fn ($query) => $query->whereIn('inquiries.id', $delayedIds))
            ->select('assigned_user_id', DB::raw('COUNT(*) as total'))
            ->whereNotNull('assigned_user_id')
            ->groupBy('assigned_user_id')
            ->pluck('total', 'assigned_user_id');

        return $users->map(function (User $user) use (
            $notesByUser,
            $statusByUser,
            $followUpsByUser,
            $followUpsCompletedByUser,
            $tasksCreatedByUser,
            $tasksCompletedByUser,
            $assignmentsChangedByUser,
            $assignedToUser,
            $noAnswerByUser,
            $convertedByUser,
            $handledByUser,
            $lastActivityByUser,
            $overdueByUser,
            $delayedByUser
        ) {
            $handled = (int) ($handledByUser->get($user->id) ?? 0);
            $converted = (int) ($convertedByUser->get($user->id) ?? 0);
            $noAnswer = (int) ($noAnswerByUser->get($user->id) ?? 0);

            return [
                'user' => $user,
                'assigned' => (int) ($assignedToUser->get($user->id) ?? 0),
                'handled' => $handled,
                'notes' => (int) ($notesByUser->get($user->id) ?? 0),
                'status_changes' => (int) ($statusByUser->get($user->id) ?? 0),
                'follow_ups' => (int) ($followUpsByUser->get($user->id) ?? 0),
                'follow_ups_completed' => (int) ($followUpsCompletedByUser->get($user->id) ?? 0),
                'tasks_created' => (int) ($tasksCreatedByUser->get($user->id) ?? 0),
                'tasks_completed' => (int) ($tasksCompletedByUser->get($user->id) ?? 0),
                'assignment_changes' => (int) ($assignmentsChangedByUser->get($user->id) ?? 0),
                'overdue' => (int) ($overdueByUser->get($user->id) ?? 0),
                'delayed' => (int) ($delayedByUser->get($user->id) ?? 0),
                'converted' => $converted,
                'no_answer' => $noAnswer,
                'conversion_rate' => $this->percentage($converted, $handled),
                'no_answer_rate' => $this->percentage($noAnswer, $handled),
                'last_activity_at' => $lastActivityByUser->get($user->id),
            ];
        })->sortByDesc('handled')->values();
    }

    protected function groupCount($query, string $column): Collection
    {
        return (clone $query)
            ->select($column, DB::raw('COUNT(*) as total'))
            ->whereNotNull($column)
            ->groupBy($column)
            ->pluck('total', $column);
    }

    protected function lastActivityByUser($leadIdSubquery, array $filters): Collection
    {
        $maps = collect([
            (clone $this->notesQuery($leadIdSubquery, $filters))
                ->select('user_id as actor_id', DB::raw('MAX(created_at) as last_at'))
                ->whereNotNull('user_id')
                ->groupBy('user_id')
                ->get(),
            (clone $this->statusUpdatesQuery($leadIdSubquery, $filters))
                ->select('changed_by as actor_id', DB::raw('MAX(changed_at) as last_at'))
                ->whereNotNull('changed_by')
                ->groupBy('changed_by')
                ->get(),
            (clone $this->assignmentChangesQuery($leadIdSubquery, $filters))
                ->select('changed_by as actor_id', DB::raw('MAX(changed_at) as last_at'))
                ->whereNotNull('changed_by')
                ->groupBy('changed_by')
                ->get(),
            (clone $this->followUpsScheduledQuery($leadIdSubquery, $filters))
                ->select('created_by as actor_id', DB::raw('MAX(created_at) as last_at'))
                ->whereNotNull('created_by')
                ->groupBy('created_by')
                ->get(),
            (clone $this->followUpsCompletedQuery($leadIdSubquery, $filters))
                ->select('completed_by as actor_id', DB::raw('MAX(completed_at) as last_at'))
                ->whereNotNull('completed_by')
                ->groupBy('completed_by')
                ->get(),
            (clone $this->tasksCreatedQuery($leadIdSubquery, $filters))
                ->select('created_by as actor_id', DB::raw('MAX(created_at) as last_at'))
                ->whereNotNull('created_by')
                ->groupBy('created_by')
                ->get(),
            (clone $this->tasksCompletedQuery($leadIdSubquery, $filters))
                ->select('assigned_user_id as actor_id', DB::raw('MAX(completed_at) as last_at'))
                ->whereNotNull('assigned_user_id')
                ->groupBy('assigned_user_id')
                ->get(),
        ]);

        $result = collect();

        $maps->flatten(1)->each(function ($row) use (&$result) {
            if (blank($row->actor_id) || blank($row->last_at)) {
                return;
            }

            $existing = $result->get($row->actor_id);
            $current = Carbon::parse($row->last_at);

            if (! $existing || $current->gt(Carbon::parse($existing))) {
                $result->put((int) $row->actor_id, $current->toDateTimeString());
            }
        });

        return $result;
    }

    protected function statusMovement($statusUpdatesQuery, Collection $statuses): Collection
    {
        return (clone $statusUpdatesQuery)
            ->select('new_status_id', DB::raw('COUNT(*) as total'))
            ->whereNotNull('new_status_id')
            ->groupBy('new_status_id')
            ->orderByDesc('total')
            ->get()
            ->map(function ($row) use ($statuses) {
                $status = $statuses->get((int) $row->new_status_id);

                return [
                    'label' => $status?->localizedName() ?: 'غير محدد',
                    'color' => $status?->color ?: 'secondary',
                    'total' => (int) $row->total,
                ];
            });
    }

    protected function statusTransitions($statusUpdatesQuery, Collection $statuses): Collection
    {
        return (clone $statusUpdatesQuery)
            ->select('old_status_id', 'new_status_id', DB::raw('COUNT(*) as total'))
            ->whereNotNull('old_status_id')
            ->whereNotNull('new_status_id')
            ->groupBy('old_status_id', 'new_status_id')
            ->orderByDesc('total')
            ->limit(12)
            ->get()
            ->map(function ($row) use ($statuses) {
                $old = $statuses->get((int) $row->old_status_id);
                $new = $statuses->get((int) $row->new_status_id);

                return [
                    'from' => $old?->localizedName() ?: 'غير محدد',
                    'to' => $new?->localizedName() ?: 'غير محدد',
                    'total' => (int) $row->total,
                ];
            });
    }

    protected function currentStatusBreakdown(Builder $leadBaseQuery, Collection $statuses): Collection
    {
        return (clone $leadBaseQuery)
            ->select('crm_status_id', DB::raw('COUNT(*) as total'))
            ->whereNotNull('crm_status_id')
            ->groupBy('crm_status_id')
            ->orderByDesc('total')
            ->get()
            ->map(function ($row) use ($statuses) {
                $status = $statuses->get((int) $row->crm_status_id);

                return [
                    'label' => $status?->localizedName() ?: 'غير محدد',
                    'color' => $status?->color ?: 'secondary',
                    'total' => (int) $row->total,
                ];
            });
    }

    protected function sourceBreakdown(Builder $leadBaseQuery, $leadIdSubquery, Collection $sources, array $statusSlugIds, array $conversionStatusIds, array $filters): Collection
    {
        $leadCounts = (clone $leadBaseQuery)
            ->select('crm_source_id', DB::raw('COUNT(*) as total'))
            ->groupBy('crm_source_id')
            ->pluck('total', 'crm_source_id');

        $statusUpdates = CrmStatusUpdate::query()
            ->join('inquiries', 'inquiries.id', '=', 'crm_status_updates.inquiry_id')
            ->whereIn('crm_status_updates.inquiry_id', $leadIdSubquery)
            ->whereBetween('crm_status_updates.changed_at', [$filters['from_at'], $filters['to_at']])
            ->when($filters['employee_id'], fn ($query, $employeeId) => $query->where('crm_status_updates.changed_by', $employeeId))
            ->groupBy('inquiries.crm_source_id')
            ->select('inquiries.crm_source_id');

        $noAnswerBySource = $statusSlugIds['no_answer']
            ? (clone $statusUpdates)
                ->where('crm_status_updates.new_status_id', $statusSlugIds['no_answer'])
                ->selectRaw('COUNT(*) as total')
                ->pluck('total', 'crm_source_id')
            : collect();

        $convertedBySource = ! empty($conversionStatusIds)
            ? (clone $statusUpdates)
                ->whereIn('crm_status_updates.new_status_id', $conversionStatusIds)
                ->selectRaw('COUNT(*) as total')
                ->pluck('total', 'crm_source_id')
            : collect();

        $delayedBySource = $this->delayedLeadService
            ->applyDelayedScope((clone $leadBaseQuery))
            ->select('crm_source_id', DB::raw('COUNT(*) as total'))
            ->groupBy('crm_source_id')
            ->pluck('total', 'crm_source_id');

        return collect($leadCounts)
            ->keys()
            ->merge($noAnswerBySource->keys())
            ->merge($convertedBySource->keys())
            ->merge($delayedBySource->keys())
            ->unique()
            ->map(function ($sourceId) use ($sources, $leadCounts, $noAnswerBySource, $convertedBySource, $delayedBySource) {
                $source = $sources->get((int) $sourceId);
                $leadCount = (int) ($leadCounts->get($sourceId) ?? 0);
                $converted = (int) ($convertedBySource->get($sourceId) ?? 0);
                $noAnswer = (int) ($noAnswerBySource->get($sourceId) ?? 0);

                return [
                    'label' => $source?->localizedName() ?: 'غير محدد',
                    'lead_count' => $leadCount,
                    'converted' => $converted,
                    'no_answer' => $noAnswer,
                    'delayed' => (int) ($delayedBySource->get($sourceId) ?? 0),
                    'conversion_rate' => $this->percentage($converted, $leadCount),
                    'no_answer_rate' => $this->percentage($noAnswer, $leadCount),
                ];
            })
            ->sortByDesc('lead_count')
            ->values();
    }

    protected function dailyBreakdown(...$queries): Collection
    {
        $days = [];

        $this->mergeDailyCounts($days, (clone $queries[0])->selectRaw('DATE(created_at) as action_date, COUNT(*) as total')->groupBy(DB::raw('DATE(created_at)'))->get(), 'notes');
        $this->mergeDailyCounts($days, (clone $queries[1])->selectRaw('DATE(changed_at) as action_date, COUNT(*) as total')->groupBy(DB::raw('DATE(changed_at)'))->get(), 'status_changes');
        $this->mergeDailyCounts($days, (clone $queries[2])->selectRaw('DATE(created_at) as action_date, COUNT(*) as total')->groupBy(DB::raw('DATE(created_at)'))->get(), 'follow_ups_scheduled');
        $this->mergeDailyCounts($days, (clone $queries[3])->selectRaw('DATE(completed_at) as action_date, COUNT(*) as total')->groupBy(DB::raw('DATE(completed_at)'))->get(), 'follow_ups_completed');
        $this->mergeDailyCounts($days, (clone $queries[4])->selectRaw('DATE(created_at) as action_date, COUNT(*) as total')->groupBy(DB::raw('DATE(created_at)'))->get(), 'tasks_created');
        $this->mergeDailyCounts($days, (clone $queries[5])->selectRaw('DATE(completed_at) as action_date, COUNT(*) as total')->groupBy(DB::raw('DATE(completed_at)'))->get(), 'tasks_completed');

        return collect($days)
            ->sortKeysDesc()
            ->map(function ($row, $date) {
                $row['date'] = $date;
                $row['total_actions'] = $row['notes'] + $row['status_changes'] + $row['follow_ups_scheduled'] + $row['follow_ups_completed'] + $row['tasks_created'] + $row['tasks_completed'];

                return $row;
            })
            ->values();
    }

    protected function mergeDailyCounts(array &$days, Collection $rows, string $key): void
    {
        foreach ($rows as $row) {
            $date = (string) $row->action_date;

            if (! isset($days[$date])) {
                $days[$date] = [
                    'notes' => 0,
                    'status_changes' => 0,
                    'follow_ups_scheduled' => 0,
                    'follow_ups_completed' => 0,
                    'tasks_created' => 0,
                    'tasks_completed' => 0,
                ];
            }

            $days[$date][$key] = (int) $row->total;
        }
    }

    protected function activityLog($leadIdSubquery, array $filters): Collection
    {
        $notes = CrmLeadNote::query()
            ->with(['inquiry.crmSource', 'inquiry.assignedUser', 'user'])
            ->whereIn('inquiry_id', $leadIdSubquery)
            ->whereBetween('created_at', [$filters['from_at'], $filters['to_at']])
            ->when($filters['employee_id'], fn ($query, $employeeId) => $query->where('user_id', $employeeId))
            ->latest('created_at')
            ->limit(60)
            ->get()
            ->map(fn (CrmLeadNote $note) => [
                'lead_id' => $note->inquiry_id,
                'lead_name' => $note->inquiry?->full_name,
                'employee_name' => $note->user?->name,
                'action_type' => 'إضافة ملاحظة',
                'old_status' => null,
                'new_status' => null,
                'note' => mb_strimwidth((string) $note->body, 0, 140, '...'),
                'action_at' => $note->created_at,
                'follow_up_at' => null,
                'source' => $note->inquiry?->crmSource?->localizedName() ?: $note->inquiry?->lead_source,
                'link' => route('admin.crm.leads.show', $note->inquiry_id),
            ]);

        $statusChanges = CrmStatusUpdate::query()
            ->with(['inquiry.crmSource', 'user', 'oldStatus', 'newStatus'])
            ->whereIn('inquiry_id', $leadIdSubquery)
            ->whereBetween('changed_at', [$filters['from_at'], $filters['to_at']])
            ->when($filters['employee_id'], fn ($query, $employeeId) => $query->where('changed_by', $employeeId))
            ->latest('changed_at')
            ->limit(60)
            ->get()
            ->map(fn (CrmStatusUpdate $update) => [
                'lead_id' => $update->inquiry_id,
                'lead_name' => $update->inquiry?->full_name,
                'employee_name' => $update->user?->name,
                'action_type' => 'تغيير حالة',
                'old_status' => $update->oldStatus?->localizedName(),
                'new_status' => $update->newStatus?->localizedName(),
                'note' => $update->note,
                'action_at' => $update->changed_at,
                'follow_up_at' => null,
                'source' => $update->inquiry?->crmSource?->localizedName() ?: $update->inquiry?->lead_source,
                'link' => route('admin.crm.leads.show', $update->inquiry_id),
            ]);

        $assignments = CrmLeadAssignment::query()
            ->with(['inquiry.crmSource', 'changedByUser', 'oldAssignedUser', 'newAssignedUser'])
            ->whereIn('inquiry_id', $leadIdSubquery)
            ->whereBetween('changed_at', [$filters['from_at'], $filters['to_at']])
            ->when($filters['employee_id'], fn ($query, $employeeId) => $query->where('changed_by', $employeeId))
            ->latest('changed_at')
            ->limit(40)
            ->get()
            ->map(fn (CrmLeadAssignment $assignment) => [
                'lead_id' => $assignment->inquiry_id,
                'lead_name' => $assignment->inquiry?->full_name,
                'employee_name' => $assignment->changedByUser?->name,
                'action_type' => 'إعادة تعيين',
                'old_status' => $assignment->oldAssignedUser?->name,
                'new_status' => $assignment->newAssignedUser?->name,
                'note' => $assignment->note,
                'action_at' => $assignment->changed_at,
                'follow_up_at' => null,
                'source' => $assignment->inquiry?->crmSource?->localizedName() ?: $assignment->inquiry?->lead_source,
                'link' => route('admin.crm.leads.show', $assignment->inquiry_id),
            ]);

        $followUpsScheduled = CrmFollowUp::query()
            ->with(['inquiry.crmSource', 'creator', 'statusModel'])
            ->whereIn('inquiry_id', $leadIdSubquery)
            ->whereBetween('created_at', [$filters['from_at'], $filters['to_at']])
            ->when($filters['employee_id'], fn ($query, $employeeId) => $query->where('created_by', $employeeId))
            ->latest('created_at')
            ->limit(40)
            ->get()
            ->map(fn (CrmFollowUp $followUp) => [
                'lead_id' => $followUp->inquiry_id,
                'lead_name' => $followUp->inquiry?->full_name,
                'employee_name' => $followUp->creator?->name,
                'action_type' => 'جدولة متابعة',
                'old_status' => null,
                'new_status' => $followUp->statusModel?->localizedName(),
                'note' => $followUp->note,
                'action_at' => $followUp->created_at,
                'follow_up_at' => $followUp->scheduled_at,
                'source' => $followUp->inquiry?->crmSource?->localizedName() ?: $followUp->inquiry?->lead_source,
                'link' => route('admin.crm.leads.show', $followUp->inquiry_id),
            ]);

        $followUpsCompleted = CrmFollowUp::query()
            ->with(['inquiry.crmSource', 'completedBy'])
            ->whereIn('inquiry_id', $leadIdSubquery)
            ->whereNotNull('completed_at')
            ->whereBetween('completed_at', [$filters['from_at'], $filters['to_at']])
            ->when($filters['employee_id'], fn ($query, $employeeId) => $query->where('completed_by', $employeeId))
            ->latest('completed_at')
            ->limit(40)
            ->get()
            ->map(fn (CrmFollowUp $followUp) => [
                'lead_id' => $followUp->inquiry_id,
                'lead_name' => $followUp->inquiry?->full_name,
                'employee_name' => $followUp->completedBy?->name,
                'action_type' => 'إغلاق متابعة',
                'old_status' => null,
                'new_status' => ucfirst($followUp->status),
                'note' => $followUp->completion_note ?: $followUp->note,
                'action_at' => $followUp->completed_at,
                'follow_up_at' => $followUp->scheduled_at,
                'source' => $followUp->inquiry?->crmSource?->localizedName() ?: $followUp->inquiry?->lead_source,
                'link' => route('admin.crm.leads.show', $followUp->inquiry_id),
            ]);

        $tasksCreated = CrmTask::query()
            ->with(['inquiry.crmSource', 'creator'])
            ->whereIn('inquiry_id', $leadIdSubquery)
            ->whereBetween('created_at', [$filters['from_at'], $filters['to_at']])
            ->when($filters['employee_id'], fn ($query, $employeeId) => $query->where('created_by', $employeeId))
            ->latest('created_at')
            ->limit(40)
            ->get()
            ->map(fn (CrmTask $task) => [
                'lead_id' => $task->inquiry_id,
                'lead_name' => $task->inquiry?->full_name,
                'employee_name' => $task->creator?->name,
                'action_type' => 'إنشاء مهمة',
                'old_status' => null,
                'new_status' => ucfirst($task->status),
                'note' => $task->title,
                'action_at' => $task->created_at,
                'follow_up_at' => $task->due_at,
                'source' => $task->inquiry?->crmSource?->localizedName() ?: $task->inquiry?->lead_source,
                'link' => route('admin.crm.leads.show', $task->inquiry_id),
            ]);

        $tasksCompleted = CrmTask::query()
            ->with(['inquiry.crmSource', 'assignedUser'])
            ->whereIn('inquiry_id', $leadIdSubquery)
            ->whereNotNull('completed_at')
            ->whereBetween('completed_at', [$filters['from_at'], $filters['to_at']])
            ->when($filters['employee_id'], fn ($query, $employeeId) => $query->where('assigned_user_id', $employeeId))
            ->latest('completed_at')
            ->limit(40)
            ->get()
            ->map(fn (CrmTask $task) => [
                'lead_id' => $task->inquiry_id,
                'lead_name' => $task->inquiry?->full_name,
                'employee_name' => $task->assignedUser?->name,
                'action_type' => 'إكمال مهمة',
                'old_status' => null,
                'new_status' => ucfirst($task->status),
                'note' => $task->title,
                'action_at' => $task->completed_at,
                'follow_up_at' => $task->due_at,
                'source' => $task->inquiry?->crmSource?->localizedName() ?: $task->inquiry?->lead_source,
                'link' => route('admin.crm.leads.show', $task->inquiry_id),
            ]);

        return $notes
            ->concat($statusChanges)
            ->concat($assignments)
            ->concat($followUpsScheduled)
            ->concat($followUpsCompleted)
            ->concat($tasksCreated)
            ->concat($tasksCompleted)
            ->sortByDesc('action_at')
            ->take(180)
            ->values();
    }

    protected function percentage(int $numerator, int $denominator): float
    {
        if ($denominator <= 0) {
            return 0;
        }

        return round(($numerator / $denominator) * 100, 1);
    }
}
