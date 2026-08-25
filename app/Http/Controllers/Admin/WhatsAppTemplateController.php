<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhatsAppTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class WhatsAppTemplateController extends Controller
{
    public function index()
    {
        if (!Schema::hasTable('whatsapp_templates')) {
            try {
                Artisan::call('migrate', ['--force' => true]);
            } catch (\Throwable $e) {}
        }

        $templates = Schema::hasTable('whatsapp_templates') ? WhatsAppTemplate::with('creator')->latest()->get() : collect();
        return view('admin.whatsapp.templates.index', compact('templates'));
    }

    public function store(Request $request)
    {
        if (!Schema::hasTable('whatsapp_templates')) {
            try {
                Artisan::call('migrate', ['--force' => true]);
            } catch (\Throwable $e) {}
        }

        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'category'   => 'required|string',
            'content'    => 'required|string',
            'media_type' => 'nullable|string',
            'media_url'  => 'nullable|string',
        ]);

        WhatsAppTemplate::create([
            'name'               => $validated['name'],
            'category'           => $validated['category'],
            'content'            => $validated['content'],
            'media_type'         => $validated['media_type'] ?? null,
            'media_url'          => $validated['media_url'] ?? null,
            'created_by_user_id' => auth()->id(),
        ]);

        return redirect()->route('admin.whatsapp.templates.index')->with('success', 'تم إضافة القالب بنجاح!');
    }

    public function update(Request $request, WhatsAppTemplate $template)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'category'   => 'required|string',
            'content'    => 'required|string',
            'media_type' => 'nullable|string',
            'media_url'  => 'nullable|string',
        ]);

        $template->update($validated);

        return redirect()->route('admin.whatsapp.templates.index')->with('success', 'تم تحديث القالب بنجاح!');
    }

    public function destroy(WhatsAppTemplate $template)
    {
        $template->delete();
        return redirect()->route('admin.whatsapp.templates.index')->with('success', 'تم حذف القالب بنجاح!');
    }
}
