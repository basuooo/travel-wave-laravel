<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatbotKnowledgeEntry;
use App\Support\AuditLogService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ChatbotKnowledgeController extends Controller
{
    public function index(Request $request)
    {
        $query = ChatbotKnowledgeEntry::query()
            ->with(['creator', 'updater'])
            ->orderByDesc('is_active')
            ->orderBy('priority')
            ->latest('updated_at');

        if ($search = trim((string) $request->query('q'))) {
            $query->where(function (Builder $builder) use ($search) {
                $builder
                    ->where('title_en', 'like', '%' . $search . '%')
                    ->orWhere('title_ar', 'like', '%' . $search . '%')
                    ->orWhere('question_en', 'like', '%' . $search . '%')
                    ->orWhere('question_ar', 'like', '%' . $search . '%')
                    ->orWhere('answer_en', 'like', '%' . $search . '%')
                    ->orWhere('answer_ar', 'like', '%' . $search . '%')
                    ->orWhere('keywords_en', 'like', '%' . $search . '%')
                    ->orWhere('keywords_ar', 'like', '%' . $search . '%')
                    ->orWhere('category_en', 'like', '%' . $search . '%')
                    ->orWhere('category_ar', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->string('status')->toString() === 'active');
        }

        if ($category = trim((string) $request->query('category'))) {
            $query->where(function (Builder $builder) use ($category) {
                $builder->where('category_en', $category)
                    ->orWhere('category_ar', $category);
            });
        }

        $categories = ChatbotKnowledgeEntry::query()
            ->select('category_en', 'category_ar')
            ->where(function (Builder $builder) {
                $builder->whereNotNull('category_en')
                    ->orWhereNotNull('category_ar');
            })
            ->get()
            ->flatMap(fn (ChatbotKnowledgeEntry $entry) => array_filter([$entry->category_en, $entry->category_ar]))
            ->unique()
            ->sort()
            ->values();

        return view('admin.chatbot-knowledge.index', [
            'items' => $query->paginate(20)->withQueryString(),
            'categories' => $categories,
            'summary' => [
                'total' => ChatbotKnowledgeEntry::query()->count(),
                'active' => ChatbotKnowledgeEntry::query()->where('is_active', true)->count(),
                'inactive' => ChatbotKnowledgeEntry::query()->where('is_active', false)->count(),
            ],
        ]);
    }

    public function create()
    {
        return view('admin.chatbot-knowledge.create', [
            'entry' => new ChatbotKnowledgeEntry([
                'is_active' => true,
                'priority' => 0,
            ]),
            'formAction' => route('admin.chatbot-knowledge.store'),
            'formMethod' => 'POST',
        ]);
    }

    public function store(Request $request, AuditLogService $auditLogService)
    {
        $data = $this->validateEntry($request);

        $entry = ChatbotKnowledgeEntry::query()->create($data + [
            'created_by' => $request->user()?->id,
            'updated_by' => $request->user()?->id,
        ]);

        $auditLogService->log(
            $request->user(),
            'chatbot',
            'created',
            $entry,
            [
                'title' => $entry->localizedTitle('ar') ?: $entry->localizedTitle('en'),
                'description' => $entry->localizedQuestion('ar') ?: $entry->localizedQuestion('en'),
                'new_values' => $this->auditValues($entry),
                'changed_fields' => array_keys($this->auditValues($entry)),
            ]
        );

        return redirect()
            ->route('admin.chatbot-knowledge.index')
            ->with('success', __('admin.chatbot_knowledge_entry_created'));
    }

    public function edit(ChatbotKnowledgeEntry $chatbotKnowledge)
    {
        return view('admin.chatbot-knowledge.edit', [
            'entry' => $chatbotKnowledge,
            'formAction' => route('admin.chatbot-knowledge.update', $chatbotKnowledge),
            'formMethod' => 'PUT',
        ]);
    }

    public function update(Request $request, ChatbotKnowledgeEntry $chatbotKnowledge, AuditLogService $auditLogService)
    {
        $before = $this->auditValues($chatbotKnowledge);
        $data = $this->validateEntry($request);

        $chatbotKnowledge->update($data + [
            'updated_by' => $request->user()?->id,
        ]);

        $after = $this->auditValues($chatbotKnowledge->fresh());
        $diff = $auditLogService->diff($before, $after);

        if ($diff['changed_fields'] !== []) {
            $auditLogService->log(
                $request->user(),
                'chatbot',
                'updated',
                $chatbotKnowledge,
                [
                    'title' => $chatbotKnowledge->fresh()->localizedTitle('ar') ?: $chatbotKnowledge->fresh()->localizedTitle('en'),
                    'description' => $chatbotKnowledge->fresh()->localizedQuestion('ar') ?: $chatbotKnowledge->fresh()->localizedQuestion('en'),
                    'old_values' => $diff['old_values'],
                    'new_values' => $diff['new_values'],
                    'changed_fields' => $diff['changed_fields'],
                ]
            );
        }

        return redirect()
            ->route('admin.chatbot-knowledge.index')
            ->with('success', __('admin.chatbot_knowledge_entry_updated'));
    }

    public function destroy(Request $request, ChatbotKnowledgeEntry $chatbotKnowledge, AuditLogService $auditLogService)
    {
        $values = $this->auditValues($chatbotKnowledge);

        $auditLogService->log(
            $request->user(),
            'chatbot',
            'deleted',
            $chatbotKnowledge,
            [
                'title' => $chatbotKnowledge->localizedTitle('ar') ?: $chatbotKnowledge->localizedTitle('en'),
                'description' => $chatbotKnowledge->localizedQuestion('ar') ?: $chatbotKnowledge->localizedQuestion('en'),
                'old_values' => $values,
                'changed_fields' => array_keys($values),
            ]
        );

        $chatbotKnowledge->delete();

        return redirect()
            ->route('admin.chatbot-knowledge.index')
            ->with('success', __('admin.chatbot_knowledge_entry_deleted'));
    }

    protected function validateEntry(Request $request): array
    {
        return $request->validate([
            'title_en' => ['required', 'string', 'max:255'],
            'title_ar' => ['required', 'string', 'max:255'],
            'question_en' => ['nullable', 'string'],
            'question_ar' => ['nullable', 'string'],
            'answer_en' => ['nullable', 'string'],
            'answer_ar' => ['nullable', 'string'],
            'keywords_en' => ['nullable', 'string'],
            'keywords_ar' => ['nullable', 'string'],
            'category_en' => ['nullable', 'string', 'max:255'],
            'category_ar' => ['nullable', 'string', 'max:255'],
            'priority' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['nullable', 'boolean'],
            'match_type' => ['required', 'in:fuzzy,exact'],
        ]) + [
            'is_active' => $request->boolean('is_active'),
            'priority' => (int) ($request->input('priority') ?: 0),
        ];
    }

    protected function auditValues(ChatbotKnowledgeEntry $entry): array
    {
        return [
            'title_en' => $entry->title_en,
            'title_ar' => $entry->title_ar,
            'question_en' => $entry->question_en,
            'question_ar' => $entry->question_ar,
            'answer_en' => $entry->answer_en,
            'answer_ar' => $entry->answer_ar,
            'keywords_en' => $entry->keywords_en,
            'keywords_ar' => $entry->keywords_ar,
            'category_en' => $entry->category_en,
            'category_ar' => $entry->category_ar,
            'is_active' => $entry->is_active,
            'priority' => $entry->priority,
        ];
    }
}
