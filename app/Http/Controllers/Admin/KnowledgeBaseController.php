<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KnowledgeBaseArticle;
use App\Models\KnowledgeBaseCategory;
use App\Models\User;
use App\Support\AuditLogService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class KnowledgeBaseController extends Controller
{
    public function index(Request $request)
    {
        $viewer = $request->user();
        $canManage = $viewer?->hasPermission('knowledge_base.manage') ?? false;

        $query = KnowledgeBaseArticle::query()
            ->with(['category', 'creator', 'updater'])
            ->when(! $canManage, fn ($builder) => $builder->readableBy($viewer))
            ->latest('published_at')
            ->latest('updated_at');

        if ($search = trim((string) $request->query('q'))) {
            $query->where(function (Builder $builder) use ($search) {
                $builder->where('title', 'like', '%' . $search . '%')
                    ->orWhere('summary', 'like', '%' . $search . '%')
                    ->orWhere('content', 'like', '%' . $search . '%')
                    ->orWhere('slug', 'like', '%' . $search . '%')
                    ->orWhereHas('category', function (Builder $categoryQuery) use ($search) {
                        $categoryQuery->where('name_ar', 'like', '%' . $search . '%')
                            ->orWhere('name_en', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($request->filled('knowledge_base_category_id')) {
            $query->where('knowledge_base_category_id', $request->integer('knowledge_base_category_id'));
        }

        if ($canManage && $request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        if ($canManage && $request->filled('visibility_scope')) {
            $query->where('visibility_scope', $request->string('visibility_scope')->toString());
        }

        if ($request->filled('created_by')) {
            $query->where('created_by', $request->integer('created_by'));
        }

        if ($request->filled('is_featured')) {
            $query->where('is_featured', $request->boolean('is_featured'));
        }

        match ($request->string('sort')->toString()) {
            'title' => $query->orderBy('title'),
            'updated_oldest' => $query->oldest('updated_at'),
            'oldest' => $query->oldest('published_at')->oldest('created_at'),
            default => $query->latest('is_featured')->latest('published_at')->latest('updated_at'),
        };

        return view('admin.knowledge-base.index', [
            'items' => $query->paginate(20)->withQueryString(),
            'categories' => KnowledgeBaseCategory::query()->where('is_active', true)->orderBy('sort_order')->get(),
            'authors' => User::query()->where('is_active', true)->orderBy('name')->get(),
            'statusOptions' => KnowledgeBaseArticle::statusOptions(),
            'visibilityOptions' => KnowledgeBaseArticle::visibilityOptions(),
            'canManageKnowledgeBase' => $canManage,
            'summary' => [
                'total_published' => KnowledgeBaseArticle::query()->where('status', KnowledgeBaseArticle::STATUS_PUBLISHED)->count(),
                'featured' => KnowledgeBaseArticle::query()->where('status', KnowledgeBaseArticle::STATUS_PUBLISHED)->where('is_featured', true)->count(),
                'updated_this_week' => KnowledgeBaseArticle::query()->whereBetween('updated_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
                'categories' => KnowledgeBaseCategory::query()->where('is_active', true)->count(),
            ],
            'featuredArticles' => KnowledgeBaseArticle::query()
                ->with('category')
                ->when(! $canManage, fn ($builder) => $builder->readableBy($viewer))
                ->where('is_featured', true)
                ->latest('updated_at')
                ->limit(4)
                ->get(),
            'latestArticles' => KnowledgeBaseArticle::query()
                ->with('category')
                ->when(! $canManage, fn ($builder) => $builder->readableBy($viewer))
                ->latest('updated_at')
                ->limit(5)
                ->get(),
        ]);
    }

    public function create()
    {
        return view('admin.knowledge-base.create', [
            'article' => new KnowledgeBaseArticle([
                'status' => KnowledgeBaseArticle::STATUS_DRAFT,
                'visibility_scope' => KnowledgeBaseArticle::SCOPE_ALL_STAFF,
                'is_featured' => false,
                'sort_order' => 0,
            ]),
            'categories' => KnowledgeBaseCategory::query()->where('is_active', true)->orderBy('sort_order')->get(),
            'statusOptions' => KnowledgeBaseArticle::statusOptions(),
            'visibilityOptions' => KnowledgeBaseArticle::visibilityOptions(),
            'formAction' => route('admin.knowledge-base.store'),
            'formMethod' => 'POST',
        ]);
    }

    public function store(Request $request, AuditLogService $auditLogService)
    {
        $data = $this->validateArticle($request);

        $article = KnowledgeBaseArticle::query()->create($data + [
            'slug' => $this->uniqueSlug($data['slug'] ?? $data['title']),
            'created_by' => $request->user()?->id,
            'updated_by' => $request->user()?->id,
            'published_at' => ($data['status'] ?? KnowledgeBaseArticle::STATUS_DRAFT) === KnowledgeBaseArticle::STATUS_PUBLISHED ? now() : null,
        ]);

        $auditLogService->log(
            $request->user(),
            'knowledge_base',
            'created',
            $article,
            [
                'title' => $article->title,
                'description' => $article->summary,
                'new_values' => $this->articleAuditValues($article->load('category')),
                'changed_fields' => array_keys($this->articleAuditValues($article->load('category'))),
            ]
        );

        return redirect()
            ->route('admin.knowledge-base.show', $article)
            ->with('success', __('admin.kb_article_created'));
    }

    public function show(Request $request, KnowledgeBaseArticle $article)
    {
        $viewer = $request->user();
        $canManage = $viewer?->hasPermission('knowledge_base.manage') ?? false;

        abort_unless($canManage || ($article->status === KnowledgeBaseArticle::STATUS_PUBLISHED && $article->allowsUser($viewer)), 403);

        $article->load(['category', 'creator', 'updater']);

        return view('admin.knowledge-base.show', [
            'article' => $article,
            'relatedArticles' => KnowledgeBaseArticle::query()
                ->with('category')
                ->whereKeyNot($article->id)
                ->when($article->knowledge_base_category_id, fn ($builder) => $builder->where('knowledge_base_category_id', $article->knowledge_base_category_id))
                ->when(! $canManage, fn ($builder) => $builder->readableBy($viewer))
                ->latest('updated_at')
                ->limit(4)
                ->get(),
            'canManageKnowledgeBase' => $canManage,
        ]);
    }

    public function edit(KnowledgeBaseArticle $article)
    {
        return view('admin.knowledge-base.edit', [
            'article' => $article->load('category'),
            'categories' => KnowledgeBaseCategory::query()->where('is_active', true)->orderBy('sort_order')->get(),
            'statusOptions' => KnowledgeBaseArticle::statusOptions(),
            'visibilityOptions' => KnowledgeBaseArticle::visibilityOptions(),
            'formAction' => route('admin.knowledge-base.update', $article),
            'formMethod' => 'PUT',
        ]);
    }

    public function update(Request $request, KnowledgeBaseArticle $article, AuditLogService $auditLogService)
    {
        $before = $this->articleAuditValues($article->load('category'));
        $data = $this->validateArticle($request, $article);

        $statusWasPublished = $article->status === KnowledgeBaseArticle::STATUS_PUBLISHED;
        $article->fill($data + [
            'slug' => $this->uniqueSlug($data['slug'] ?? $data['title'], $article->id),
            'updated_by' => $request->user()?->id,
        ]);

        if (! $statusWasPublished && $article->status === KnowledgeBaseArticle::STATUS_PUBLISHED) {
            $article->published_at = now();
        } elseif ($article->status !== KnowledgeBaseArticle::STATUS_PUBLISHED) {
            $article->published_at = null;
        }

        $article->save();
        $article->load('category');

        $after = $this->articleAuditValues($article);
        $diff = $auditLogService->diff($before, $after);

        if ($diff['changed_fields'] !== []) {
            $auditLogService->log(
                $request->user(),
                'knowledge_base',
                'updated',
                $article,
                [
                    'title' => $article->title,
                    'description' => $article->summary,
                    'old_values' => $diff['old_values'],
                    'new_values' => $diff['new_values'],
                    'changed_fields' => $diff['changed_fields'],
                ]
            );
        }

        return redirect()
            ->route('admin.knowledge-base.show', $article)
            ->with('success', __('admin.kb_article_updated'));
    }

    protected function validateArticle(Request $request, ?KnowledgeBaseArticle $article = null): array
    {
        return $request->validate([
            'knowledge_base_category_id' => ['nullable', 'exists:knowledge_base_categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'summary' => ['nullable', 'string'],
            'content' => ['required', 'string'],
            'status' => ['required', Rule::in(array_keys(KnowledgeBaseArticle::statusOptions()))],
            'visibility_scope' => ['required', Rule::in(array_keys(KnowledgeBaseArticle::visibilityOptions()))],
            'is_featured' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]) + [
            'is_featured' => $request->boolean('is_featured'),
            'sort_order' => (int) ($request->input('sort_order') ?: 0),
        ];
    }

    protected function uniqueSlug(string $source, ?int $ignoreId = null): string
    {
        $base = Str::slug($source);
        $slug = $base !== '' ? $base : 'knowledge-article';
        $counter = 1;

        while (
            KnowledgeBaseArticle::query()
                ->when($ignoreId, fn ($builder) => $builder->whereKeyNot($ignoreId))
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = $base . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    protected function articleAuditValues(KnowledgeBaseArticle $article): array
    {
        return [
            'title' => $article->title,
            'summary' => $article->summary,
            'category' => $article->category?->localizedName(),
            'status' => $article->localizedStatus(),
            'visibility_scope' => $article->localizedVisibilityScope(),
            'is_featured' => $article->is_featured,
        ];
    }
}
