<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CrmLeadSource;
use App\Models\CrmStatus;
use App\Models\User;
use App\Support\CrmReportsCenterService;
use App\Support\SimpleSpreadsheet;
use Illuminate\Http\Request;

class CrmReportsCenterController extends Controller
{
    public function index(Request $request, CrmReportsCenterService $reportsService)
    {
        $viewer = auth()->user();
        if ($viewer && method_exists($viewer, 'hasPermission')) {
            if (!$viewer->hasPermission('reports.view') && !$viewer->is_admin) {
                abort(403, 'غير مصرح لك باستعراض شاشة التقارير والتحليل الإداري.');
            }
        }

        $activeTab = $request->string('tab')->toString() ?: 'overview';
        $filters = $reportsService->normalizeFilters($request);

        $statuses = CrmStatus::query()->where('is_active', true)->orderBy('sort_order')->get();
        $sources = CrmLeadSource::query()->where('is_active', true)->orderBy('sort_order')->get();
        $salesReps = User::query()->where('is_active', true)->orderBy('name')->get();

        $data = [];
        switch ($activeTab) {
            case 'sales':
                $data['sales_performance'] = $reportsService->getSalesPerformanceReport($viewer, $filters);
                break;
            case 'leads':
                $data['lead_funnel'] = $reportsService->getLeadFunnelReport($viewer, $filters);
                $data['lead_aging'] = $reportsService->getLeadAgingReport($viewer, $filters);
                break;
            case 'marketing':
                $data['source_performance'] = $reportsService->getLeadSourcePerformanceReport($viewer, $filters);
                break;
            case 'revenue':
                $data['executive'] = $reportsService->getExecutiveDashboardData($viewer, $filters);
                break;
            case 'advanced':
                $data['executive'] = $reportsService->getExecutiveDashboardData($viewer, $filters);
                $data['sales_performance'] = $reportsService->getSalesPerformanceReport($viewer, $filters);
                break;
            case 'overview':
            default:
                $data['executive'] = $reportsService->getExecutiveDashboardData($viewer, $filters);
                $data['lead_funnel'] = $reportsService->getLeadFunnelReport($viewer, $filters);
                break;
        }

        return view('admin.reports_center.index', [
            'activeTab' => $activeTab,
            'filters' => $filters,
            'statuses' => $statuses,
            'sources' => $sources,
            'salesReps' => $salesReps,
            'data' => $data,
        ]);
    }

    public function drilldown(Request $request, CrmReportsCenterService $reportsService)
    {
        $viewer = auth()->user();
        if ($viewer && method_exists($viewer, 'hasPermission')) {
            if (!$viewer->hasPermission('reports.view') && !$viewer->is_admin) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        }

        $result = $reportsService->getDrilldownLeads($request, $viewer);

        return response()->json($result);
    }

    public function export(Request $request, CrmReportsCenterService $reportsService, SimpleSpreadsheet $spreadsheet)
    {
        $viewer = auth()->user();
        if ($viewer && method_exists($viewer, 'hasPermission')) {
            if (!$viewer->hasPermission('reports.view') && !$viewer->is_admin) {
                abort(403);
            }
        }

        $drilldown = $reportsService->getDrilldownLeads($request, $viewer);
        $leads = $drilldown['leads'] ?? [];

        $headers = ['# ID', 'اسم العميل', 'رقم الهاتف', 'الحالة الحالية', 'الموظف المسئول', 'تاريخ التسجيل'];
        $rows = [];

        foreach ($leads as $lead) {
            $rows[] = [
                $lead['id'],
                $lead['full_name'],
                $lead['phone'],
                $lead['status_name'],
                $lead['assigned_user_name'],
                $lead['created_at_formatted'],
            ];
        }

        $filename = 'crm-reports-export-' . date('Y-m-d-His') . '.xlsx';
        $path = storage_path('app/' . $filename);

        $spreadsheet->createXlsx($path, $headers, $rows);

        return response()->download($path, $filename)->deleteFileAfterSend(true);
    }
}
