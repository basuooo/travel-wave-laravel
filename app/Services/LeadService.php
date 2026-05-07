<?php

namespace App\Services;

use App\Events\LeadReceived;
use App\Models\CrmIntegration;
use App\Models\CrmLead;
use App\Models\Inquiry;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LeadService
{
    /**
     * Process a normalized lead.
     */
    public function processLead(array $data, CrmIntegration $integration)
    {
        return DB::transaction(function () use ($data, $integration) {
            // 1. Check for duplicates (Phone or Email)
            $existingLead = CrmLead::where('phone', $data['phone'])
                ->orWhere('email', $data['email'])
                ->first();

            if ($existingLead) {
                $data['status'] = 'duplicate';
            }

            // 2. Create the CrmLead record
            $lead = CrmLead::create(array_merge($data, [
                'integration_id' => $integration->id,
            ]));

            // 3. If not duplicate, auto-assign and create an Inquiry (main CRM entity)
            if ($lead->status !== 'duplicate') {
                $assignedUserId = $this->autoAssignSales($lead);
                $lead->update(['assigned_user_id' => $assignedUserId]);

                // Create main Inquiry record for the CRM workflow
                $inquiry = $this->convertToInquiry($lead);
                $lead->update(['inquiry_id' => $inquiry->id]);

                // 4. Trigger events
                event(new LeadReceived($lead));
            }

            return $lead;
        });
    }

    /**
     * Simple Round-Robin Sales Assignment.
     */
    protected function autoAssignSales(CrmLead $lead): ?int
    {
        // Find users with 'seller' role or similar
        // For now, we'll just pick an active user who can handle leads
        $users = User::where('is_active', true)->get();
        if ($users->isEmpty()) return null;

        // Implementation of round-robin or least-assigned can go here
        return $users->random()->id;
    }

    /**
     * Convert the normalized lead to the main CRM Inquiry model.
     */
    protected function convertToInquiry(CrmLead $lead): Inquiry
    {
        return Inquiry::create([
            'full_name' => $lead->full_name,
            'phone' => $lead->phone,
            'email' => $lead->email,
            'lead_source' => $lead->source,
            'campaign_name' => $lead->campaign_name,
            'country' => $lead->country,
            'assigned_user_id' => $lead->assigned_user_id,
            'status' => 'new',
            'submitted_data' => $lead->metadata,
        ]);
    }
}
