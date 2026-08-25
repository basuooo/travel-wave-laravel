<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhatsAppAccount;
use App\Models\WhatsAppCampaign;
use App\Models\WhatsAppCampaignRecipient;
use App\Models\WhatsAppContact;
use App\Models\WhatsAppConversation;
use App\Models\WhatsAppMessage;
use App\Support\WhatsAppSchemaInstaller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class WhatsAppDashboardController extends Controller
{
    public function index()
    {
        WhatsAppSchemaInstaller::install();

        $accountsCount = Schema::hasTable('whatsapp_accounts') ? WhatsAppAccount::count() : 0;
        $connectedAccountsCount = Schema::hasTable('whatsapp_accounts') ? WhatsAppAccount::where('status', 'connected')->count() : 0;
        $disconnectedAccountsCount = Schema::hasTable('whatsapp_accounts') ? WhatsAppAccount::where('status', 'disconnected')->count() : 0;
        
        $conversationsCount = Schema::hasTable('whatsapp_conversations') ? WhatsAppConversation::count() : 0;
        $contactsCount = Schema::hasTable('whatsapp_contacts') ? WhatsAppContact::count() : 0;
        
        $activeCampaignsCount = Schema::hasTable('whatsapp_campaigns') ? WhatsAppCampaign::where('status', 'running')->count() : 0;
        $scheduledCampaignsCount = Schema::hasTable('whatsapp_campaigns') ? WhatsAppCampaign::where('status', 'scheduled')->count() : 0;
        
        $sentMessagesCount = Schema::hasTable('whatsapp_campaign_recipients') ? WhatsAppCampaignRecipient::where('status', 'sent')->count() : 0;
        $failedMessagesCount = Schema::hasTable('whatsapp_campaign_recipients') ? WhatsAppCampaignRecipient::where('status', 'failed')->count() : 0;
        $pendingMessagesCount = Schema::hasTable('whatsapp_campaign_recipients') ? WhatsAppCampaignRecipient::where('status', 'pending')->count() : 0;
        $repliesCount = Schema::hasTable('whatsapp_messages') ? WhatsAppMessage::where('direction', 'inbound')->count() : 0;

        $accounts = Schema::hasTable('whatsapp_accounts') ? WhatsAppAccount::with('assignedUser')->latest()->take(10)->get() : collect();
        $recentCampaigns = Schema::hasTable('whatsapp_campaigns') ? WhatsAppCampaign::with(['account', 'creator'])->latest()->take(6)->get() : collect();

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
