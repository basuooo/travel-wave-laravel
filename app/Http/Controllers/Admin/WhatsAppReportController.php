<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhatsAppCampaign;
use App\Models\WhatsAppCampaignRecipient;
use Illuminate\Http\Request;

class WhatsAppReportController extends Controller
{
    public function index()
    {
        $campaigns = WhatsAppCampaign::with('account')->latest()->get();

        $totalSent = WhatsAppCampaignRecipient::where('status', 'sent')->count();
        $totalFailed = WhatsAppCampaignRecipient::where('status', 'failed')->count();
        $totalOptOuts = WhatsAppCampaignRecipient::whereIn('status', ['skipped_optout', 'skipped_blacklist'])->count();
        $totalRecipients = WhatsAppCampaignRecipient::count();

        $deliveryRate = $totalRecipients > 0 ? round(($totalSent / $totalRecipients) * 100, 1) : 0;

        return view('admin.whatsapp.reports.index', compact(
            'campaigns',
            'totalSent',
            'totalFailed',
            'totalOptOuts',
            'totalRecipients',
            'deliveryRate'
        ));
    }

    public function exportCsv(WhatsAppCampaign $campaign)
    {
        $fileName = "campaign_{$campaign->id}_report.csv";
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename={$fileName}",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($campaign) {
            $file = fopen('php://output', 'w');
            // Add UTF-8 BOM
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, ['ID', 'Contact Name', 'Phone', 'Matching Status', 'Delivery Status', 'Sent At', 'Error Message']);

            foreach ($campaign->recipients as $rec) {
                fputcsv($file, [
                    $rec->id,
                    $rec->contact_name,
                    $rec->phone,
                    $rec->contact_status,
                    $rec->status,
                    $rec->sent_at ? $rec->sent_at->format('Y-m-d H:i:s') : '-',
                    $rec->error_message ?? ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
