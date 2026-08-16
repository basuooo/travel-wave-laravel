<?php

namespace App\Observers;

use App\Models\CrmCustomer;
use App\Models\CrmTask;
use App\Models\Inquiry;
use App\Services\ZapierWebhookService;

class ZapierObserver
{
    /**
     * Handle the CrmCustomer "created" event.
     */
    public function customerCreated(CrmCustomer $customer): void
    {
        ZapierWebhookService::dispatchEvent('customer.created', [
            'id' => (string) $customer->id,
            'customer_code' => $customer->customer_code,
            'full_name' => $customer->full_name,
            'phone' => $customer->phone,
            'whatsapp_number' => $customer->whatsapp_number,
            'email' => $customer->email,
            'nationality' => $customer->nationality,
            'country' => $customer->country,
            'destination' => $customer->destination,
            'stage' => $customer->stage,
            'stage_localized' => $customer->localizedStage(),
            'notes' => $customer->notes,
            'created_at' => $customer->created_at?->toISOString(),
        ], $customer->created_by);
    }

    /**
     * Handle the CrmCustomer "updated" event.
     */
    public function customerUpdated(CrmCustomer $customer): void
    {
        if ($customer->wasChanged('stage')) {
            ZapierWebhookService::dispatchEvent('customer.stage_updated', [
                'id' => (string) $customer->id,
                'customer_code' => $customer->customer_code,
                'full_name' => $customer->full_name,
                'old_stage' => $customer->getOriginal('stage'),
                'new_stage' => $customer->stage,
                'new_stage_localized' => $customer->localizedStage(),
                'updated_at' => $customer->updated_at?->toISOString(),
            ], $customer->assigned_user_id);
        }
    }

    /**
     * Handle the Inquiry "created" event.
     */
    public function inquiryCreated(Inquiry $inquiry): void
    {
        ZapierWebhookService::dispatchEvent('inquiry.created', [
            'id' => (string) $inquiry->id,
            'full_name' => $inquiry->full_name,
            'phone' => $inquiry->phone,
            'whatsapp_number' => $inquiry->whatsapp_number,
            'email' => $inquiry->email,
            'country' => $inquiry->country,
            'destination' => $inquiry->destination,
            'service_type' => $inquiry->service_type,
            'travel_date' => $inquiry->travel_date,
            'travelers_count' => $inquiry->travelers_count,
            'message' => $inquiry->message,
            'lead_source' => $inquiry->lead_source,
            'status' => $inquiry->status,
            'created_at' => $inquiry->created_at?->toISOString(),
        ], $inquiry->assigned_user_id);
    }

    /**
     * Handle the CrmTask "created" event.
     */
    public function taskCreated(CrmTask $task): void
    {
        ZapierWebhookService::dispatchEvent('task.created', [
            'id' => (string) $task->id,
            'title' => $task->title,
            'description' => $task->description,
            'priority' => $task->priority,
            'status' => $task->status,
            'category' => $task->category,
            'task_type' => $task->task_type,
            'due_at' => $task->due_at?->toISOString(),
            'created_at' => $task->created_at?->toISOString(),
        ], $task->created_by);
    }
}
