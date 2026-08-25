<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WhatsAppAccount;
use App\Services\WhatsApp\WhatsAppGatewayService;
use App\Support\AuditLogService;
use App\Support\WhatsAppSchemaInstaller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class WhatsAppAccountController extends Controller
{
    public function index()
    {
        WhatsAppSchemaInstaller::install();

        $accounts = Schema::hasTable('whatsapp_accounts') ? WhatsAppAccount::with('assignedUser')->latest()->get() : collect();
        $employees = User::where('is_admin', true)->orWhereHas('roles')->get();

        return view('admin.whatsapp.accounts.index', compact('accounts', 'employees'));
    }

    public function store(Request $request)
    {
        WhatsAppSchemaInstaller::install();

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

        app(AuditLogService::class)->log(auth()->user(), 'whatsapp', 'created', $account, ['description' => "Created WhatsApp Account #{$account->id} ({$account->name})"]);

        return redirect()->route('admin.whatsapp.accounts.index')->with('success', 'تم إضافة رقم الواتساب بنجاح!');
    }

    public function update(Request $request, WhatsAppAccount $account)
    {
        WhatsAppSchemaInstaller::install();

        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'phone_number'      => 'required|string|max:50',
            'usage_type'        => 'required|in:retargeting,bulk,both',
            'assigned_user_id'  => 'nullable|exists:users,id',
            'department_branch' => 'nullable|string|max:255',
            'phone_number_id'   => 'nullable|string|max:255',
            'access_token'      => 'nullable|string',
            'business_account_id'=> 'nullable|string|max:255',
            'gateway_url'       => 'nullable|url',
        ]);

        $settings = $account->connection_settings ?: [];
        $settings['phone_number_id'] = $validated['phone_number_id'] ?? ($settings['phone_number_id'] ?? null);
        $settings['access_token'] = $validated['access_token'] ?? ($settings['access_token'] ?? null);
        $settings['business_account_id'] = $validated['business_account_id'] ?? ($settings['business_account_id'] ?? null);
        $settings['gateway_url'] = $validated['gateway_url'] ?? ($settings['gateway_url'] ?? null);

        $account->update([
            'name'              => $validated['name'],
            'phone_number'      => $validated['phone_number'],
            'usage_type'        => $validated['usage_type'],
            'assigned_user_id'  => $validated['assigned_user_id'] ?? null,
            'department_branch' => $validated['department_branch'] ?? null,
            'connection_settings' => $settings,
        ]);

        app(AuditLogService::class)->log(auth()->user(), 'whatsapp', 'updated', $account, ['description' => "Updated WhatsApp Account #{$account->id} ({$account->name})"]);

        return redirect()->route('admin.whatsapp.accounts.index')->with('success', 'تم تحديث بيانات حساب الواتساب بنجاح!');
    }

    public function getQrCode(WhatsAppAccount $account)
    {
        WhatsAppSchemaInstaller::install();

        $settings = $account->connection_settings ?: [];
        $customGatewayUrl = $settings['gateway_url'] ?? null;
        $hasMetaCredentials = !empty($settings['phone_number_id']) && !empty($settings['access_token']);

        $gatewayService = new WhatsAppGatewayService($customGatewayUrl);
        $gatewayResult = $gatewayService->getQrCode($account->id);

        if (isset($gatewayResult['status']) && $gatewayResult['status'] === 'connected') {
            $account->update([
                'status'            => 'connected',
                'last_connected_at' => now(),
            ]);
            return response()->json([
                'status' => 'connected',
                'message' => 'الرقم متصل بالفعل والجلسة شغال بنجاح!'
            ]);
        }

        if (isset($gatewayResult['qr'])) {
            return response()->json([
                'status'  => 'success',
                'type'    => 'live_baileys',
                'qr'      => $gatewayResult['qr'],
                'message' => 'تم توليد QR Code الحي من خادم الواتساب بنجاح. امسحه الآن من الموبايل!'
            ]);
        }

        // Fallback for Meta Cloud API or offline local server guidance
        $timestamp = time();
        $tokenSession = "WA_PAIRING_SESSION_" . $account->id . "_" . dechex($timestamp);
        $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=" . urlencode($tokenSession);

        return response()->json([
            'status'             => 'success',
            'type'               => $hasMetaCredentials ? 'meta_cloud_api' : 'gateway_offline',
            'has_meta'           => $hasMetaCredentials,
            'qr_url'             => $qrUrl,
            'token_session'      => $tokenSession,
            'phone_number'       => $account->phone_number,
            'timestamp'          => $timestamp,
            'meta_phone_id'      => $settings['phone_number_id'] ?? null,
            'message'            => $gatewayResult['message'] ?? 'تم إعداد هذا الرقم للربط.'
        ]);
    }

    public function toggleConnect(WhatsAppAccount $account)
    {
        $newStatus = $account->status === 'connected' ? 'disconnected' : 'connected';
        $account->update([
            'status'            => $newStatus,
            'last_connected_at' => $newStatus === 'connected' ? now() : $account->last_connected_at,
        ]);

        app(AuditLogService::class)->log(auth()->user(), 'whatsapp', 'status_changed', $account, ['description' => "Toggled status for Account #{$account->id} to {$newStatus}"]);

        return redirect()->route('admin.whatsapp.accounts.index')->with('success', "تم تغيير حالة الاتصال إلى {$newStatus} بنجاح!");
    }

    public function destroy(WhatsAppAccount $account)
    {
        app(AuditLogService::class)->log(auth()->user(), 'whatsapp', 'deleted', $account, ['description' => "Deleted WhatsApp Account #{$account->id} ({$account->name})"]);
        $account->delete();

        return redirect()->route('admin.whatsapp.accounts.index')->with('success', 'تم حذف رقم الواتساب بنجاح!');
    }
}
