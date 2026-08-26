<?php

namespace App\Support;

use App\Models\Inquiry;
use App\Models\CrmStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CrmDelayedLeadService
{
    public const INACTIVE_HOURS = 48;

    public function applyDelayedScope(Builder $query): Builder
    {
        $threshold = now()->subHours(self::INACTIVE_HOURS)->format('Y-m-d H:i:s');
        $lastActionSql = $this->lastActionSql();
        $overdueFollowUpSql = $this->overdueFollowUpSql();

        $targetStatusIds = $this->resolveTargetStatusIds();

        return $query
            ->select('inquiries.*')
            ->selectRaw($lastActionSql . ' as delay_last_action_at')
            ->selectRaw($overdueFollowUpSql . ' as delay_overdue_follow_up_at')
            ->where(function (Builder $builder) use ($lastActionSql, $overdueFollowUpSql, $threshold, $targetStatusIds) {
                // Overdue scheduled follow-up
                $builder->whereRaw($overdueFollowUpSql . ' IS NOT NULL AND ' . $lastActionSql . ' <= ' . $overdueFollowUpSql)
                    // Or no action in last 48 hours for target statuses (or null status/new)
                    ->orWhere(function (Builder $subQuery) use ($lastActionSql, $threshold, $targetStatusIds) {
                        $subQuery->whereRaw($lastActionSql . ' <= ?', [$threshold])
                            ->where(function (Builder $stQuery) use ($targetStatusIds) {
                                $stQuery->whereNull('inquiries.crm_status_id');
                                if (!empty($targetStatusIds)) {
                                    $stQuery->orWhereIn('inquiries.crm_status_id', $targetStatusIds);
                                }
                            });
                    });
            })
            ->orderByRaw('CASE WHEN ' . $overdueFollowUpSql . ' IS NULL THEN 1 ELSE 0 END')
            ->orderByRaw($overdueFollowUpSql . ' ASC')
            ->orderByRaw($lastActionSql . ' ASC');
    }

    public function annotate(Collection $leads): Collection
    {
        return $leads->map(function (Inquiry $lead) {
            $meta = $this->delayMeta($lead);

            $lead->setAttribute('delay_reason', $meta['reason']);
            $lead->setAttribute('delay_reason_type', $meta['type']);
            $lead->setAttribute('delay_last_action_at', $meta['last_action_at']?->toDateTimeString());
            $lead->setAttribute('delay_reference_at', $meta['reference_at']?->toDateTimeString());

            return $lead;
        });
    }

    public function delayMeta(Inquiry $lead): array
    {
        $lastActionAt = $this->resolveLeadTimestamp($lead->getAttribute('delay_last_action_at'))
            ?? $this->fallbackLastActionAt($lead);
        $overdueFollowUpAt = $this->resolveLeadTimestamp($lead->getAttribute('delay_overdue_follow_up_at'));
        $statusChangedAt = $lead->crm_status_updated_at ? Carbon::parse($lead->crm_status_updated_at) : null;
        $threshold = now()->subHours(self::INACTIVE_HOURS);

        if ($overdueFollowUpAt && (! $lastActionAt || $lastActionAt->lessThanOrEqualTo($overdueFollowUpAt))) {
            $reason = (! $statusChangedAt || $statusChangedAt->lessThanOrEqualTo($overdueFollowUpAt))
                ? 'تمت الجدولة ولم يتم تغيير الحالة'
                : 'تجاوز موعد المتابعة المحدد';

            return [
                'type' => 'overdue_follow_up',
                'reason' => $reason,
                'last_action_at' => $lastActionAt,
                'reference_at' => $overdueFollowUpAt,
            ];
        }

        if ($lastActionAt && $lastActionAt->lessThanOrEqualTo($threshold)) {
            return [
                'type' => 'inactive',
                'reason' => 'لم يتم اتخاذ أي إجراء منذ 48 ساعة',
                'last_action_at' => $lastActionAt,
                'reference_at' => $lastActionAt,
            ];
        }

        return [
            'type' => null,
            'reason' => null,
            'last_action_at' => $lastActionAt,
            'reference_at' => $overdueFollowUpAt,
        ];
    }

    protected function resolveTargetStatusIds(): array
    {
        if (!Schema::hasTable('crm_statuses')) {
            return [];
        }

        $targetKeywords = ['جديد', 'new', 'لم يتم الرد', 'مشغول', 'غير متاح', 'بيكنسل', 'مغلق', 'no-answer', 'busy', 'unavailable'];

        return CrmStatus::query()
            ->get()
            ->filter(function ($status) use ($targetKeywords) {
                $name = mb_strtolower(($status->name_ar ?: '') . ' ' . ($status->name_en ?: '') . ' ' . ($status->slug ?: ''));
                foreach ($targetKeywords as $kw) {
                    if (str_contains($name, mb_strtolower($kw))) {
                        return true;
                    }
                }
                return false;
            })
            ->pluck('id')
            ->toArray();
    }

    protected function lastActionSql(): string
    {
        $hasCallsTable = Schema::hasTable('crm_lead_calls');
        $hasWhatsAppsTable = Schema::hasTable('crm_lead_whatsapps');

        $callsSql = $hasCallsTable
            ? "COALESCE((SELECT MAX(created_at) FROM crm_lead_calls WHERE crm_lead_calls.inquiry_id = inquiries.id), '1970-01-01 00:00:00')"
            : "'1970-01-01 00:00:00'";

        $whatsAppsSql = $hasWhatsAppsTable
            ? "COALESCE((SELECT MAX(created_at) FROM crm_lead_whatsapps WHERE crm_lead_whatsapps.inquiry_id = inquiries.id), '1970-01-01 00:00:00')"
            : "'1970-01-01 00:00:00'";

        return $this->rowGreatestSql([
            "COALESCE(inquiries.updated_at, '1970-01-01 00:00:00')",
            "COALESCE(inquiries.crm_status_updated_at, '1970-01-01 00:00:00')",
            "COALESCE((SELECT MAX(created_at) FROM crm_lead_notes WHERE crm_lead_notes.inquiry_id = inquiries.id), '1970-01-01 00:00:00')",
            "COALESCE((SELECT MAX(changed_at) FROM crm_status_updates WHERE crm_status_updates.inquiry_id = inquiries.id), '1970-01-01 00:00:00')",
            $callsSql,
            $whatsAppsSql,
            "COALESCE((" . $this->taskLastActionSql() . "), '1970-01-01 00:00:00')",
        ]);
    }

    protected function overdueFollowUpSql(): string
    {
        $now = now()->format('Y-m-d H:i:s');

        return "(SELECT MIN(scheduled_at) FROM crm_follow_ups
            WHERE crm_follow_ups.inquiry_id = inquiries.id
              AND crm_follow_ups.status = 'pending'
              AND crm_follow_ups.scheduled_at < '{$now}')";
    }

    protected function taskLastActionSql(): string
    {
        $rowWiseTaskTimestamp = $this->rowGreatestSql([
            "COALESCE(completed_at, '1970-01-01 00:00:00')",
            "COALESCE(due_at, '1970-01-01 00:00:00')",
            "COALESCE(updated_at, '1970-01-01 00:00:00')",
            "COALESCE(created_at, '1970-01-01 00:00:00')",
        ]);

        return "SELECT MAX({$rowWiseTaskTimestamp}) FROM crm_tasks WHERE crm_tasks.inquiry_id = inquiries.id";
    }

    protected function rowGreatestSql(array $expressions): string
    {
        $function = DB::connection()->getDriverName() === 'sqlite' ? 'max' : 'GREATEST';

        return $function . '(' . implode(",\n            ", $expressions) . ')';
    }

    protected function resolveLeadTimestamp(mixed $value): ?Carbon
    {
        if (blank($value)) {
            return null;
        }

        return $value instanceof Carbon ? $value : Carbon::parse($value);
    }

    protected function fallbackLastActionAt(Inquiry $lead): ?Carbon
    {
        $timestamps = collect([
            $lead->updated_at,
            $lead->crm_status_updated_at,
            $lead->crmNotes->max('created_at') ?? null,
            $lead->crmStatusUpdates->max('changed_at') ?? null,
            $lead->calls->max('created_at') ?? null,
            $lead->whatsappLogs->max('created_at') ?? null,
            $lead->crmTasks->max(function ($task) {
                return $task->completed_at ?? $task->due_at ?? $task->updated_at ?? $task->created_at;
            }) ?? null,
        ])->filter();

        if ($timestamps->isEmpty()) {
            return null;
        }

        return $timestamps
            ->map(fn ($timestamp) => $timestamp instanceof Carbon ? $timestamp : Carbon::parse($timestamp))
            ->sortDesc()
            ->first();
    }
}
