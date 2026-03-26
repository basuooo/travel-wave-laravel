<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CrmCustomer;
use App\Models\CrmDocument;
use App\Models\CrmDocumentCategory;
use App\Models\Inquiry;
use App\Models\User;
use App\Support\CrmDocumentService;
use App\Support\CrmLeadAccess;
use App\Support\AuditLogService;
use App\Support\WorkflowAutomationService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class CrmDocumentController extends Controller
{
    public function index(Request $request, CrmDocumentService $documentService)
    {
        $query = $documentService->visibleQuery($request->user())->latest('uploaded_at');

        if ($search = trim((string) $request->query('q'))) {
            $query->where(function (Builder $builder) use ($search) {
                $builder->where('title', 'like', '%' . $search . '%')
                    ->orWhere('original_file_name', 'like', '%' . $search . '%')
                    ->orWhereHasMorph('documentable', [Inquiry::class], function (Builder $morphQuery) use ($search) {
                        $morphQuery->where('full_name', 'like', '%' . $search . '%')
                            ->orWhere('phone', 'like', '%' . $search . '%');
                    })
                    ->orWhereHasMorph('documentable', [CrmCustomer::class], function (Builder $morphQuery) use ($search) {
                        $morphQuery->where('full_name', 'like', '%' . $search . '%')
                            ->orWhere('customer_code', 'like', '%' . $search . '%')
                            ->orWhere('phone', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($request->filled('crm_document_category_id')) {
            $query->where('crm_document_category_id', $request->integer('crm_document_category_id'));
        }

        if ($request->filled('documentable_type')) {
            $className = CrmDocumentService::documentableMap()[$request->string('documentable_type')->toString()] ?? null;

            if ($className) {
                $query->where('documentable_type', $className);
            }
        }

        if ($request->filled('uploaded_by')) {
            $query->where('uploaded_by', $request->integer('uploaded_by'));
        }

        if ($request->filled('from')) {
            $query->whereDate('uploaded_at', '>=', $request->date('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('uploaded_at', '<=', $request->date('to'));
        }

        return view('admin.documents.index', [
            'items' => $query->paginate(20)->withQueryString(),
            'categories' => CrmDocumentCategory::query()->where('is_active', true)->orderBy('sort_order')->get(),
            'entityTypes' => collect(CrmDocumentService::documentableMap())->keys()->all(),
            'uploaders' => User::query()->where('is_active', true)->orderBy('name')->get(),
            'summary' => [
                'total' => (clone $documentService->visibleQuery($request->user()))->count(),
                'uploaded_this_week' => (clone $documentService->visibleQuery($request->user()))->whereBetween('uploaded_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
                'recent' => (clone $documentService->visibleQuery($request->user()))->whereDate('uploaded_at', today())->count(),
            ],
        ]);
    }

    public function create(Request $request, CrmDocumentService $documentService)
    {
        $documentable = null;
        $documentableType = $request->string('documentable_type')->toString();

        if ($documentableType !== '' && $request->filled('documentable_id')) {
            $documentable = $documentService->resolveDocumentable($documentableType, $request->integer('documentable_id'));
            abort_unless($documentService->authorizeDocumentable($request->user(), $documentable), 403);
        }

        return view('admin.documents.create', [
            'document' => new CrmDocument([
                'documentable_type' => $documentable ? get_class($documentable) : null,
                'documentable_id' => $documentable?->getKey(),
            ]),
            'selectedDocumentable' => $documentable,
            'documentableType' => $documentableType,
            'categories' => CrmDocumentCategory::query()->where('is_active', true)->orderBy('sort_order')->get(),
            'leadOptions' => $this->leadOptions($request->user()),
            'customerOptions' => $this->customerOptions($request->user()),
            'entityTypes' => collect(CrmDocumentService::documentableMap())->keys()->only(['inquiry', 'customer'])->values()->all(),
        ]);
    }

    public function store(Request $request, CrmDocumentService $documentService)
    {
        $auditLogService = app(AuditLogService::class);
        $data = $request->validate([
            'documentable_type' => ['required', 'in:inquiry,customer'],
            'documentable_id' => ['nullable', 'integer'],
            'documentable_id_inquiry' => ['nullable', 'integer'],
            'documentable_id_customer' => ['nullable', 'integer'],
            'crm_document_category_id' => ['required', 'exists:crm_document_categories,id'],
            'title' => ['nullable', 'string', 'max:255'],
            'file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx', 'max:10240'],
            'note' => ['nullable', 'string'],
            'issue_date' => ['nullable', 'date'],
            'expiry_date' => ['nullable', 'date'],
            'is_required' => ['nullable', 'boolean'],
        ]);

        $documentableId = (int) ($data['documentable_id']
            ?? ($data['documentable_type'] === 'inquiry' ? ($data['documentable_id_inquiry'] ?? 0) : ($data['documentable_id_customer'] ?? 0)));

        abort_unless($documentableId > 0, 422);

        $documentable = $documentService->resolveDocumentable($data['documentable_type'], $documentableId);
        abort_unless($request->user()?->hasPermission('documents.manage'), 403);
        abort_unless($documentService->authorizeDocumentable($request->user(), $documentable), 403);

        $category = CrmDocumentCategory::query()
            ->whereKey($data['crm_document_category_id'])
            ->where('is_active', true)
            ->firstOrFail();

        $document = $documentService->storeDocument($documentable, $category, $request->file('file'), $data, $request->user());
        $auditLogService->log(
            $request->user(),
            'documents',
            'uploaded',
            $document->fresh(['category']),
            [
                'title' => $document->title ?: $document->original_file_name,
                'description' => CrmDocumentService::documentableLabel($documentable),
                'new_values' => [
                    'title' => $document->title,
                    'crm_document_category_id' => $document->category?->localizedName() ?? $document->crm_document_category_id,
                    'related_record' => CrmDocumentService::documentableLabel($documentable),
                    'file_type' => $document->extension,
                ],
                'changed_fields' => ['title', 'crm_document_category_id', 'related_record', 'file_type'],
            ]
        );
        app(WorkflowAutomationService::class)->dispatch(WorkflowAutomationService::TRIGGER_DOCUMENT_UPLOADED, $document->fresh(['category', 'documentable']), [
            'actor' => $request->user(),
        ]);

        return redirect()
            ->route('admin.documents.show', $document)
            ->with('success', __('admin.document_uploaded_success'));
    }

    public function show(CrmDocument $document, CrmDocumentService $documentService)
    {
        $document->load(['category', 'uploader', 'documentable']);
        abort_unless($documentService->authorizeDocument(auth()->user(), $document), 403);

        return view('admin.documents.show', [
            'document' => $document,
            'documentableLabel' => CrmDocumentService::documentableLabel($document->documentable),
        ]);
    }

    public function preview(CrmDocument $document, CrmDocumentService $documentService)
    {
        $document->loadMissing('documentable');
        abort_unless($documentService->authorizeDocument(auth()->user(), $document), 403);

        return $documentService->previewResponse($document);
    }

    public function download(CrmDocument $document, CrmDocumentService $documentService)
    {
        $document->loadMissing('documentable');
        abort_unless($documentService->authorizeDocument(auth()->user(), $document), 403);

        return $documentService->downloadResponse($document);
    }

    public function destroy(CrmDocument $document, CrmDocumentService $documentService)
    {
        $auditLogService = app(AuditLogService::class);
        $document->loadMissing('documentable');
        abort_unless(auth()->user()?->hasPermission('documents.manage'), 403);
        abort_unless($documentService->authorizeDocument(auth()->user(), $document), 403);

        $redirectUrl = $this->documentableRedirect($document->documentable);
        $auditLogService->log(
            auth()->user(),
            'documents',
            'deleted',
            $document,
            [
                'title' => $document->title ?: $document->original_file_name,
                'description' => CrmDocumentService::documentableLabel($document->documentable),
                'old_values' => [
                    'title' => $document->title,
                    'crm_document_category_id' => $document->category?->localizedName() ?? $document->crm_document_category_id,
                    'related_record' => CrmDocumentService::documentableLabel($document->documentable),
                    'file_type' => $document->extension,
                ],
                'changed_fields' => ['title', 'crm_document_category_id', 'related_record', 'file_type'],
            ]
        );
        $documentService->deleteDocument($document);

        return redirect()->to($redirectUrl)->with('success', __('admin.document_deleted_success'));
    }

    protected function leadOptions(User $user)
    {
        return CrmLeadAccess::applyVisibilityScope(
            Inquiry::query()->select('id', 'full_name', 'phone')->latest()->limit(150),
            $user
        )->get();
    }

    protected function customerOptions(User $user)
    {
        return CrmCustomer::query()
            ->visibleTo($user)
            ->select('id', 'customer_code', 'full_name', 'phone')
            ->latest('converted_at')
            ->limit(150)
            ->get();
    }

    protected function documentableRedirect($documentable): string
    {
        return match (true) {
            $documentable instanceof Inquiry => route('admin.crm.leads.show', $documentable),
            $documentable instanceof CrmCustomer => route('admin.crm.customers.show', $documentable),
            default => route('admin.documents.index'),
        };
    }
}
