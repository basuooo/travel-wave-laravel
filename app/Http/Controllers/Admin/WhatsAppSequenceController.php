<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhatsAppAccount;
use App\Models\WhatsAppSequence;
use App\Models\WhatsAppSequenceStep;
use App\Models\WhatsAppTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class WhatsAppSequenceController extends Controller
{
    public function index()
    {
        if (!Schema::hasTable('whatsapp_sequences')) {
            try {
                Artisan::call('migrate', ['--force' => true]);
            } catch (\Throwable $e) {}
        }

        $sequences = Schema::hasTable('whatsapp_sequences') ? WhatsAppSequence::with(['account', 'steps'])->latest()->get() : collect();
        $accounts = Schema::hasTable('whatsapp_accounts') ? WhatsAppAccount::all() : collect();
        $templates = Schema::hasTable('whatsapp_templates') ? WhatsAppTemplate::all() : collect();

        return view('admin.whatsapp.sequences.index', compact('sequences', 'accounts', 'templates'));
    }

    public function store(Request $request)
    {
        if (!Schema::hasTable('whatsapp_sequences')) {
            try {
                Artisan::call('migrate', ['--force' => true]);
            } catch (\Throwable $e) {}
        }

        $validated = $request->validate([
            'name'                 => 'required|string|max:255',
            'description'          => 'nullable|string',
            'whatsapp_account_id'  => 'nullable|exists:whatsapp_accounts,id',
            'smart_stop_on_reply'  => 'boolean',
            'smart_stop_on_convert'=> 'boolean',
            'steps'                => 'required|array|min:1',
            'steps.*.delay_days'   => 'required|integer|min:0',
            'steps.*.content'      => 'required|string',
        ]);

        $sequence = WhatsAppSequence::create([
            'name'                 => $validated['name'],
            'description'          => $validated['description'] ?? null,
            'whatsapp_account_id'  => $validated['whatsapp_account_id'] ?? null,
            'smart_stop_on_reply'  => $request->has('smart_stop_on_reply'),
            'smart_stop_on_convert'=> $request->has('smart_stop_on_convert'),
            'is_active'            => true,
        ]);

        foreach ($validated['steps'] as $idx => $stepData) {
            WhatsAppSequenceStep::create([
                'sequence_id'     => $sequence->id,
                'step_number'     => $idx + 1,
                'delay_days'      => $stepData['delay_days'],
                'message_content' => $stepData['content'],
            ]);
        }

        return redirect()->route('admin.whatsapp.sequences.index')->with('success', 'تم إنشاء متتابعة المتابعة (Sequence) بنجاح!');
    }

    public function destroy(WhatsAppSequence $sequence)
    {
        $sequence->delete();
        return redirect()->route('admin.whatsapp.sequences.index')->with('success', 'تم حذف المتتابعة بنجاح!');
    }
}
