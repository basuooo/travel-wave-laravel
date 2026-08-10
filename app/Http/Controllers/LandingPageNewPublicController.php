<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use App\Models\LandingPageNew\LpNewLandingPage;

class LandingPageNewPublicController extends Controller
{
    public function show(string $slug)
    {
        LpNewLandingPage::ensureTableSchema();

        $page = LpNewLandingPage::where('slug', $slug)->firstOrFail();

        if (! $page->is_active || $page->status === LpNewLandingPage::STATUS_ARCHIVED) {
            abort(404);
        }

        $structure = is_string($page->structure) ? json_decode($page->structure, true) : ($page->structure ?? []);

        return view('frontend.landing-pages-new.show', [
            'page' => $page,
            'structure' => $structure,
            'elements' => $structure['elements'] ?? [],
        ]);
    }
}
