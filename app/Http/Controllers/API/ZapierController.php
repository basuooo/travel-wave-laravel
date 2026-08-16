<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CrmCustomer;
use App\Models\CrmTask;
use App\Models\Inquiry;
use App\Services\ZapierWebhookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ZapierController extends Controller
{
    /**
     * User authentication test endpoint for Zapier API Key setup.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'is_admin' => (bool) ($user->is_admin ?? false),
        ]);
    }

    /**
     * List customers (Polling & Sample Data for Zapier Trigger).
     */
    public function listCustomers(Request $request): JsonResponse
    {
        $limit = min((int) $request->input('limit', 10), 50);

        $customers = CrmCustomer::with(['crmSource', 'crmServiceType'])
            ->latest('id')
            ->take($limit)
            ->get()
            ->map(fn($c) => $this->formatCustomer($c));

        return response()->json($customers);
    }

    /**
     * Create customer (Zapier Action).
     */
    public function createCustomer(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'whatsapp_number' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'nationality' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'destination' => 'nullable|string|max:150',
            'stage' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $customer = CrmCustomer::create([
            'full_name' => $validated['full_name'],
            'phone' => $validated['phone'] ?? null,
            'whatsapp_number' => $validated['whatsapp_number'] ?? $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'nationality' => $validated['nationality'] ?? null,
            'country' => $validated['country'] ?? null,
            'destination' => $validated['destination'] ?? null,
            'stage' => $validated['stage'] ?? CrmCustomer::STAGE_NEW,
            'notes' => $validated['notes'] ?? null,
            'created_by' => $request->user()?->id,
            'assigned_user_id' => $request->user()?->id,
        ]);

        $formatted = $this->formatCustomer($customer);

        ZapierWebhookService::dispatchEvent('customer.created', $formatted, $request->user()?->id);

        return response()->json($formatted, 201);
    }

    /**
     * List inquiries (Polling & Sample Data for Zapier Trigger).
     */
    public function listInquiries(Request $request): JsonResponse
    {
        $limit = min((int) $request->input('limit', 10), 50);

        $inquiries = Inquiry::latest('id')
            ->take($limit)
            ->get()
            ->map(fn($i) => $this->formatInquiry($i));

        return response()->json($inquiries);
    }

    /**
     * Create inquiry (Zapier Action).
     */
    public function createInquiry(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'whatsapp_number' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'country' => 'nullable|string|max:100',
            'destination' => 'nullable|string|max:150',
            'service_type' => 'nullable|string|max:100',
            'travel_date' => 'nullable|date',
            'travelers_count' => 'nullable|integer',
            'message' => 'nullable|string',
            'lead_source' => 'nullable|string|max:100',
        ]);

        $inquiry = Inquiry::create([
            'full_name' => $validated['full_name'],
            'phone' => $validated['phone'] ?? null,
            'whatsapp_number' => $validated['whatsapp_number'] ?? $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'country' => $validated['country'] ?? null,
            'destination' => $validated['destination'] ?? null,
            'service_type' => $validated['service_type'] ?? null,
            'travel_date' => $validated['travel_date'] ?? null,
            'travelers_count' => $validated['travelers_count'] ?? null,
            'message' => $validated['message'] ?? null,
            'lead_source' => $validated['lead_source'] ?? 'Zapier',
            'status' => 'new',
            'assigned_user_id' => $request->user()?->id,
        ]);

        $formatted = $this->formatInquiry($inquiry);

        ZapierWebhookService::dispatchEvent('inquiry.created', $formatted, $request->user()?->id);

        return response()->json($formatted, 201);
    }

    /**
     * List tasks (Polling & Sample Data for Zapier Trigger).
     */
    public function listTasks(Request $request): JsonResponse
    {
        $limit = min((int) $request->input('limit', 10), 50);

        $tasks = CrmTask::latest('id')
            ->take($limit)
            ->get()
            ->map(fn($t) => $this->formatTask($t));

        return response()->json($tasks);
    }

    /**
     * Create task (Zapier Action).
     */
    public function createTask(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'nullable|string|in:low,medium,high,urgent',
            'due_at' => 'nullable|date',
            'category' => 'nullable|string',
            'inquiry_id' => 'nullable|integer|exists:inquiries,id',
        ]);

        $task = CrmTask::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'priority' => $validated['priority'] ?? CrmTask::PRIORITY_MEDIUM,
            'due_at' => $validated['due_at'] ?? null,
            'category' => $validated['category'] ?? CrmTask::CATEGORY_INTERNAL,
            'status' => CrmTask::STATUS_NEW,
            'task_type' => $validated['inquiry_id'] ? CrmTask::TYPE_LEAD : CrmTask::TYPE_GENERAL,
            'inquiry_id' => $validated['inquiry_id'] ?? null,
            'created_by' => $request->user()?->id,
            'assigned_user_id' => $request->user()?->id,
        ]);

        $formatted = $this->formatTask($task);

        ZapierWebhookService::dispatchEvent('task.created', $formatted, $request->user()?->id);

        return response()->json($formatted, 201);
    }

    protected function formatCustomer(CrmCustomer $customer): array
    {
        return [
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
            'updated_at' => $customer->updated_at?->toISOString(),
        ];
    }

    protected function formatInquiry(Inquiry $inquiry): array
    {
        return [
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
            'updated_at' => $inquiry->updated_at?->toISOString(),
        ];
    }

    protected function formatTask(CrmTask $task): array
    {
        return [
            'id' => (string) $task->id,
            'title' => $task->title,
            'description' => $task->description,
            'priority' => $task->priority,
            'status' => $task->status,
            'category' => $task->category,
            'task_type' => $task->task_type,
            'due_at' => $task->due_at?->toISOString(),
            'created_at' => $task->created_at?->toISOString(),
            'updated_at' => $task->updated_at?->toISOString(),
        ];
    }
}
