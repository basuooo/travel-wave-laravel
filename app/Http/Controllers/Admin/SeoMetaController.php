<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\SeoManager;
use Illuminate\Http\Request;

class SeoMetaController extends Controller
{
    public function index(SeoManager $seoManager)
    {
        return view('admin.seo.meta-index', [
            'targets' => $seoManager->targets(),
            'entries' => \App\Models\SeoMetaEntry::query()->get()->keyBy(fn ($entry) => $entry->target_type . ':' . $entry->target_id),
        ]);
    }

    public function edit(string $targetType, int $targetId, SeoManager $seoManager)
    {
        return view('admin.seo.meta-form', [
            'entry' => $seoManager->entryForTarget($targetType, $targetId),
            'target' => $seoManager->targets()->first(fn (array $item) => $item['type'] === $targetType && (int) $item['id'] === $targetId),
        ]);
    }

    public function update(Request $request, string $targetType, int $targetId, SeoManager $seoManager)
    {
        $entry = $seoManager->entryForTarget($targetType, $targetId);

        $data = $request->validate([
            'meta_title_en' => ['nullable', 'string', 'max:255'],
            'meta_title_ar' => ['nullable', 'string', 'max:255'],
            'meta_description_en' => ['nullable', 'string'],
            'meta_description_ar' => ['nullable', 'string'],
            'canonical_url' => ['nullable', 'url', 'max:255'],
            'robots_meta' => ['nullable', 'string', 'max:255'],
            'og_title_en' => ['nullable', 'string', 'max:255'],
            'og_title_ar' => ['nullable', 'string', 'max:255'],
            'og_description_en' => ['nullable', 'string'],
            'og_description_ar' => ['nullable', 'string'],
            'og_image' => ['nullable', 'string', 'max:255'],
            'twitter_title_en' => ['nullable', 'string', 'max:255'],
            'twitter_title_ar' => ['nullable', 'string', 'max:255'],
            'twitter_description_en' => ['nullable', 'string'],
            'twitter_description_ar' => ['nullable', 'string'],
            'twitter_image' => ['nullable', 'string', 'max:255'],
            'schema_type' => ['nullable', 'string', 'max:100'],
            'hreflang_en_url' => ['nullable', 'url', 'max:255'],
            'hreflang_ar_url' => ['nullable', 'url', 'max:255'],
        ]);

        $entry->update($data + [
            'schema_enabled' => $request->boolean('schema_enabled', true),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.seo.meta.index')->with('success', __('admin.seo_meta_updated'));
    }
}
