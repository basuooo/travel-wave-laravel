<?php

namespace App\Notifications;

use App\Models\CrmFollowUp;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CrmFollowUpReminderNotification extends Notification
{
    use Queueable;

    public function __construct(public CrmFollowUp $followUp)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
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
