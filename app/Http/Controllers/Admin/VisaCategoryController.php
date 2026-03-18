<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\HandlesCmsData;
use App\Http\Controllers\Controller;
use App\Models\VisaCategory;
use Illuminate\Http\Request;

class VisaCategoryController extends Controller
{
    use HandlesCmsData;

    public function index()
    {
        return view('admin.visa-categories.index', [
            'items' => VisaCategory::orderBy('sort_order')->paginate(15),
        ]);
    }

    public function create()
    {
        return view('admin.visa-categories.form', ['item' => new VisaCategory()]);
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        $data['image'] = $this->uploadFile($request, 'image', 'visa-categories');
        $data['is_active'] = $request->boolean('is_active');
        $data['is_featured'] = $request->boolean('is_featured');
        VisaCategory::create($data);

        return redirect()->route('admin.visa-categories.index')->with('success', 'Visa category created.');
    }

    public function show(VisaCategory $visaCategory)
    {
        return redirect()->route('admin.visa-categories.edit', $visaCategory);
    }

    public function edit(VisaCategory $visa_category)
    {
        return view('admin.visa-categories.form', ['item' => $visa_category]);
    }

    public function update(Request $request, VisaCategory $visa_category)
    {
        $data = $this->validatedData($request, $visa_category->id);
        $data['image'] = $this->uploadFile($request, 'image', 'visa-categories', $visa_category->image);
        $data['is_active'] = $request->boolean('is_active');
        $data['is_featured'] = $request->boolean('is_featured');
        $visa_category->update($data);

        return redirect()->route('admin.visa-categories.index')->with('success', 'Visa category updated.');
    }

    public function destroy(VisaCategory $visa_category)
    {
        $visa_category->delete();

        return back()->with('success', 'Visa category deleted.');
    }

    protected function validatedData(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'name_en' => ['required', 'string', 'max:255'],
            'name_ar' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:visa_categories,slug,' . $id],
            'short_description_en' => ['nullable', 'string'],
            'short_description_ar' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer'],
            'image' => ['nullable', 'image'],
        ]);
    }
}
