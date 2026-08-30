<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminDatabaseNotification extends Notification
{
    use Queueable;

    public function __construct(protected array $payload)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    protected function isLeadNotification(): bool
    {
        $type = $this->payload['type'] ?? '';
        $notifiableType = $this->payload['notifiable_type'] ?? '';

        return str_starts_with($type, 'lead_') || $notifiableType === \App\Models\Inquiry::class;
    }

    public function getTargetEmails(object $notifiable): array
    {
        try {
            if (! \Illuminate\Support\Facades\Schema::hasTable('settings') || ! \Illuminate\Support\Facades\Schema::hasColumn('settings', 'notification_emails')) {
                return ! empty($notifiable->email) && filter_var($notifiable->email, FILTER_VALIDATE_EMAIL) ? [$notifiable->email] : [];
            }
        } catch (\Throwable $e) {
            return ! empty($notifiable->email) && filter_var($notifiable->email, FILTER_VALIDATE_EMAIL) ? [$notifiable->email] : [];
        }

        $setting = \App\Models\Setting::query()->first();
        $rawCustomEmails = $setting?->notification_emails;
        $mode = $setting?->notification_email_mode ?? 'assigned_and_custom';

        $customEmails = [];
        if (! empty($rawCustomEmails)) {
            $customEmails = preg_split('/[\s,;]+/', $rawCustomEmails);
            $customEmails = array_filter(array_map('trim', $customEmails), fn ($email) => filter_var($email, FILTER_VALIDATE_EMAIL));
        }

        $emails = [];

        if ($mode !== 'custom_only' && ! empty($notifiable->email) && filter_var($notifiable->email, FILTER_VALIDATE_EMAIL)) {
            $emails[] = $notifiable->email;
        }

        if ($mode !== 'assigned_only') {
            $emails = array_merge($emails, $customEmails);
        }

        return array_values(array_unique($emails));
    }

    public function toMail(object $notifiable): MailMessage
    {
        \App\Support\MailSettingsService::applyConfig();

        $locale = $notifiable->preferred_language ?? app()->getLocale();
        $isAr = $locale === 'ar';

        $title = $isAr
            ? ($this->payload['title_ar'] ?? $this->payload['title_en'] ?? __('admin.notifications_ui_type_system_alert', locale: 'ar'))
            : ($this->payload['title_en'] ?? $this->payload['title_ar'] ?? __('admin.notifications_ui_type_system_alert', locale: 'en'));

        $message = $isAr
            ? ($this->payload['message_ar'] ?? $this->payload['message_en'] ?? '')
            : ($this->payload['message_en'] ?? $this->payload['message_ar'] ?? '');

        $actionLabel = $isAr
            ? ($this->payload['action_label_ar'] ?? __('admin.notifications_ui_action_view', locale: 'ar'))
            : ($this->payload['action_label_en'] ?? __('admin.notifications_ui_action_view', locale: 'en'));

        $url = $this->payload['url'] ?? null;
        $subjectName = $this->payload['subject_name'] ?? $this->payload['lead_name'] ?? null;

        $targetEmails = $this->getTargetEmails($notifiable);
        $recipientEmail = $notifiable->routeNotificationFor('mail', $this) ?: ($notifiable->email ?? null);

        if ($recipientEmail) {
            $targetEmails = array_values(array_filter($targetEmails, fn ($e) => strtolower($e) !== strtolower($recipientEmail)));
        }

        $mailMessage = (new MailMessage);

        if (! empty($targetEmails)) {
            $mailMessage->cc($targetEmails);
        }

        $mailMessage
            ->subject($title)
            ->greeting($isAr ? "مرحباً {$notifiable->name}" : "Hello {$notifiable->name},")
            ->line($message);

        if ($subjectName && $subjectName !== $message) {
            $mailMessage->line(($isAr ? 'الموضوع: ' : 'Subject: ') . $subjectName);
        }

        if ($url) {
            $mailMessage->action($actionLabel, $url);
        }

        $mailMessage->line($isAr ? 'شكراً لاستخدامك Travel Wave.' : 'Thank you for using Travel Wave.');

        return $mailMessage;
    }

    public function toArray(object $notifiable): array
    {
        return $this->payload;
    }
}

