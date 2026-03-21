<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SeoRedirect;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SeoRedirectController extends Controller
{
    public function index()
    {
        return view('admin.seo.redirects.index', [
            'items' => SeoRedirect::query()->latest('id')->paginate(20),
        ]);
    }

    public function create()
    {
        return view('admin.seo.redirects.form', [
            'item' => new SeoRedirect([
                'redirect_type' => 301,
                'is_active' => true,
            ]),
            'isEdit' => false,
        ]);
    }

    public function store(Request $request)
    {
        SeoRedirect::query()->create($this->validatedData($request));

        return redirect()->route('admin.seo.redirects.index')->with('success', __('admin.seo_redirect_saved'));
    }

    public function edit(SeoRedirect $redirect)
    {
        return view('admin.seo.redirects.form', [
            'item' => $redirect,
            'isEdit' => true,
        ]);
    }

    public function update(Request $request, SeoRedirect $redirect)
    {
        $redirect->update($this->validatedData($request, $redirect->id));

        return redirect()->route('admin.seo.redirects.index')->with('success', __('admin.seo_redirect_updated'));
    }

    public function destroy(SeoRedirect $redirect)
    {
        $redirect->delete();

        return redirect()->route('admin.seo.redirects.index')->with('success', __('admin.seo_redirect_deleted'));
    }

    protected function validatedData(Request $request, ?int $id = null): array
    {
        $data = $request->validate([
            'source_path' => ['required', 'string', 'max:255', Rule::unique('seo_redirects', 'source_path')->ignore($id)],
            'destination_url' => ['required', 'string', 'max:255'],
            'redirect_type' => ['required', 'in:301,302'],
            'notes' => ['nullable', 'string'],
        ]);

        return [
            'source_path' => '/' . ltrim($data['source_path'], '/'),
            'destination_url' => $data['destination_url'],
            'redirect_type' => (int) $data['redirect_type'],
            'notes' => $data['notes'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ];
    }
}
