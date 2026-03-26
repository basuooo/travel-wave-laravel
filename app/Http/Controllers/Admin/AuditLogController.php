<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use App\Support\AuditLogService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::query()
            ->with(['actor', 'auditable'])
            ->latest('id');

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->date('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->date('to'));
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->integer('user_id'));
        }

        if ($request->filled('module')) {
            $query->where('module', $request->string('module')->toString());
        }

        if ($request->filled('action_type')) {
            $query->where('action_type', $request->string('action_type')->toString());
        }

        if ($request->filled('auditable_type')) {
            $query->where('auditable_type', $request->string('auditable_type')->toString());
        }

        if ($request->filled('auditable_id')) {
            $query->where('auditable_id', $request->integer('auditable_id'));
        }

        if ($search = trim((string) $request->query('q'))) {
            $query->where(function (Builder $builder) use ($search) {
                $builder->where('title', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%')
                    ->orWhere('target_label', 'like', '%' . $search . '%')
                    ->orWhere('action_type', 'like', '%' . $search . '%')
                    ->orWhere('module', 'like', '%' . $search . '%')
                    ->orWhereHas('actor', function (Builder $actorQuery) use ($search) {
                        $actorQuery->where('name', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%');
                    });
            });
        }

        return view('admin.audit-logs.index', [
            'items' => $query->paginate(30)->withQueryString(),
            'users' => User::query()->where('is_active', true)->orderBy('name')->get(),
            'moduleOptions' => AuditLogService::moduleOptions(),
            'actionOptions' => AuditLogService::actionOptions(),
            'auditableTypeOptions' => AuditLogService::auditableTypeOptions(),
            'summary' => [
                'total' => AuditLog::query()->count(),
                'today' => AuditLog::query()->whereDate('created_at', today())->count(),
                'critical' => AuditLog::query()->whereIn('action_type', ['deleted', 'force_deleted', 'expense_deleted'])->count(),
                'finance' => AuditLog::query()->where('module', 'accounting')->count(),
            ],
        ]);
    }

    public function show(AuditLog $auditLog, AuditLogService $auditLogService)
    {
        $auditLog->load(['actor', 'auditable']);

        return view('admin.audit-logs.show', [
            'auditLog' => $auditLog,
            'contextualUrl' => $auditLogService->contextualUrl($auditLog),
            'fieldLabelResolver' => fn (string $field) => AuditLogService::fieldLabel($field),
        ]);
    }
}
