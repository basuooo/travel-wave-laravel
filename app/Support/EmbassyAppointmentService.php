<?php

namespace App\Support;

use App\Models\CrmLeadNote;
use App\Models\CrmStatus;
use App\Models\EmbassyAppointment;
use App\Models\EmbassyAppointmentLog;
use App\Models\EmbassyAppointmentNotification;
use App\Models\EmbassyAvailabilityEvent;
use App\Models\Inquiry;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class EmbassyAppointmentService
{
    public function updateStatus(
        EmbassyAppointment $appointment,
        string $newStatus,
        ?string $earliestDate = null,
        ?string $notes = null,
        ?User $actor = null
    ): EmbassyAppointment {
        $oldStatus = $appointment->status;
        $oldEarliestDate = is_string($appointment->earliest_date)
            ? $appointment->earliest_date
            : ($appointment->earliest_date ? (string) $appointment->earliest_date : null);

        $appointment->status = $newStatus;
        $appointment->earliest_date = $earliestDate;
        $appointment->last_updated_at = now();
        $appointment->updated_by = $actor?->id;
        if ($notes !== null) {
            $appointment->notes = $notes;
        }
        $appointment->save();

        // Create log entry
        EmbassyAppointmentLog::create([
            'embassy_appointment_id' => $appointment->id,
            'user_id' => $actor?->id,
            'user_name' => $actor?->name ?? 'النظام',
            'action' => $oldStatus !== $newStatus ? 'status_change' : 'update',
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'old_earliest_date' => $oldEarliestDate,
            'new_earliest_date' => $appointment->earliest_date,
            'notes' => $notes,
        ]);

        // Trigger Event & Notifications if status is available_now
        if ($newStatus === EmbassyAppointment::STATUS_AVAILABLE_NOW) {
            $event = EmbassyAvailabilityEvent::firstOrCreate([
                'embassy_appointment_id' => $appointment->id,
                'status' => 'active',
            ], [
                'triggered_by' => $actor?->id,
                'notes' => $notes,
            ]);

            $this->matchAndNotifyLeads($event);
        }

        return $appointment;
    }

    public function syncAllAvailableNowAppointments(): int
    {
        $availableNowAppointments = EmbassyAppointment::where('status', EmbassyAppointment::STATUS_AVAILABLE_NOW)->get();
        $totalMatched = 0;

        foreach ($availableNowAppointments as $appointment) {
            $event = EmbassyAvailabilityEvent::firstOrCreate([
                'embassy_appointment_id' => $appointment->id,
                'status' => 'active',
            ], [
                'triggered_by' => auth()->id(),
                'notes' => 'مواصفة تلقائية للمواعيد المتاحة',
            ]);

            $totalMatched += $this->matchAndNotifyLeads($event);
        }

        return $totalMatched;
    }

    public static function targetCrmStatusSlugs(): array
    {
        return [
            'awaiting-embassy-appointment',
            'whatsapp-follow-up',
            'awaiting-documents',
            'missing-documents',
            'documents-complete',
            'documents-complete-weak',
            'documents-needs-followup',
            'complete-lead',
        ];
    }

    public static function targetCrmStatusNames(): array
    {
        return [
            'انتظار فتح مواعيد السفارة',
            'متابعة واتساب',
            'بانتظار المستندات',
            'الأوراق مكتملة',
            'الاوراق مكتملة',
            'الأوراق مكتملة (ضعيفة)',
            'الاوراق مكتملة (ضعيفة)',
            'الأوراق مكملة',
            'أوراق ناقصة مستندات',
        ];
    }

    public function applyTargetCrmStatusFilter(Builder $query): Builder
    {
        $slugs = static::targetCrmStatusSlugs();
        $names = static::targetCrmStatusNames();

        $statusIds = CrmStatus::query()
            ->whereIn('slug', $slugs)
            ->orWhere(function ($q) use ($names) {
                foreach ($names as $name) {
                    $q->orWhere('name_ar', 'like', "%{$name}%");
                }
            })
            ->pluck('id')
            ->toArray();

        return $query->where(function (Builder $q) use ($statusIds, $slugs, $names) {
            if (! empty($statusIds)) {
                $q->whereIn('crm_status_id', $statusIds)
                  ->orWhereIn('crm_status2_id', $statusIds);
            }

            foreach ($slugs as $slug) {
                $q->orWhere('status', $slug);
            }

            foreach ($names as $name) {
                $q->orWhere('status', 'like', "%{$name}%");
            }

            $q->orWhereHas('crmStatus', function ($s) use ($slugs, $names) {
                $s->whereIn('slug', $slugs)
                  ->orWhere(function ($subQ) use ($names) {
                      foreach ($names as $name) {
                          $subQ->orWhere('name_ar', 'like', "%{$name}%");
                      }
                  });
            });

            $q->orWhereHas('crmStatus2', function ($s) use ($slugs, $names) {
                $s->whereIn('slug', $slugs)
                  ->orWhere(function ($subQ) use ($names) {
                      foreach ($names as $name) {
                          $subQ->orWhere('name_ar', 'like', "%{$name}%");
                      }
                  });
            });
        })
        ->whereDoesntHave('crmStatus', function (Builder $q) {
            $q->whereIn('slug', ['closed', 'cancelled', 'duplicate', 'not-interested', 'no-answer', 'wrong-number']);
        });
    }

    public function matchAndNotifyLeads(EmbassyAvailabilityEvent $event): int
    {
        $appointment = $event->appointment()->with('country')->first();
        if (! $appointment) {
            return 0;
        }

        $countryNameAr = $appointment->country?->name_ar;
        $countryNameEn = $appointment->country?->name_en;

        $query = Inquiry::query();

        // Filter strictly by the 5 target CRM Lead statuses
        $this->applyTargetCrmStatusFilter($query);

        // Filter by country
        $query->where(function (Builder $q) use ($appointment, $countryNameAr, $countryNameEn) {
            $q->where('visa_country_id', $appointment->visa_country_id);

            if ($countryNameAr) {
                $cleanAr = preg_replace('/[أإآ]/u', 'ا', $countryNameAr);
                $q->orWhere('country', 'like', '%' . $countryNameAr . '%')
                  ->orWhere('country', 'like', '%' . $cleanAr . '%')
                  ->orWhere('destination', 'like', '%' . $countryNameAr . '%')
                  ->orWhere('destination', 'like', '%' . $cleanAr . '%')
                  ->orWhere('service_country_name', 'like', '%' . $countryNameAr . '%')
                  ->orWhere('service_country_name', 'like', '%' . $cleanAr . '%');
            }

            if ($countryNameEn) {
                $q->orWhere('country', 'like', '%' . $countryNameEn . '%')
                  ->orWhere('destination', 'like', '%' . $countryNameEn . '%')
                  ->orWhere('service_country_name', 'like', '%' . $countryNameEn . '%');
            }

            $q->orWhereHas('visaCountry', function ($vc) use ($appointment, $countryNameAr, $countryNameEn) {
                $vc->where('id', $appointment->visa_country_id);
                if ($countryNameAr) {
                    $cleanAr = preg_replace('/[أإآ]/u', 'ا', $countryNameAr);
                    $vc->orWhere('name_ar', 'like', '%' . $countryNameAr . '%')
                       ->orWhere('name_ar', 'like', '%' . $cleanAr . '%');
                }
                if ($countryNameEn) {
                    $vc->orWhere('name_en', 'like', '%' . $countryNameEn . '%');
                }
            });
        });

        // Filter by center if specified on lead
        if (filled($appointment->appointment_center)) {
            $query->where(function (Builder $q) use ($appointment) {
                $q->whereNull('appointment_center')
                  ->orWhere('appointment_center', '')
                  ->orWhere('appointment_center', 'like', '%' . $appointment->appointment_center . '%');
            });
        }

        // Filter by appointment_type if specified on lead
        if (filled($appointment->appointment_type)) {
            $query->where(function (Builder $q) use ($appointment) {
                $q->whereNull('appointment_type')
                  ->orWhere('appointment_type', '')
                  ->orWhere('appointment_type', 'like', '%' . $appointment->appointment_type . '%');
            });
        }

        $matchingLeads = $query->get();
        $notificationCenter = app(AdminNotificationCenterService::class);
        $count = 0;

        foreach ($matchingLeads as $lead) {
            $sellerId = $lead->assigned_user_id ?: (auth()->id() ?: 1);

            $notif = EmbassyAppointmentNotification::firstOrCreate([
                'embassy_availability_event_id' => $event->id,
                'inquiry_id' => $lead->id,
            ], [
                'embassy_appointment_id' => $appointment->id,
                'seller_id' => $sellerId,
                'status' => EmbassyAppointmentNotification::STATUS_PENDING,
            ]);

            if ($sellerId) {
                $seller = User::find($sellerId);
                if ($seller) {
                    $notificationCenter->notifyUser($seller, [
                        'event_key' => sprintf('embassy_appt_open:%d:%d:%d', $event->id, $lead->id, $sellerId),
                        'type' => 'embassy_appointment_opened',
                        'module' => 'crm',
                        'severity' => AdminNotificationCenterService::SEVERITY_SUCCESS,
                        'title_ar' => '🔔 مواعيد السفارة متاحة الآن: ' . $appointment->country_name,
                        'title_en' => '🔔 Embassy Appointments Available: ' . $appointment->country_name,
                        'message_ar' => sprintf(
                            'مواعيد السفارة متاحة الآن للعميل %s (%s - %s - %s - %s)',
                            $lead->full_name,
                            $appointment->country_name,
                            $appointment->visa_type,
                            $appointment->appointment_center,
                            $appointment->appointment_type
                        ),
                        'message_en' => sprintf(
                            'Embassy appointments available for lead %s (%s - %s - %s - %s)',
                            $lead->full_name,
                            $appointment->country_name,
                            $appointment->visa_type,
                            $appointment->appointment_center,
                            $appointment->appointment_type
                        ),
                        'action_url' => route('admin.crm.leads.show', $lead->id),
                    ]);
                }
            }
            $count++;
        }

        return $count;
    }

    public function getSellerPendingNotifications(User $seller): Collection
    {
        return EmbassyAppointmentNotification::query()
            ->with(['appointment.country', 'lead', 'event'])
            ->where(function (Builder $q) use ($seller) {
                $q->where('seller_id', $seller->id)
                  ->orWhereNull('seller_id')
                  ->orWhere('seller_id', 0);
            })
            ->whereIn('status', [
                EmbassyAppointmentNotification::STATUS_PENDING,
                EmbassyAppointmentNotification::STATUS_NOTIFIED,
                EmbassyAppointmentNotification::STATUS_SNOOZED,
            ])
            ->where(function (Builder $q) {
                $q->whereNull('snoozed_until')
                  ->orWhere('snoozed_until', '<=', now());
            })
            ->whereHas('appointment', fn ($q) => $q->where('status', EmbassyAppointment::STATUS_AVAILABLE_NOW))
            ->latest()
            ->get();
    }

    public function markContacted(
        EmbassyAppointmentNotification $notification,
        string $callResult,
        ?string $notes = null,
        ?User $actor = null
    ): EmbassyAppointmentNotification {
        $notification->status = EmbassyAppointmentNotification::STATUS_CONTACTED;
        $notification->contacted_at = now();
        $notification->contact_result = $callResult;
        $notification->contact_notes = $notes;
        $notification->save();

        if ($notification->inquiry_id) {
            CrmLeadNote::create([
                'inquiry_id' => $notification->inquiry_id,
                'user_id' => $actor?->id ?? $notification->seller_id,
                'body' => sprintf(
                    'تم الاتصال بالعميل بشأن مواعيد سفارة %s (%s - %s - %s) - النتيجة: %s%s',
                    $notification->appointment?->country_name ?? '',
                    $notification->appointment?->visa_type ?? '',
                    $notification->appointment?->appointment_center ?? '',
                    $notification->appointment?->appointment_type ?? '',
                    $notification->contact_result_label,
                    $notes ? " | ملاحظات: {$notes}" : ''
                ),
            ]);
        }

        return $notification;
    }

    public function snooze(
        EmbassyAppointmentNotification $notification,
        string $durationOption,
        ?string $customTime = null
    ): EmbassyAppointmentNotification {
        $now = now();
        $snoozedUntil = match ($durationOption) {
            '15' => $now->copy()->addMinutes(15),
            '30' => $now->copy()->addMinutes(30),
            '60' => $now->copy()->addHour(),
            '120' => $now->copy()->addHours(2),
            'tomorrow' => $now->copy()->addDay()->startOfDay()->addHours(9),
            'custom' => $customTime ? Carbon::parse($customTime) : $now->copy()->addHour(),
            default => $now->copy()->addMinutes((int) $durationOption ?: 30),
        };

        $notification->status = EmbassyAppointmentNotification::STATUS_SNOOZED;
        $notification->snoozed_until = $snoozedUntil;
        $notification->save();

        return $notification;
    }

    public function countWaitingLeads(EmbassyAppointment $appointment): int
    {
        $countryNameAr = $appointment->country?->name_ar;
        $countryNameEn = $appointment->country?->name_en;

        $query = Inquiry::query();

        // Filter strictly by the 5 target CRM Lead statuses
        $this->applyTargetCrmStatusFilter($query);

        // Filter by country
        $query->where(function (Builder $q) use ($appointment, $countryNameAr, $countryNameEn) {
            $q->where('visa_country_id', $appointment->visa_country_id);

            if ($countryNameAr) {
                $cleanAr = preg_replace('/[أإآ]/u', 'ا', $countryNameAr);
                $q->orWhere('country', 'like', '%' . $countryNameAr . '%')
                  ->orWhere('country', 'like', '%' . $cleanAr . '%')
                  ->orWhere('destination', 'like', '%' . $countryNameAr . '%')
                  ->orWhere('destination', 'like', '%' . $cleanAr . '%')
                  ->orWhere('service_country_name', 'like', '%' . $countryNameAr . '%')
                  ->orWhere('service_country_name', 'like', '%' . $cleanAr . '%');
            }

            if ($countryNameEn) {
                $q->orWhere('country', 'like', '%' . $countryNameEn . '%')
                  ->orWhere('destination', 'like', '%' . $countryNameEn . '%')
                  ->orWhere('service_country_name', 'like', '%' . $countryNameEn . '%');
            }

            $q->orWhereHas('visaCountry', function ($vc) use ($appointment, $countryNameAr, $countryNameEn) {
                $vc->where('id', $appointment->visa_country_id);
                if ($countryNameAr) {
                    $cleanAr = preg_replace('/[أإآ]/u', 'ا', $countryNameAr);
                    $vc->orWhere('name_ar', 'like', '%' . $countryNameAr . '%')
                       ->orWhere('name_ar', 'like', '%' . $cleanAr . '%');
                }
                if ($countryNameEn) {
                    $vc->orWhere('name_en', 'like', '%' . $countryNameEn . '%');
                }
            });
        });

        if (filled($appointment->appointment_center)) {
            $query->where(function (Builder $q) use ($appointment) {
                $q->whereNull('appointment_center')
                  ->orWhere('appointment_center', '')
                  ->orWhere('appointment_center', 'like', '%' . $appointment->appointment_center . '%');
            });
        }

        if (filled($appointment->appointment_type)) {
            $query->where(function (Builder $q) use ($appointment) {
                $q->whereNull('appointment_type')
                  ->orWhere('appointment_type', '')
                  ->orWhere('appointment_type', 'like', '%' . $appointment->appointment_type . '%');
            });
        }

        return $query->count();
    }
}
