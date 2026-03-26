<?php

namespace App\Support;

use App\Models\Inquiry;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CrmDelayedLeadService
{
    public const INACTIVE_DAYS = 5;

    public function applyDelayedScope(Builder $query): Builder
    {
        $threshold = now()->subDays(self::INACTIVE_DAYS)->format('Y-m-d H:i:s');
        $lastActionSql = $this->lastActionSql();
        $overdueFollowUpSql = $this->overdueFollowUpSql();

        return $query
            ->select('inquiries.*')
            ->selectRaw($lastActionSql . ' as delay_last_action_at')
            ->selectRaw($overdueFollowUpSql . ' as delay_overdue_follow_up_at')
            ->where(function (Builder $builder) use ($lastActionSql, $overdueFollowUpSql, $threshold) {
                $builder
                    ->whereRaw($overdueFollowUpSql . ' IS NOT NULL AND ' . $lastActionSql . ' <= ' . $overdueFollowUpSql)
                    ->orWhereRaw($lastActionSql . ' <= ?', [$threshold]);
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
        $threshold = now()->subDays(self::INACTIVE_DAYS);

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
                'reason' => 'لم يتم اتخاذ أي إجراء منذ 5 أيام',
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

    protected function lastActionSql(): string
    {
        return $this->rowGreatestSql([
            "COALESCE(inquiries.updated_at, '1970-01-01 00:00:00')",
            "COALESCE(inquiries.crm_status_updated_at, '1970-01-01 00:00:00')",
            "COALESCE((SELECT MAX(created_at) FROM crm_lead_notes WHERE crm_lead_notes.inquiry_id = inquiries.id), '1970-01-01 00:00:00')",
            "COALESCE((SELECT MAX(changed_at) FROM crm_status_updates WHERE crm_status_updates.inquiry_id = inquiries.id), '1970-01-01 00:00:00')",
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
