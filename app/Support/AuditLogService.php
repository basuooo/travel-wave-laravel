<?php

namespace App\Support;

use App\Models\AccountingCustomerAccount;
use App\Models\AccountingCustomerExpense;
use App\Models\AccountingCustomerPayment;
use App\Models\AccountingEmployeeTransaction;
use App\Models\AccountingGeneralExpense;
use App\Models\AuditLog;
use App\Models\CrmCustomer;
use App\Models\CrmDocument;
use App\Models\CrmInformation;
use App\Models\CrmTask;
use App\Models\Inquiry;
use App\Models\KnowledgeBaseArticle;
use App\Models\KnowledgeBaseCategory;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class AuditLogService
{
    public function log(?User $actor, string $module, string $actionType, Model $auditable = null, array $payload = []): AuditLog
    {
        $request = $payload['request'] ?? request();

        return AuditLog::query()->create([
            'user_id' => $actor?->id,
            'action_type' => $actionType,
            'module' => $module,
            'auditable_type' => $auditable ? get_class($auditable) : null,
            'auditable_id' => $auditable?->getKey(),
            'title' => $payload['title'] ?? null,
            'description' => $payload['description'] ?? null,
            'old_values' => $this->normalizeArray($payload['old_values'] ?? null),
            'new_values' => $this->normalizeArray($payload['new_values'] ?? null),
            'changed_fields' => array_values($payload['changed_fields'] ?? []),
            'target_label' => $payload['target_label'] ?? ($auditable ? $this->targetLabel($auditable) : null),
            'ip_address' => $request instanceof Request ? $request->ip() : null,
            'user_agent' => $request instanceof Request ? $request->userAgent() : null,
            'created_at' => now(),
        ]);
    }

    public function diff(array $before, array $after): array
    {
        $oldValues = [];
        $newValues = [];
        $changedFields = [];

        foreach ($after as $field => $newValue) {
            $oldValue = $before[$field] ?? null;

            if ($this->normalizeValue($oldValue) === $this->normalizeValue($newValue)) {
                continue;
            }

            $changedFields[] = $field;
            $oldValues[$field] = $this->normalizeValue($oldValue);
            $newValues[$field] = $this->normalizeValue($newValue);
        }

        return [
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'changed_fields' => $changedFields,
        ];
    }

    public function only(array $source, array $fields): array
    {
        $values = [];

        foreach ($fields as $field) {
            $values[$field] = $source[$field] ?? null;
        }

        return $values;
    }

    public function targetLabel(Model $auditable): string
    {
        return match (true) {
            $auditable instanceof Inquiry => trim(($auditable->full_name ?: __('admin.crm_lead')) . ' #' . $auditable->id),
            $auditable instanceof CrmCustomer => trim(($auditable->customer_code ?: $auditable->full_name ?: __('admin.customer')) . ' #' . $auditable->id),
            $auditable instanceof CrmTask => trim(($auditable->title ?: __('admin.crm_task_details')) . ' #' . $auditable->id),
            $auditable instanceof AccountingCustomerAccount => trim(($auditable->customer_name ?: __('admin.accounting_customer_accounts')) . ' #' . $auditable->id),
            $auditable instanceof AccountingCustomerPayment => __('admin.accounting_add_payment') . ' #' . $auditable->id,
            $auditable instanceof AccountingCustomerExpense => __('admin.accounting_customer_expenses') . ' #' . $auditable->id,
            $auditable instanceof AccountingGeneralExpense => __('admin.accounting_general_expenses') . ' #' . $auditable->id,
            $auditable instanceof AccountingEmployeeTransaction => __('admin.accounting_employee_transactions') . ' #' . $auditable->id,
            $auditable instanceof CrmInformation => trim(($auditable->title ?: __('admin.crm_information')) . ' #' . $auditable->id),
            $auditable instanceof CrmDocument => trim(($auditable->title ?: $auditable->original_file_name ?: __('admin.document_details')) . ' #' . $auditable->id),
            $auditable instanceof KnowledgeBaseArticle => trim(($auditable->title ?: __('admin.kb_article')) . ' #' . $auditable->id),
            $auditable instanceof KnowledgeBaseCategory => trim(($auditable->localizedName() ?: __('admin.kb_category')) . ' #' . $auditable->id),
            default => class_basename($auditable) . ' #' . $auditable->getKey(),
        };
    }

    public function contextualUrl(AuditLog $log): ?string
    {
        $auditable = $log->auditable;

        if (! $auditable) {
            return null;
        }

        return match (true) {
            $auditable instanceof Inquiry => route('admin.crm.leads.show', $auditable),
            $auditable instanceof CrmCustomer => route('admin.crm.customers.show', $auditable),
            $auditable instanceof CrmTask => route('admin.crm.tasks.show', $auditable),
            $auditable instanceof AccountingCustomerAccount => route('admin.accounting.customers.show', $auditable),
            $auditable instanceof AccountingCustomerPayment => route('admin.accounting.customers.show', $auditable->accounting_customer_account_id),
            $auditable instanceof AccountingCustomerExpense => route('admin.accounting.customers.show', $auditable->accounting_customer_account_id),
            $auditable instanceof AccountingGeneralExpense => route('admin.accounting.general-expenses.index'),
            $auditable instanceof AccountingEmployeeTransaction => route('admin.accounting.employees.index'),
            $auditable instanceof CrmInformation => route('admin.crm.information.show', $auditable),
            $auditable instanceof CrmDocument => route('admin.documents.show', $auditable),
            $auditable instanceof KnowledgeBaseArticle => route('admin.knowledge-base.show', $auditable),
            $auditable instanceof KnowledgeBaseCategory => route('admin.knowledge-base.categories.index'),
            default => null,
        };
    }

    public static function moduleOptions(): array
    {
        return [
            'crm_leads' => __('admin.audit_module_crm_leads'),
            'customers' => __('admin.audit_module_customers'),
            'tasks' => __('admin.audit_module_tasks'),
            'accounting' => __('admin.audit_module_accounting'),
            'information' => __('admin.audit_module_information'),
            'documents' => __('admin.audit_module_documents'),
            'knowledge_base' => __('admin.audit_module_knowledge_base'),
        ];
    }

    public static function actionOptions(): array
    {
        return [
            'created' => __('admin.audit_action_created'),
            'updated' => __('admin.audit_action_updated'),
            'deleted' => __('admin.audit_action_deleted'),
            'status_changed' => __('admin.audit_action_status_changed'),
            'assigned' => __('admin.audit_action_assigned'),
            'reassigned' => __('admin.audit_action_reassigned'),
            'completed' => __('admin.audit_action_completed'),
            'acknowledged' => __('admin.audit_action_acknowledged'),
            'payment_added' => __('admin.audit_action_payment_added'),
            'expense_added' => __('admin.audit_action_expense_added'),
            'expense_updated' => __('admin.audit_action_expense_updated'),
            'expense_deleted' => __('admin.audit_action_expense_deleted'),
            'uploaded' => __('admin.audit_action_uploaded'),
            'converted_to_customer' => __('admin.audit_action_converted_to_customer'),
            'amount_changed' => __('admin.audit_action_amount_changed'),
            'restored' => __('admin.audit_action_restored'),
            'force_deleted' => __('admin.audit_action_force_deleted'),
            'transaction_added' => __('admin.audit_action_transaction_added'),
            'transaction_updated' => __('admin.audit_action_transaction_updated'),
            'transaction_deleted' => __('admin.audit_action_transaction_deleted'),
        ];
    }

    public static function auditableTypeOptions(): array
    {
        return [
            Inquiry::class => __('admin.audit_entity_lead'),
            CrmCustomer::class => __('admin.audit_entity_customer'),
            CrmTask::class => __('admin.audit_entity_task'),
            AccountingCustomerAccount::class => __('admin.audit_entity_account'),
            AccountingCustomerPayment::class => __('admin.audit_entity_payment'),
            AccountingCustomerExpense::class => __('admin.audit_entity_customer_expense'),
            AccountingGeneralExpense::class => __('admin.audit_entity_general_expense'),
            AccountingEmployeeTransaction::class => __('admin.audit_entity_employee_transaction'),
            CrmInformation::class => __('admin.audit_entity_information'),
            CrmDocument::class => __('admin.audit_entity_document'),
            KnowledgeBaseArticle::class => __('admin.audit_entity_kb_article'),
            KnowledgeBaseCategory::class => __('admin.audit_entity_kb_category'),
        ];
    }

    public static function actionLabel(string $action): string
    {
        return static::actionOptions()[$action] ?? str($action)->replace('_', ' ')->headline()->toString();
    }

    public static function moduleLabel(?string $module): string
    {
        return static::moduleOptions()[$module ?? ''] ?? str((string) $module)->replace('_', ' ')->headline()->toString();
    }

    public static function actionBadgeClass(string $action): string
    {
        return match ($action) {
            'deleted', 'force_deleted', 'expense_deleted' => 'danger',
            'status_changed', 'reassigned', 'amount_changed' => 'warning',
            'payment_added', 'completed', 'uploaded', 'converted_to_customer', 'acknowledged', 'restored' => 'success',
            default => 'primary',
        };
    }

    public static function moduleBadgeClass(?string $module): string
    {
        return match ($module) {
            'accounting' => 'warning',
            'documents' => 'info',
            'information' => 'secondary',
            default => 'light',
        };
    }

    public static function fieldLabel(string $field): string
    {
        $labels = [
            'full_name' => __('admin.audit_field_full_name'),
            'phone' => __('admin.audit_field_phone'),
            'whatsapp_number' => __('admin.audit_field_whatsapp_number'),
            'email' => __('admin.audit_field_email'),
            'crm_status_id' => __('admin.audit_field_crm_status'),
            'assigned_user_id' => __('admin.audit_field_assigned_user'),
            'crm_source_id' => __('admin.audit_field_crm_source'),
            'crm_service_type_id' => __('admin.audit_field_crm_service_type'),
            'crm_service_subtype_id' => __('admin.audit_field_crm_service_subtype'),
            'priority' => __('admin.audit_field_priority'),
            'travel_date' => __('admin.audit_field_travel_date'),
            'next_follow_up_at' => __('admin.audit_field_next_follow_up'),
            'follow_up_result' => __('admin.audit_field_follow_up_result'),
            'total_amount' => __('admin.audit_field_total_amount'),
            'paid_amount' => __('admin.audit_field_paid_amount'),
            'remaining_amount' => __('admin.audit_field_remaining_amount'),
            'payment_status' => __('admin.audit_field_payment_status'),
            'stage' => __('admin.audit_field_stage'),
            'amount' => __('admin.audit_field_amount'),
            'expense_date' => __('admin.audit_field_expense_date'),
            'payment_date' => __('admin.audit_field_payment_date'),
            'title' => __('admin.audit_field_title'),
            'crm_document_category_id' => __('admin.audit_field_document_category'),
            'category' => __('admin.audit_field_category'),
            'audience_type' => __('admin.audit_field_audience_type'),
            'is_active' => __('admin.audit_field_is_active'),
            'transaction_type' => __('admin.audit_field_transaction_type'),
            'transaction_date' => __('admin.audit_field_transaction_date'),
        ];

        return $labels[$field] ?? str($field)->replace('_', ' ')->headline()->toString();
    }

    protected function normalizeArray(?array $values): ?array
    {
        if ($values === null || $values === []) {
            return null;
        }

        $normalized = [];

        foreach ($values as $key => $value) {
            $normalized[$key] = $this->normalizeValue($value);
        }

        return $normalized;
    }

    protected function normalizeValue(mixed $value): mixed
    {
        if ($value instanceof CarbonInterface) {
            return $value->toDateTimeString();
        }

        if ($value instanceof Model) {
            return method_exists($value, 'localizedName')
                ? $value->localizedName()
                : ($value->name ?? $value->title ?? $value->id);
        }

        if (is_bool($value)) {
            return $value ? 1 : 0;
        }

        if (is_array($value)) {
            return array_map(fn ($item) => $this->normalizeValue($item), $value);
        }

        return $value;
    }
}
