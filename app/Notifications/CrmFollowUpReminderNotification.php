<?php

namespace App\Notifications;

use App\Models\CrmFollowUp;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CrmFollowUpReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public CrmFollowUp $followUp)
    {
    }

    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if (! empty($this->getTargetEmails($notifiable))) {
            $channels[] = 'mail';
        }

        return $channels;
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

        $lead = $this->followUp->inquiry;
        $minutes = (int) $this->followUp->reminder_offset_minutes;
        $locale = $notifiable->preferred_language ?? app()->getLocale();
        $isAr = $locale === 'ar';

        $subject = $isAr
            ? "تذكير بمتابعة العميل: {$lead?->full_name}"
            : "Follow-up Reminder: {$lead?->full_name}";

        $greeting = $isAr ? "مرحباً {$notifiable->name}" : "Hello {$notifiable->name},";

        $line = $isAr
            ? "بعد {$minutes} دقيقة يجب التواصل مع العميل {$lead?->full_name}."
            : "Follow up with {$lead?->full_name} in {$minutes} minutes.";

        $targetEmails = $this->getTargetEmails($notifiable);
        $primaryEmail = array_shift($targetEmails);

        $mailMessage = (new MailMessage);

        if ($primaryEmail) {
            $mailMessage->to($primaryEmail);
        }

        if (! empty($targetEmails)) {
            $mailMessage->cc($targetEmails);
        }

        $mailMessage
            ->subject($subject)
            ->greeting($greeting)
            ->line($line);

        if ($lead?->phone) {
            $mailMessage->line(($isAr ? 'رقم الهاتف: ' : 'Phone: ') . $lead->phone);
        }

        if ($this->followUp->note) {
            $mailMessage->line(($isAr ? 'ملاحظة المتابعة: ' : 'Follow-up Note: ') . $this->followUp->note);
        }

        if ($lead) {
            $mailMessage->action($isAr ? 'عرض العميل في CRM' : 'View Lead in CRM', route('admin.crm.leads.show', $lead));
        }

        $mailMessage->line($isAr ? 'شكراً لاستخدامك Travel Wave.' : 'Thank you for using Travel Wave.');

        return $mailMessage;
    }

    public function toArray(object $notifiable): array
    {
        $lead = $this->followUp->inquiry;
        $minutes = (int) $this->followUp->reminder_offset_minutes;

        return [
            'type' => 'crm_follow_up_reminder',
            'title_ar' => "بعد {$minutes} دقيقة يجب التواصل مع العميل {$lead?->full_name}",
            'title_en' => "Follow up with {$lead?->full_name} in {$minutes} minutes",
            'lead_id' => $lead?->id,
            'lead_name' => $lead?->full_name,
            'phone' => $lead?->phone,
            'whatsapp_number' => $lead?->whatsapp_number,
            'scheduled_at' => optional($this->followUp->scheduled_at)->toIso8601String(),
            'status_reason_ar' => 'اتصل لاحقًا',
            'status_reason_en' => 'Call Later',
            'follow_up_id' => $this->followUp->id,
            'follow_up_note' => $this->followUp->note,
            'assigned_user_name' => $this->followUp->assignedUser?->name,
            'reminder_offset_minutes' => $minutes,
            'reminder_label' => $this->followUp->reminderLabel(),
            'url' => route('admin.crm.leads.show', $lead),
            'follow_up_update_url' => route('admin.crm.follow-ups.update', $this->followUp),
        ];
    }
}

