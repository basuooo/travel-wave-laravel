<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WhatsAppAccount;
use App\Models\WhatsAppContact;
use Illuminate\Http\Request;

class WhatsAppContactController extends Controller
{
    public function index(Request $request)
    {
        $query = WhatsAppContact::with(['account', 'assignedUser', 'lead', 'customer']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'LIKE', "%{$s}%")
                  ->orWhere('phone', 'LIKE', "%{$s}%")
                  ->orWhere('normalized_phone', 'LIKE', "%{$s}%");
            });
        }

        if ($request->filled('account_id')) {
            $query->where('whatsapp_account_id', $request->account_id);
        }

        if ($request->filled('status_in_crm')) {
            $query->where('status_in_crm', $request->status_in_crm);
        }

        if ($request->filled('opt_out_status')) {
            $query->where('opt_out_status', $request->opt_out_status);
        }

        $contacts = $query->latest()->paginate(25);
        $accounts = WhatsAppAccount::all();
        $employees = User::all();

        return view('admin.whatsapp.contacts.index', compact('contacts', 'accounts', 'employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                => 'required|string|max:255',
            'phone'               => 'required|string|max:50',
            'whatsapp_account_id' => 'nullable|exists:whatsapp_accounts,id',
            'assigned_user_id'    => 'nullable|exists:users,id',
            'status_in_crm'       => 'nullable|string',
            'service'             => 'nullable|string',
            'country'             => 'nullable|string',
            'lead_source'         => 'nullable|string',
            'notes'               => 'nullable|string',
        ]);

        $normalized = preg_replace('/\D/', '', $validated['phone']);

        WhatsAppContact::create([
            'name'                => $validated['name'],
            'phone'               => $validated['phone'],
            'normalized_phone'    => $normalized,
            'whatsapp_account_id' => $validated['whatsapp_account_id'] ?? null,
            'assigned_user_id'    => $validated['assigned_user_id'] ?? null,
            'status_in_crm'       => $validated['status_in_crm'] ?? 'lead',
            'service'             => $validated['service'] ?? null,
            'country'             => $validated['country'] ?? null,
            'lead_source'         => $validated['lead_source'] ?? 'WhatsApp',
            'notes'               => $validated['notes'] ?? null,
        ]);

        return redirect()->route('admin.whatsapp.contacts.index')->with('success', 'تم إنشاء جهة الاتصال بنجاح!');
    }
}
