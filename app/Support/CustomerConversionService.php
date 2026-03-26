<?php

namespace App\Support;

use App\Models\AccountingCustomerAccount;
use App\Models\CrmCustomer;
use App\Models\CrmCustomerActivity;
use App\Models\Inquiry;
use App\Models\User;
use App\Support\AuditLogService;
use App\Support\WorkflowAutomationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CustomerConversionService
{
    public function convertFromLead(Inquiry $lead, User $actor, array $overrides = []): CrmCustomer
    {
        if ($lead->crmCustomer()->exists()) {
            throw ValidationException::withMessages([
                'inquiry_id' => __('admin.customer_already_exists_for_lead'),
            ]);
        }

        return DB::transaction(function () use ($lead, $actor, $overrides) {
            $lead->loadMissing(['crmSource', 'crmServiceType', 'crmServiceSubtype', 'assignedUser', 'accountingAccount']);

            $customer = CrmCustomer::query()->create([
                'inquiry_id' => $lead->id,
                'full_name' => $overrides['full_name'] ?? $lead->full_name,
                'phone' => $overrides['phone'] ?? $lead->phone,
                'whatsapp_number' => $overrides['whatsapp_number'] ?? $lead->whatsapp_number,
                'email' => $overrides['email'] ?? $lead->email,
                'nationality' => $overrides['nationality'] ?? $lead->nationality,
                'country' => $overrides['country'] ?? $lead->country,
                'destination' => $overrides['destination'] ?? $lead->serviceDestinationValue(),
                'crm_source_id' => $lead->crm_source_id,
                'crm_service_type_id' => $lead->crm_service_type_id,
                'crm_service_subtype_id' => $lead->crm_service_subtype_id,
                'assigned_user_id' => $overrides['assigned_user_id'] ?? $lead->assigned_user_id,
                'created_by' => $actor->id,
                'stage' => $overrides['stage'] ?? CrmCustomer::STAGE_NEW,
                'is_active' => $overrides['is_active'] ?? true,
                'converted_at' => now(),
                'appointment_at' => $overrides['appointment_at'] ?? null,
                'submission_at' => $overrides['submission_at'] ?? null,
                'notes' => $overrides['notes'] ?? $lead->admin_notes,
            ]);

            $customer->forceFill([
                'customer_code' => $this->generateCustomerCode($customer),
            ])->save();

            CrmCustomerActivity::query()->create([
                'crm_customer_id' => $customer->id,
                'user_id' => $actor->id,
                'action_type' => 'converted_from_lead',
                'old_value' => null,
                'new_value' => $customer->stage,
                'note' => $lead->full_name,
            ]);

            app(AuditLogService::class)->log(
                $actor,
                'customers',
                'converted_to_customer',
                $customer,
                [
                    'title' => __('admin.audit_action_converted_to_customer'),
                    'description' => $lead->full_name,
                    'old_values' => [
                        'lead_id' => $lead->id,
                        'lead_name' => $lead->full_name,
                    ],
                    'new_values' => [
                        'customer_code' => $customer->customer_code,
                        'stage' => $customer->localizedStage(),
                    ],
                    'changed_fields' => ['lead_id', 'customer_code', 'stage'],
                ]
            );

            $account = $lead->accountingAccount;
            if ($account instanceof AccountingCustomerAccount) {
                $account->forceFill([
                    'crm_customer_id' => $customer->id,
                    'assigned_user_id' => $customer->assigned_user_id,
                    'customer_name' => $customer->full_name,
                    'phone' => $customer->phone,
                    'whatsapp_number' => $customer->whatsapp_number,
                    'email' => $customer->email,
                ])->save();
            }

            app(WorkflowAutomationService::class)->dispatch(WorkflowAutomationService::TRIGGER_CUSTOMER_CREATED, $customer->fresh(['inquiry', 'assignedUser']), [
                'actor' => $actor,
                'inquiry' => $lead,
            ]);

            return $customer->fresh(['inquiry', 'assignedUser', 'crmSource', 'crmServiceType', 'crmServiceSubtype']);
        });
    }

    public function syncCustomerAccountLink(CrmCustomer $customer): void
    {
        $account = $customer->inquiry?->accountingAccount;

        if (! $account) {
            return;
        }

        $account->forceFill([
            'crm_customer_id' => $customer->id,
            'assigned_user_id' => $customer->assigned_user_id,
            'customer_name' => $customer->full_name,
            'phone' => $customer->phone,
            'whatsapp_number' => $customer->whatsapp_number,
            'email' => $customer->email,
        ])->save();
    }

    protected function generateCustomerCode(CrmCustomer $customer): string
    {
        return sprintf('CUST-%s-%04d', now()->format('Y'), $customer->id);
    }
}
