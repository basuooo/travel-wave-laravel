<?php

namespace App\Support;

use App\Models\AccountingCustomerAccount;
use App\Models\Inquiry;
use Illuminate\Support\Facades\DB;

class AccountingCalculatorService
{
    public function syncLeadAccount(Inquiry $lead, ?int $actorId = null): AccountingCustomerAccount
    {
        $lead->loadMissing(['assignedUser', 'crmSource', 'crmServiceType']);

        return DB::transaction(function () use ($lead, $actorId) {
            $account = AccountingCustomerAccount::query()->firstOrNew([
                'inquiry_id' => $lead->id,
            ]);

            if (! $account->exists) {
                $account->created_by = $actorId;
            }

            $paymentsTotal = (float) $account->payments()->sum('amount');
            $expensesTotal = (float) $account->expenses()->sum('amount');
            $totalAmount = round((float) ($lead->total_amount ?? 0), 2);
            $paidAmount = round((float) ($lead->paid_amount ?? $paymentsTotal), 2);
            $remainingAmount = max(0, round($totalAmount - $paidAmount, 2));
            $companyProfitBeforeSeller = round($paidAmount - $expensesTotal, 2);
            $sellerProfit = round($companyProfitBeforeSeller * 0.10, 2);
            $finalCompanyProfit = round($companyProfitBeforeSeller - $sellerProfit, 2);
            $paymentStatus = $paidAmount <= 0
                ? 'unpaid'
                : ($remainingAmount <= 0 ? 'fully_paid' : 'partially_paid');

            $account->fill([
                'crm_customer_id' => $lead->crmCustomer?->id,
                'assigned_user_id' => $lead->assigned_user_id,
                'customer_name' => $lead->full_name,
                'phone' => $lead->phone,
                'whatsapp_number' => $lead->whatsapp_number,
                'email' => $lead->email,
                'service_label' => $lead->localizedServiceType() ?: $lead->service_type,
                'service_destination' => $lead->serviceDestinationValue(),
                'lead_source' => $lead->crmSource?->localizedName() ?: $lead->lead_source,
                'total_amount' => $totalAmount,
                'paid_amount' => $paidAmount,
                'remaining_amount' => $remainingAmount,
                'total_customer_expenses' => round($expensesTotal, 2),
                'company_profit_before_seller' => $companyProfitBeforeSeller,
                'seller_profit' => $sellerProfit,
                'final_company_profit' => $finalCompanyProfit,
                'payment_status' => $paymentStatus,
                'last_payment_at' => $account->payments()->max('payment_date'),
            ])->save();

            $lead->forceFill([
                'paid_amount' => $paidAmount,
                'remaining_amount' => $remainingAmount,
                'payment_status' => $paymentStatus,
            ])->save();

            return $account->fresh(['payments', 'expenses', 'assignedUser', 'inquiry']);
        });
    }

    public function syncLeadPaymentSummary(Inquiry $lead, float $paidAmount, ?int $actorId = null): AccountingCustomerAccount
    {
        $lead->forceFill([
            'paid_amount' => round($paidAmount, 2),
            'remaining_amount' => max(0, round((float) ($lead->total_amount ?? 0) - $paidAmount, 2)),
            'payment_status' => $paidAmount <= 0
                ? 'unpaid'
                : ((float) ($lead->total_amount ?? 0) <= $paidAmount ? 'fully_paid' : 'partially_paid'),
        ])->save();

        return $this->syncLeadAccount($lead, $actorId);
    }
}
