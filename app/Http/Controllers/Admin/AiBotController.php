<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiBotConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AiBotController extends Controller
{
    public function index()
    {
        $bots = AiBotConfig::query()->latest()->get();
        return view('admin.ai-bots.index', compact('bots'));
    }

    public function create()
    {
        $bot = new AiBotConfig([
            'enabled' => true,
            'provider' => 'openai',
            'max_tokens' => 1000,
            'temperature' => 0.7,
        ]);
        return view('admin.ai-bots.create', compact('bot'));
    }

    public function store(Request $request)
    {
        $data = $this->validateBot($request);
        $data['key'] = Str::slug($data['name']);
        
        AiBotConfig::create($data);

        return redirect()->route('admin.ai-bots.index')->with('success', 'Bot created successfully.');
    }

    public function edit(AiBotConfig $aiBot)
    {
        return view('admin.ai-bots.edit', ['bot' => $aiBot]);
    }

    public function update(Request $request, AiBotConfig $aiBot)
    {
        $data = $this->validateBot($request, $aiBot->id);
        $aiBot->update($data);

        return redirect()->route('admin.ai-bots.index')->with('success', 'Bot updated successfully.');
    }

    public function destroy(AiBotConfig $aiBot)
    {
        if ($aiBot->key === 'default') {
            return back()->with('error', 'Cannot delete the default bot.');
        }
        $aiBot->delete();
        return redirect()->route('admin.ai-bots.index')->with('success', 'Bot deleted successfully.');
    }

    public function toggle(AiBotConfig $aiBot)
    {
        $aiBot->update(['enabled' => ! $aiBot->enabled]);
        return back()->with('success', 'Bot status updated.');
    }

    protected function validateBot(Request $request, $id = null)
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'enabled' => 'boolean',
            'provider' => 'required|in:openai,gemini,deepseek,claude',
            'openai_api_key' => 'nullable|string',
            'openai_model' => 'nullable|string',
            'gemini_api_key' => 'nullable|string',
            'gemini_model' => 'nullable|string',
            'deepseek_api_key' => 'nullable|string',
            'deepseek_model' => 'nullable|string',
            'claude_api_key' => 'nullable|string',
            'claude_model' => 'nullable|string',
            'system_prompt_ar' => 'nullable|string',
            'system_prompt_en' => 'nullable|string',
            'max_tokens' => 'nullable|integer|min:100|max:4000',
            'temperature' => 'nullable|numeric|min:0|max:2',
            'fallback_to_keyword' => 'boolean',
        ]) + [
            'enabled' => $request->boolean('enabled'),
            'fallback_to_keyword' => $request->boolean('fallback_to_keyword'),
        ];
    }
}
