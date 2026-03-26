<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\AdminNotificationCenterService;
use Illuminate\Http\Request;

class AdminNotificationController extends Controller
{
    public function index(Request $request, AdminNotificationCenterService $notificationCenterService)
    {
        $data = $request->validate([
            'state' => ['nullable', 'in:all,unread,read'],
            'type' => ['nullable', 'string', 'max:100'],
            'severity' => ['nullable', 'in:info,success,warning,danger'],
            'module' => ['nullable', 'string', 'max:100'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
            'actionable' => ['nullable', 'in:0,1'],
        ]);

        $query = $request->user()->notifications()->latest();

        if (($data['state'] ?? 'all') === 'unread') {
            $query->whereNull('read_at');
        } elseif (($data['state'] ?? 'all') === 'read') {
            $query->whereNotNull('read_at');
        }

        foreach (['type', 'severity', 'module'] as $field) {
            if (! empty($data[$field])) {
                if ($field === 'type' && $data[$field] === 'lead_followup_due') {
                    $query->where(function ($builder) {
                        $builder->where('data->type', 'crm_follow_up_reminder')
                            ->orWhere('data->type', 'lead_followup_due')
                            ->orWhere('type', \App\Notifications\CrmFollowUpReminderNotification::class);
                    });
                    continue;
                }

                $query->where('data->' . $field, $data[$field]);
            }
        }

        if (! empty($data['from'])) {
            $query->whereDate('created_at', '>=', $request->date('from'));
        }

        if (! empty($data['to'])) {
            $query->whereDate('created_at', '<=', $request->date('to'));
        }

        if (($data['actionable'] ?? null) === '1') {
            $query->whereNotNull('data->url');
        }

        $items = $notificationCenterService->presentPaginator(
            $query->paginate(20)->withQueryString()
        );

        $allNotifications = $request->user()->notifications();

        return view('admin.notifications.index', [
            'items' => $items,
            'filters' => [
                'state' => $data['state'] ?? 'all',
                'type' => $data['type'] ?? null,
                'severity' => $data['severity'] ?? null,
                'module' => $data['module'] ?? null,
                'from' => $data['from'] ?? null,
                'to' => $data['to'] ?? null,
                'actionable' => $data['actionable'] ?? null,
            ],
            'summary' => [
                'total' => (clone $allNotifications)->count(),
                'unread' => (clone $allNotifications)->whereNull('read_at')->count(),
                'urgent_unread' => (clone $allNotifications)->whereNull('read_at')->where('data->severity', 'danger')->count(),
                'actionable_unread' => (clone $allNotifications)->whereNull('read_at')->whereNotNull('data->url')->count(),
            ],
            'typeOptions' => [
                'task_assigned',
                'task_reassigned',
                'task_due',
                'task_delayed',
                'task_completed',
                'lead_assigned',
                'lead_reassigned',
                'lead_delayed',
                'lead_followup_due',
                'information_new',
                'information_ack_required',
                'accounting_payment',
            ],
            'severityOptions' => ['info', 'success', 'warning', 'danger'],
            'moduleOptions' => ['tasks', 'crm', 'information', 'accounting', 'system'],
            'notificationCenterService' => $notificationCenterService,
        ]);
    }

    public function read(Request $request, string $notification)
    {
        $item = $request->user()
            ->notifications()
            ->whereKey($notification)
            ->firstOrFail();

        if (is_null($item->read_at)) {
            $item->markAsRead();
        }

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'ok',
                'unread_count' => $request->user()->unreadNotifications()->count(),
            ]);
        }

        return back()->with('success', __('admin.notifications_ui_marked_read_success'));
    }

    public function readAll(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'ok',
                'unread_count' => 0,
            ]);
        }

        return back()->with('success', __('admin.notifications_ui_marked_all_read_success'));
    }
}
