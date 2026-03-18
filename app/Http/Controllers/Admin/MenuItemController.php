<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use Illuminate\Http\Request;

class MenuItemController extends Controller
{
    public function index()
    {
        return view('admin.menu-items.index', [
            'items' => MenuItem::with('parent')->orderBy('location')->orderBy('sort_order')->paginate(30),
        ]);
    }

    public function create()
    {
        return view('admin.menu-items.form', [
            'item' => new MenuItem(),
            'parents' => MenuItem::whereNull('parent_id')->orderBy('location')->orderBy('sort_order')->get(),
        ]);
    }

    public function store(Request $request)
    {
        MenuItem::create($this->validatedData($request));

        return redirect()->route('admin.menu-items.index')->with('success', 'Menu item created.');
    }

    public function show(MenuItem $menuItem)
    {
        return redirect()->route('admin.menu-items.edit', $menuItem);
    }

    public function edit(MenuItem $menu_item)
    {
        return view('admin.menu-items.form', [
            'item' => $menu_item,
            'parents' => MenuItem::whereNull('parent_id')->where('id', '!=', $menu_item->id)->orderBy('location')->orderBy('sort_order')->get(),
        ]);
    }

    public function update(Request $request, MenuItem $menu_item)
    {
        $menu_item->update($this->validatedData($request));

        return redirect()->route('admin.menu-items.index')->with('success', 'Menu item updated.');
    }

    public function destroy(MenuItem $menu_item)
    {
        $menu_item->delete();

        return back()->with('success', 'Menu item deleted.');
    }

    protected function validatedData(Request $request): array
    {
        $data = $request->validate([
            'location' => ['required', 'in:header,footer'],
            'parent_id' => ['nullable', 'exists:menu_items,id'],
            'title_en' => ['required', 'string', 'max:255'],
            'title_ar' => ['required', 'string', 'max:255'],
            'url' => ['nullable', 'string', 'max:255'],
            'route_name' => ['nullable', 'string', 'max:255'],
            'target' => ['nullable', 'string', 'max:20'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
