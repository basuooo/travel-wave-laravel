<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WhatsAppAccount;
use App\Support\AuditLogService;
use Illuminate\Http\Request;

class WhatsAppAccountController extends Controller
{
    public function index()
    {
        $accounts = WhatsAppAccount::with('assignedUser')->latest()->get();
        $employees = User::where('is_admin', true)->orWhereHas('roles')->get();

        return view('admin.whatsapp.accounts.index', compact('accounts', 'employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'phone_number'      => 'required|string|max:50',
            'usage_type'        => 'required|in:retargeting,bulk,both',
            'assigned_user_id'  => 'nullable|exists:users,id',
            'department_branch' => 'nullable|string|max:255',
            'phone_number_id'   => 'nullable|string|max:255',
            'access_token'      => 'nullable|string',
            'business_account_id'=> 'nullable|string|max:255',
        ]);

        $account = WhatsAppAccount::create([
            'name'              => $validated['name'],
            'phone_number'      => $validated['phone_number'],
            'status'            => 'disconnected',
            'usage_type'        => $validated['usage_type'],
            'assigned_user_id'  => $validated['assigned_user_id'] ?? null,
            'department_branch' => $validated['department_branch'] ?? null,
            'connection_settings' => [
                'phone_number_id'    => $validated['phone_number_id'] ?? null,
                'access_token'       => $validated['access_token'] ?? null,
                'business_account_id'=> $validated['business_account_id'] ?? null,
            ],
            'is_active'         => true,
        ]);

        AuditLogService::log('whatsapp_account_created', "Created WhatsApp Account #{$account->id} ({$account->name})");

        return redirect()->route('admin.whatsapp.accounts.index')->with('success', 'تم إضافة رقم الواتساب بنجاح!');
    }

    public function update(Request $request, WhatsAppAccount $account)
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'phone_number'      => 'required|string|max:50',
            'usage_type'        => 'required|in:retargeting,bulk,both',
            'assigned_user_id'  => 'nullable|exists:users,id',
            'department_branch' => 'nullable|string|max:255',
            'phone_number_id'   => 'nullable|string|max:255',
            'access_token'      => 'nullable|string',
            'business_account_id'=> 'nullable|string|max:255',
        ]);

        $settings = $account->connection_settings ?: [];
        $settings['phone_number_id'] = $validated['phone_number_id'] ?? ($settings['phone_number_id'] ?? null);
        $settings['access_token'] = $validated['access_token'] ?? ($settings['access_token'] ?? null);
        $settings['business_account_id'] = $validated['business_account_id'] ?? ($settings['business_account_id'] ?? null);

        $account->update([
            'name'              => $validated['name'],
            'phone_number'      => $validated['phone_number'],
            'usage_type'        => $validated['usage_type'],
            'assigned_user_id'  => $validated['assigned_user_id'] ?? null,
            'department_branch' => $validated['department_branch'] ?? null,
            'connection_settings' => $settings,
        ]);

        AuditLogService::log('whatsapp_account_updated', "Updated WhatsApp Account #{$account->id} ({$account->name})");

        return redirect()->route('admin.whatsapp.accounts.index')->with('success', 'تم تحديث بياتات حساب الواتساب بنجاح!');
    }

    public function toggleConnect(WhatsAppAccount $account)
    {
        $newStatus = $account->status === 'connected' ? 'disconnected' : 'connected';
        $account->update([
            'status'            => $newStatus,
            'last_connected_at' => $newStatus === 'connected' ? now() : $account->last_connected_at,
        ]);

        AuditLogService::log('whatsapp_account_status_toggle', "Toggled status for Account #{$account->id} to {$newStatus}");

        return redirect()->route('admin.whatsapp.accounts.index')->with('success', "تم تغيير حالة الاتصال إلى {$newStatus} بنجاح!");
    }

    public function destroy(WhatsAppAccount $account)
    {
        AuditLogService::log('whatsapp_account_deleted', "Deleted WhatsApp Account #{$account->id} ({$account->name})");
        $account->delete();

        return redirect()->route('admin.whatsapp.accounts.index')->with('success', 'تم حذف رقم الواتساب بنجاح!');
    }
}
