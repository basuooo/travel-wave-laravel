<?php

namespace App\Support;

use App\Models\CrmTask;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class CrmTaskReportService
{
    public function build(Request $request, User $viewer, Collection $users, callable $baseQueryFactory): array
    {
        $filters = [
            'from' => $request->filled('from') ? $request->date('from')->toDateString() : null,
            'to' => $request->filled('to') ? $request->date('to')->toDateString() : null,
            'assigned_user_id' => $request->filled('assigned_user_id') ? $request->integer('assigned_user_id') : null,
        ];

        $tasks = $baseQueryFactory($request)->get();
        $activeTasks = $tasks->whereNotIn('status', [CrmTask::STATUS_COMPLETED, CrmTask::STATUS_CANCELLED]);
        $delayedTasks = $tasks->filter(fn (CrmTask $task) => $task->isDelayed());
        $todayTasks = $tasks->filter(fn (CrmTask $task) => optional($task->due_at)?->isToday());
        $tomorrowTasks = $tasks->filter(fn (CrmTask $task) => optional($task->due_at)?->isTomorrow());
        $weekTasks = $tasks->filter(fn (CrmTask $task) => $task->due_at && $task->due_at->between(now()->startOfDay(), now()->copy()->endOfWeek()));

        $employeeRows = $users->map(function (User $user) use ($tasks) {
            $rows = $tasks->where('assigned_user_id', $user->id);
            $assigned = $rows->count();
            $completed = $rows->where('status', CrmTask::STATUS_COMPLETED)->count();
            $delayed = $rows->filter(fn (CrmTask $task) => $task->isDelayed())->count();
            $waiting = $rows->where('status', CrmTask::STATUS_WAITING)->count();
            $cancelled = $rows->where('status', CrmTask::STATUS_CANCELLED)->count();
            $inProgress = $rows->where('status', CrmTask::STATUS_IN_PROGRESS)->count();
            $urgent = $rows->where('priority', CrmTask::PRIORITY_URGENT)->whereNotIn('status', [CrmTask::STATUS_COMPLETED, CrmTask::STATUS_CANCELLED])->count();
            $open = $rows->whereNotIn('status', [CrmTask::STATUS_COMPLETED, CrmTask::STATUS_CANCELLED])->count();
            $avgCompletionHours = $rows
                ->where('status', CrmTask::STATUS_COMPLETED)
                ->filter(fn (CrmTask $task) => $task->completed_at && $task->created_at)
                ->map(fn (CrmTask $task) => round($task->created_at->floatDiffInHours($task->completed_at), 2));

            return [
                'user' => $user,
                'assigned' => $assigned,
                'open' => $open,
                'completed' => $completed,
                'delayed' => $delayed,
                'in_progress' => $inProgress,
                'waiting' => $waiting,
                'cancelled' => $cancelled,
                'urgent' => $urgent,
                'completion_rate' => $assigned > 0 ? round(($completed / $assigned) * 100, 1) : 0,
                'delayed_rate' => $assigned > 0 ? round(($delayed / $assigned) * 100, 1) : 0,
                'average_completion_hours' => $avgCompletionHours->isNotEmpty() ? round($avgCompletionHours->avg(), 2) : null,
                'last_activity_at' => $rows->max('last_activity_at'),
            ];
        })->filter(fn (array $row) => $row['assigned'] > 0 || $viewer->id === $row['user']->id)->values();

        $leadDensity = $tasks
            ->filter(fn (CrmTask $task) => $task->inquiry_id !== null && $task->inquiry)
            ->groupBy('inquiry_id')
            ->map(function (Collection $rows) {
                $lead = $rows->first()->inquiry;
                $openCount = $rows->whereNotIn('status', [CrmTask::STATUS_COMPLETED, CrmTask::STATUS_CANCELLED])->count();
                $delayedCount = $rows->filter(fn (CrmTask $task) => $task->isDelayed())->count();

                return [
                    'lead' => $lead,
                    'total' => $rows->count(),
                    'open' => $openCount,
                    'delayed' => $delayedCount,
                    'latest_due_at' => $rows->filter(fn (CrmTask $task) => $task->due_at)->sortBy('due_at')->first()?->due_at,
                ];
            })
            ->sortByDesc('open')
            ->values();

        $aging = [
            'over_1_day' => $delayedTasks->filter(fn (CrmTask $task) => $task->due_at && $task->due_at->diffInDays(now()) >= 1)->count(),
            'over_3_days' => $delayedTasks->filter(fn (CrmTask $task) => $task->due_at && $task->due_at->diffInDays(now()) >= 3)->count(),
            'over_7_days' => $delayedTasks->filter(fn (CrmTask $task) => $task->due_at && $task->due_at->diffInDays(now()) >= 7)->count(),
            'over_14_days' => $delayedTasks->filter(fn (CrmTask $task) => $task->due_at && $task->due_at->diffInDays(now()) >= 14)->count(),
        ];

        $summary = [
            'total_tasks' => $tasks->count(),
            'open_tasks' => $activeTasks->count(),
            'completed_tasks' => $tasks->where('status', CrmTask::STATUS_COMPLETED)->count(),
            'delayed_tasks' => $delayedTasks->count(),
            'today_tasks' => $todayTasks->count(),
            'my_open_tasks' => $activeTasks->where('assigned_user_id', $viewer->id)->count(),
            'my_delayed_tasks' => $delayedTasks->where('assigned_user_id', $viewer->id)->count(),
            'urgent_open_tasks' => $activeTasks->where('priority', CrmTask::PRIORITY_URGENT)->count(),
        ];

        return [
            'filters' => $filters,
            'summary' => $summary,
            'employeePerformance' => $employeeRows,
            'statusCounts' => collect(CrmTask::statusOptions())->map(fn ($label, $status) => [
                'status' => $status,
                'label' => $label[app()->getLocale() === 'ar' ? 'ar' : 'en'],
                'count' => $tasks->where('status', $status)->count(),
            ])->values(),
            'priorityCounts' => collect(CrmTask::priorityOptions())->map(fn ($label, $priority) => [
                'priority' => $priority,
                'label' => $label[app()->getLocale() === 'ar' ? 'ar' : 'en'],
                'count' => $tasks->where('priority', $priority)->count(),
            ])->values(),
            'typeCounts' => collect(CrmTask::typeOptions())->map(fn ($label, $type) => [
                'type' => $type,
                'label' => $label[app()->getLocale() === 'ar' ? 'ar' : 'en'],
                'count' => $tasks->where('task_type', $type)->count(),
            ])->values(),
            'categoryCounts' => collect(CrmTask::categoryOptions())->map(fn ($label, $category) => [
                'category' => $category,
                'label' => $label[app()->getLocale() === 'ar' ? 'ar' : 'en'],
                'count' => $tasks->where('category', $category)->count(),
            ])->values(),
            'delayedItems' => $delayedTasks->sortBy('due_at')->values(),
            'todayItems' => $todayTasks->sortBy('due_at')->values(),
            'tomorrowItems' => $tomorrowTasks->sortBy('due_at')->values(),
            'upcomingItems' => $weekTasks
                ->filter(fn (CrmTask $task) => $task->due_at && $task->due_at->isFuture() && ! in_array($task->status, [CrmTask::STATUS_COMPLETED, CrmTask::STATUS_CANCELLED], true))
                ->sortBy('due_at')
                ->values(),
            'workloadRows' => $employeeRows->sortByDesc('open')->values(),
            'aging' => $aging,
            'leadDensity' => $leadDensity,
        ];
    }
}
