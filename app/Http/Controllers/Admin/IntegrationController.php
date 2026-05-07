<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Integrations\PlatformManager;
use App\Models\CrmApiLog;
use App\Models\CrmIntegration;
use App\Models\CrmWebhookLog;
use Illuminate\Http\Request;

class IntegrationController extends Controller
{
    public function index()
    {
        $integrations = CrmIntegration::withCount(['leads', 'webhookLogs'])->get();
        return view('admin.integrations.index', compact('integrations'));
    }

    public function create()
    {
        return view('admin.integrations.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'platform' => 'required|string|in:meta,tiktok',
            'is_active' => 'boolean',
            'credentials' => 'required|array',
            'webhook_verify_token' => 'nullable|string',
        ]);

        CrmIntegration::create($data);

        return redirect()->route('admin.integrations.index')->with('success', 'Integration created successfully.');
    }

    public function edit(CrmIntegration $integration)
    {
        $platform = PlatformManager::make($integration);
        $fields = $platform->getSettingsFields();
        
        return view('admin.integrations.edit', compact('integration', 'fields'));
    }

    public function update(Request $request, CrmIntegration $integration)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'boolean',
            'credentials' => 'required|array',
            'webhook_verify_token' => 'nullable|string',
        ]);

        $integration->update($data);

        return redirect()->route('admin.integrations.index')->with('success', 'Integration updated successfully.');
    }

    public function testConnection(CrmIntegration $integration)
    {
        try {
            $platform = PlatformManager::make($integration);
            $success = $platform->testConnection($integration);
            
            $integration->update([
                'connection_status' => $success ? 'connected' : 'failed',
                'last_sync_at' => now(),
                'error_log' => $success ? null : 'Connection test failed.',
            ]);

            return back()->with($success ? 'success' : 'error', $success ? 'Connection successful!' : 'Connection failed.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function logs()
    {
        $webhookLogs = CrmWebhookLog::with('integration')->latest()->paginate(20);
        $apiLogs = CrmApiLog::with('integration')->latest()->paginate(20);
        
        return view('admin.integrations.logs', compact('webhookLogs', 'apiLogs'));
    }
}
