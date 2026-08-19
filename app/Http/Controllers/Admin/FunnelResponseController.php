<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Funnel;
use App\Models\FunnelResponse;
use App\Services\Funnels\FunnelCrmSyncService;
use Illuminate\Http\Request;

class FunnelResponseController extends Controller
{
    public function index(Request $request)
    {
        if (! \Illuminate\Support\Facades\Schema::hasTable('funnel_responses')) {
            $responses = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 25);
            $funnels = collect();
            return view('admin.funnels.responses.index', compact('responses', 'funnels'));
        }

        $query = FunnelResponse::query()->with(['funnel', 'result', 'inquiry'])->latest();

        if ($request->filled('funnel_id')) {
            $query->where('funnel_id', $request->funnel_id);
        }

        if ($request->filled('crm_status')) {
            $query->where('crm_sync_status', $request->crm_status);
        }

        $responses = $query->paginate(25);
        $funnels = Funnel::whereNull('deleted_at')->get();

        return view('admin.funnels.responses.index', compact('responses', 'funnels'));
    }

    public function show(FunnelResponse $response)
    {
        $response->load(['funnel', 'result', 'inquiry', 'answers.element']);

        return view('admin.funnels.responses.show', compact('response'));
    }

    public function retryCrmSync(FunnelResponse $response, FunnelCrmSyncService $syncService)
    {
        $success = $syncService->syncToCrm($response);

        if ($success) {
            return redirect()->back()->with('success', __('admin.crm_sync_success'));
        }

        return redirect()->back()->with('error', __('admin.crm_sync_failed') . ': ' . $response->sync_error);
    }
}
