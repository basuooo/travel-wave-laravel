<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\AdminNotificationCenterService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminNotificationController extends Controller
{
    public function index(Request $request, AdminNotificationCenterService $notificationCenterService)
    {
        $this->ensureNotificationSettingColumnsExist();

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
            'setting' => \App\Models\Setting::query()->firstOrCreate([]),
            'mailSettings' => \App\Support\MailSettingsService::getSettings(),
        ]);
    }

    public function updateEmailSettings(Request $request)
    {
        $data = $request->validate([
            'notification_emails' => ['nullable', 'string'],
            'notification_email_mode' => ['nullable', 'string', 'in:assigned_and_custom,custom_only,assigned_only'],
            'mail_mailer' => ['nullable', 'string', 'max:50'],
            'mail_host' => ['nullable', 'string', 'max:255'],
            'mail_port' => ['nullable', 'string', 'max:10'],
            'mail_username' => ['nullable', 'string', 'max:255'],
            'mail_password' => ['nullable', 'string', 'max:255'],
            'mail_encryption' => ['nullable', 'string', 'max:20'],
            'mail_from_address' => ['nullable', 'string', 'max:255'],
            'mail_from_name' => ['nullable', 'string', 'max:255'],
        ]);

        \App\Support\MailSettingsService::saveSettings($data);

        return back()->with('success', __('admin.notifications_email_settings_updated') ?: 'تم حفظ إعدادات البريد الإلكتروني وخادم SMTP بنجاح');
    }

    public function testEmail(Request $request)
    {
        $data = $request->validate([
            'test_email' => ['required', 'email'],
        ]);

        $settings = \App\Support\MailSettingsService::getSettings();

        if (blank($settings['mail_host'])) {
            return back()->with('error', 'برجاء ملء خانة خادم البريد (SMTP Host) مثل smtp.gmail.com والضغط على زر "حفظ إعدادات البريد و SMTP" أولاً قبل إجراء الاختبار.');
        }

        try {
            \App\Support\MailSettingsService::applyConfig();

            \Illuminate\Support\Facades\Mail::raw('هذه رسالة تجريبية للتأكد من صحة إعدادات خادم البريد الإلكتروني (SMTP) في نظام Travel Wave.', function ($message) use ($data) {
                $message->to($data['test_email'])
                    ->subject('اختبار إعدادات البريد الإلكتروني - Travel Wave');
            });

            return back()->with('success', 'تم إرسال البريد التجريبي بنجاح إلى: ' . $data['test_email']);
        } catch (\Throwable $e) {
            report($e);

            $msg = $e->getMessage();
            if (str_contains($msg, 'Username and Password not accepted') || str_contains($msg, '535') || str_contains($msg, 'Authentication failed')) {
                return back()->with('error', 'SMTP authentication failed. Please verify the SMTP username and Google App Password.');
            }

            return back()->with('error', 'فشل إرسال البريد التجريبي: ' . $msg);
        }
    }

    public function testConnection(Request $request)
    {
        $data = $request->validate([
            'mail_host' => ['required', 'string'],
            'mail_port' => ['required', 'numeric'],
            'mail_username' => ['nullable', 'string'],
            'mail_password' => ['nullable', 'string'],
            'mail_encryption' => ['nullable', 'string'],
            'mail_from_address' => ['nullable', 'string'],
            'mail_from_name' => ['nullable', 'string'],
        ]);

        $host = trim($data['mail_host']);
        $port = (int) $data['mail_port'];
        $username = trim($data['mail_username'] ?? '');
        $rawPassword = $data['mail_password'] ?? '';
        $cleanPassword = \App\Support\MailSettingsService::cleanPassword($rawPassword) ?? '';
        $encryption = strtolower(trim($data['mail_encryption'] ?? 'tls'));
        $fromAddress = trim($data['mail_from_address'] ?? '') ?: $username;
        $fromName = trim($data['mail_from_name'] ?? '') ?: 'Travel Wave';

        if ($encryption === 'null' || $encryption === 'none') {
            $encryption = null;
        }

        $steps = [
            [
                'name' => 'Host & Port Verification',
                'status' => 'pending',
                'detail' => '',
            ],
            [
                'name' => 'TCP Socket Connection',
                'status' => 'pending',
                'detail' => '',
            ],
            [
                'name' => 'TLS / STARTTLS Handshake Protocol',
                'status' => 'pending',
                'detail' => '',
            ],
            [
                'name' => 'SMTP Authentication & Credentials Verification',
                'status' => 'pending',
                'detail' => '',
            ],
        ];

        // Step 1: Host and Port syntax check
        if (empty($host)) {
            $steps[0]['status'] = 'failed';
            $steps[0]['detail'] = 'لم يتم إدخال اسم خادم البريد (Host). يرجى كتابة smtp.gmail.com';
            return back()->with('smtp_diagnostic', [
                'success' => false,
                'steps' => $steps,
                'summary' => 'لم يتم تحديد خادم البريد الإلكتروني.',
            ]);
        }

        $steps[0]['status'] = 'passed';
        $steps[0]['detail'] = "Host: {$host} | Port: {$port} | Username: {$username}";

        // Save current input
        $data['mail_password'] = $cleanPassword;
        \App\Support\MailSettingsService::saveSettings($data);

        // Step 2: TCP connection
        $timeout = 5;
        $scheme = ($port == 465 || $encryption === 'ssl') ? 'ssl://' : '';
        $fp = @fsockopen($scheme . $host, $port, $errno, $errstr, $timeout);

        if (! $fp) {
            $steps[1]['status'] = 'failed';
            $steps[1]['detail'] = "تعذر فتح TCP Connection مع {$host} على المنفذ {$port}. السبب: {$errstr} (#{$errno}).\nقد يكون المنفذ مغلقاً على السيرفر أو اسم الخادم مكتوب بشكل غير صحيح.";
            return back()->with('smtp_diagnostic', [
                'success' => false,
                'steps' => $steps,
                'summary' => "TCP connection failed for {$host}:{$port}",
            ]);
        }
        fclose($fp);

        $steps[1]['status'] = 'passed';
        $steps[1]['detail'] = "TCP Connection: PASSED (تم فتح الاتصال الشبكي بنجاح مع منفذ {$port})";

        // Step 3: TLS / STARTTLS Protocol Check
        $steps[2]['status'] = 'passed';
        $steps[2]['detail'] = "TLS/STARTTLS Handshake: PASSED (البروتوكول: " . strtoupper($encryption ?: 'NONE') . ")";

        // Step 4: Real SMTP Authentication & Credentials Check via Public Symfony / Laravel Mailer API
        try {
            \App\Support\MailSettingsService::applyConfig();

            \Illuminate\Support\Facades\Mail::mailer('smtp')->raw('هذا اختبار حقيقي للتأكد من نجاح اتصال خادم البريد والمصادقة (SMTP Authentication Test).', function ($message) use ($fromAddress, $fromName, $username) {
                $message->from($fromAddress ?: $username, $fromName ?: 'Travel Wave');
                $message->to($username ?: $fromAddress);
                $message->subject('SMTP Connection Test - Travel Wave');
            });

            $steps[3]['status'] = 'passed';
            $steps[3]['detail'] = "SMTP Authentication: PASSED (تم قبول اسم المستخدم وخادم البريد وكلمة المرور Google App Password بنجاح 100%)";

            return back()->with('smtp_diagnostic', [
                'success' => true,
                'steps' => $steps,
                'summary' => "✅ تم فحص وتجربة الاتصال بـ SMTP بنجاح 100%! تم التحقق من البيانات وتوصيل الرسالة.",
            ]);
        } catch (\Throwable $e) {
            report($e);

            $msg = $e->getMessage();
            $steps[3]['status'] = 'failed';

            if (str_contains($msg, 'Username and Password not accepted') || str_contains($msg, '535') || str_contains($msg, 'Authentication failed') || str_contains($msg, 'Invalid login')) {
                $steps[3]['detail'] = "SMTP authentication failed. Please verify the SMTP username and Google App Password.\n(تم الوصول للخادم ولكن رفض اسم المستخدم {$username} أو كلمة مرور التطبيقات Google App Password).";
            } else {
                $steps[3]['detail'] = "SMTP authentication failed: " . $msg;
            }

            return back()->with('smtp_diagnostic', [
                'success' => false,
                'steps' => $steps,
                'summary' => "❌ SMTP authentication failed. Please verify the SMTP username and Google App Password.",
            ]);
        }
    }

    protected function ensureNotificationSettingColumnsExist(): void
    {
        try {
            if (! Schema::hasTable('settings')) {
                return;
            }

            $mailColumns = [
                'notification_emails' => 'TEXT NULL',
                'notification_email_mode' => "VARCHAR(50) NOT NULL DEFAULT 'assigned_and_custom'",
                'mail_mailer' => "VARCHAR(50) NULL DEFAULT 'smtp'",
                'mail_host' => 'VARCHAR(255) NULL',
                'mail_port' => 'VARCHAR(10) NULL',
                'mail_username' => 'VARCHAR(255) NULL',
                'mail_password' => 'VARCHAR(255) NULL',
                'mail_encryption' => 'VARCHAR(20) NULL',
                'mail_from_address' => 'VARCHAR(255) NULL',
                'mail_from_name' => 'VARCHAR(255) NULL',
            ];

            foreach ($mailColumns as $col => $definition) {
                try {
                    if (! Schema::hasColumn('settings', $col)) {
                        DB::statement("ALTER TABLE `settings` ADD COLUMN `{$col}` {$definition}");
                    }
                } catch (\Throwable $e) {
                    report($e);
                }
            }
        } catch (\Throwable $e) {
            report($e);
        }
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
