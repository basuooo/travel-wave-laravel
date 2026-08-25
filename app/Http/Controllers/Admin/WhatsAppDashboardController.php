<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhatsAppAccount;
use App\Models\WhatsAppCampaign;
use App\Models\WhatsAppCampaignRecipient;
use App\Models\WhatsAppContact;
use App\Models\WhatsAppConversation;
use App\Models\WhatsAppMessage;
use Illuminate\Http\Request;

class WhatsAppDashboardController extends Controller
{
    public function index()
    {
        $accountsCount = WhatsAppAccount::count();
        $connectedAccountsCount = WhatsAppAccount::where('status', 'connected')->count();
        $disconnectedAccountsCount = WhatsAppAccount::where('status', 'disconnected')->count();
        
        $conversationsCount = WhatsAppConversation::count();
        $contactsCount = WhatsAppContact::count();
        
        $activeCampaignsCount = WhatsAppCampaign::where('status', 'running')->count();
        $scheduledCampaignsCount = WhatsAppCampaign::where('status', 'scheduled')->count();
        
        $sentMessagesCount = WhatsAppCampaignRecipient::where('status', 'sent')->count();
        $failedMessagesCount = WhatsAppCampaignRecipient::where('status', 'failed')->count();
        $pendingMessagesCount = WhatsAppCampaignRecipient::where('status', 'pending')->count();
        $repliesCount = WhatsAppMessage::where('direction', 'inbound')->count();

        $accounts = WhatsAppAccount::with('assignedUser')->latest()->take(10)->get();
        $recentCampaigns = WhatsAppCampaign::with(['account', 'creator'])->latest()->take(6)->get();

        return view('admin.whatsapp.dashboard', compact(
            'accountsCount',
            'connectedAccountsCount',
            'disconnectedAccountsCount',
            'conversationsCount',
            'contactsCount',
            'activeCampaignsCount',
            'scheduledCampaignsCount',
            'sentMessagesCount',
            'failedMessagesCount',
            'pendingMessagesCount',
            'repliesCount',
            'accounts',
            'recentCampaigns'
        ));
    }
}
