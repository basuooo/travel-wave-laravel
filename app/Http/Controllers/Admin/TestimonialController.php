<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\HandlesCmsData;
use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    use HandlesCmsData;

    public function index()
    {
        return view('admin.testimonials.index', [
            'items' => Testimonial::orderBy('sort_order')->paginate(15),
        ]);
    }

    public function trash()
    {
        return view('admin.testimonials.trash', [
            'items' => Testimonial::onlyTrashed()->with('deletedBy')->orderByDesc('deleted_at')->paginate(15),
        ]);
    }

    public function create()
    {
        return view('admin.testimonials.form', ['item' => new Testimonial()]);
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        $data['image'] = $this->uploadFile($request, 'image', 'testimonials');
        $data['is_active'] = $request->boolean('is_active');
        Testimonial::create($data);

        return redirect()->route('admin.testimonials.index')->with('success', 'Testimonial created.');
    }

    public function show(Testimonial $testimonial)
    {
        return redirect()->route('admin.testimonials.edit', $testimonial);
    }

    public function edit(Testimonial $testimonial)
    {
        return view('admin.testimonials.form', ['item' => $testimonial]);
    }

    public function update(Request $request, Testimonial $testimonial)
    {
        $data = $this->validatedData($request);
        $data['image'] = $this->uploadFile($request, 'image', 'testimonials', $testimonial->image);
        $data['is_active'] = $request->boolean('is_active');
        $testimonial->update($data);

        return redirect()->route('admin.testimonials.index')->with('success', 'Testimonial updated.');
    }

    public function duplicate(Testimonial $testimonial)
    {
        $copy = $testimonial->replicate();
        $copy->client_name = trim($testimonial->client_name . ' Copy');
        $copy->is_active = false;
        $copy->save();

        return redirect()->route('admin.testimonials.edit', $copy)->with('success', 'Testimonial duplicated.');
    }

    public function destroy(Testimonial $testimonial)
    {
        $testimonial->forceFill(['deleted_by' => auth()->id()])->save();
        $testimonial->delete();

        return redirect()->route('admin.testimonials.index')->with('success', 'Testimonial moved to trash.');
    }

    public function restore(int $testimonial)
    {
        $item = Testimonial::onlyTrashed()->findOrFail($testimonial);
        $item->restore();
        $item->forceFill(['deleted_by' => null])->save();

        return redirect()->route('admin.testimonials.trash')->with('success', 'Testimonial restored.');
    }

    public function forceDestroy(int $testimonial)
    {
        $item = Testimonial::onlyTrashed()->findOrFail($testimonial);
        $item->forceDelete();

        return redirect()->route('admin.testimonials.trash')->with('success', 'Testimonial deleted permanently.');
    }

    protected function validatedData(Request $request): array
    {
        return $request->validate([
            'client_name' => ['required', 'string', 'max:255'],
            'client_role_en' => ['nullable', 'string', 'max:255'],
            'client_role_ar' => ['nullable', 'string', 'max:255'],
            'testimonial_en' => ['required', 'string'],
            'testimonial_ar' => ['required', 'string'],
            'rating' => ['nullable', 'integer', 'between:1,5'],
            'sort_order' => ['nullable', 'integer'],
            'image' => ['nullable', 'image'],
        ]);
    }
}
