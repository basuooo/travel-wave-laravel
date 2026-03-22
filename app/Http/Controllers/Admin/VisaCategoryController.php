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

    public function trash()
    {
        return view('admin.visa-categories.trash', [
            'items' => VisaCategory::onlyTrashed()->with('deletedBy')->orderByDesc('deleted_at')->paginate(15),
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

    public function duplicate(VisaCategory $visa_category)
    {
        $copy = $visa_category->replicate();
        $copy->name_en = trim($visa_category->name_en . ' Copy');
        $copy->name_ar = trim($visa_category->name_ar . ' - نسخة');
        $copy->slug = VisaCategory::makeUniqueSlug(($visa_category->slug ?: $visa_category->name_en) . '-copy');
        $copy->is_active = false;
        $copy->is_featured = false;
        $copy->save();

        return redirect()->route('admin.visa-categories.edit', $copy)->with('success', 'Visa category duplicated.');
    }

    public function destroy(VisaCategory $visa_category)
    {
        $visa_category->forceFill(['deleted_by' => auth()->id()])->save();
        $visa_category->delete();

        return redirect()->route('admin.visa-categories.index')->with('success', 'Visa category moved to trash.');
    }

    public function restore(int $visa_category)
    {
        $item = VisaCategory::onlyTrashed()->findOrFail($visa_category);
        $item->restore();
        $item->forceFill(['deleted_by' => null])->save();

        return redirect()->route('admin.visa-categories.trash')->with('success', 'Visa category restored.');
    }

    public function forceDestroy(int $visa_category)
    {
        $item = VisaCategory::onlyTrashed()->findOrFail($visa_category);

        if ($item->countries()->withTrashed()->exists()) {
            return redirect()->route('admin.visa-categories.trash')
                ->withErrors('Delete or reassign the visa countries in this category before permanently deleting it.');
        }

        $item->forceDelete();

        return redirect()->route('admin.visa-categories.trash')->with('success', 'Visa category deleted permanently.');
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
