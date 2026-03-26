<?php

namespace App\Support;

use App\Models\AccountingCustomerAccount;
use App\Models\CrmCustomer;
use App\Models\CrmDocument;
use App\Models\CrmDocumentCategory;
use App\Models\CrmTask;
use App\Models\Inquiry;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CrmDocumentService
{
    public const DISK = 'local';

    public static function documentableMap(): array
    {
        return [
            'inquiry' => Inquiry::class,
            'customer' => CrmCustomer::class,
            'task' => CrmTask::class,
            'account' => AccountingCustomerAccount::class,
        ];
    }

    public static function documentableAlias(?string $className): string
    {
        return array_search($className, static::documentableMap(), true) ?: 'unknown';
    }

    public static function localizedEntityLabel(string $alias): string
    {
        return match ($alias) {
            'inquiry' => __('admin.crm_leads'),
            'customer' => __('admin.crm_customers'),
            'task' => __('admin.crm_tasks'),
            'account' => __('admin.accounting_customer_accounts'),
            default => __('admin.documents'),
        };
    }

    public static function documentableLabel(?Model $documentable): string
    {
        return match (true) {
            $documentable instanceof Inquiry => $documentable->full_name ?: ('#' . $documentable->id),
            $documentable instanceof CrmCustomer => ($documentable->customer_code ? $documentable->customer_code . ' - ' : '') . $documentable->full_name,
            $documentable instanceof CrmTask => $documentable->title,
            $documentable instanceof AccountingCustomerAccount => $documentable->customer_name,
            default => '-',
        };
    }

    public function resolveDocumentable(string $alias, int $id): Model
    {
        $className = static::documentableMap()[$alias] ?? null;

        if (! $className) {
            throw ValidationException::withMessages([
                'documentable_type' => __('validation.in'),
            ]);
        }

        return $className::query()->findOrFail($id);
    }

    public function authorizeDocumentable(?User $user, Model $documentable): bool
    {
        if (! $user || ! $user->hasPermission('documents.view')) {
            return false;
        }

        if ($documentable instanceof Inquiry) {
            return CrmLeadAccess::canAccessLead($user, $documentable);
        }

        if ($documentable instanceof CrmCustomer) {
            return CrmLeadAccess::canViewAll($user) || (int) $documentable->assigned_user_id === (int) $user->id;
        }

        if ($documentable instanceof CrmTask) {
            return CrmLeadAccess::canViewAll($user)
                || (int) $documentable->assigned_user_id === (int) $user->id
                || (int) $documentable->created_by === (int) $user->id;
        }

        if ($documentable instanceof AccountingCustomerAccount) {
            return $user->hasPermission('accounting.view')
                && (CrmLeadAccess::canViewAll($user) || (int) $documentable->assigned_user_id === (int) $user->id);
        }

        return false;
    }

    public function authorizeDocument(?User $user, CrmDocument $document): bool
    {
        return $this->authorizeDocumentable($user, $document->documentable);
    }

    public function visibleQuery(User $user): Builder
    {
        $query = CrmDocument::query()->with(['category', 'uploader', 'documentable']);

        if (CrmLeadAccess::canViewAll($user)) {
            return $query;
        }

        return $query->where(function (Builder $builder) use ($user) {
            $builder
                ->whereHasMorph('documentable', [Inquiry::class], fn (Builder $morphQuery) => CrmLeadAccess::applyVisibilityScope($morphQuery, $user))
                ->orWhereHasMorph('documentable', [CrmCustomer::class], fn (Builder $morphQuery) => $morphQuery->where('assigned_user_id', $user->id))
                ->orWhereHasMorph('documentable', [CrmTask::class], function (Builder $morphQuery) use ($user) {
                    $morphQuery->where(function (Builder $taskQuery) use ($user) {
                        $taskQuery->where('assigned_user_id', $user->id)->orWhere('created_by', $user->id);
                    });
                });
        });
    }

    public function storeDocument(Model $documentable, CrmDocumentCategory $category, UploadedFile $file, array $attributes, User $actor): CrmDocument
    {
        $directory = $this->documentDirectory($documentable);
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'bin');
        $storedFileName = Str::uuid()->toString() . '.' . $extension;
        $path = $file->storeAs($directory, $storedFileName, static::DISK);

        return $documentable->documents()->create([
            'crm_document_category_id' => $category->id,
            'title' => trim((string) ($attributes['title'] ?? '')) ?: pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'original_file_name' => $file->getClientOriginalName(),
            'stored_file_name' => $storedFileName,
            'disk' => static::DISK,
            'directory' => $directory,
            'path' => $path,
            'mime_type' => $file->getMimeType(),
            'extension' => $extension,
            'size' => $file->getSize(),
            'status' => CrmDocument::STATUS_UPLOADED,
            'issue_date' => $attributes['issue_date'] ?? null,
            'expiry_date' => $attributes['expiry_date'] ?? null,
            'is_required' => (bool) ($attributes['is_required'] ?? false),
            'note' => $attributes['note'] ?? null,
            'meta' => $attributes['meta'] ?? [],
            'uploaded_by' => $actor->id,
            'uploaded_at' => now(),
        ])->load(['category', 'uploader', 'documentable']);
    }

    public function deleteDocument(CrmDocument $document): void
    {
        $disk = Storage::disk($document->disk ?: static::DISK);

        if ($disk->exists($document->normalized_path)) {
            $disk->delete($document->normalized_path);
        }

        $document->delete();
    }

    public function documentDirectory(Model $documentable): string
    {
        return match (true) {
            $documentable instanceof Inquiry => 'documents/leads/' . $documentable->id,
            $documentable instanceof CrmCustomer => 'documents/customers/' . $documentable->id,
            $documentable instanceof CrmTask => 'documents/tasks/' . $documentable->id,
            $documentable instanceof AccountingCustomerAccount => 'documents/accounts/' . $documentable->id,
            default => 'documents/misc',
        };
    }

    public function previewResponse(CrmDocument $document)
    {
        $disk = Storage::disk($document->disk ?: static::DISK);

        abort_unless($disk->exists($document->normalized_path), 404);

        if (! $document->isPreviewable()) {
            return $disk->download($document->normalized_path, $document->original_file_name);
        }

        return $disk->response($document->normalized_path, $document->original_file_name, [
            'Content-Disposition' => 'inline; filename="' . addslashes($document->original_file_name) . '"',
        ]);
    }

    public function downloadResponse(CrmDocument $document)
    {
        $disk = Storage::disk($document->disk ?: static::DISK);

        abort_unless($disk->exists($document->normalized_path), 404);

        return $disk->download($document->normalized_path, $document->original_file_name);
    }
}
