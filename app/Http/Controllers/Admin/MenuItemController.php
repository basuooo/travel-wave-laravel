<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Page;
use Illuminate\Http\Request;

class MenuItemController extends Controller
{
    public function index(Request $request)
    {
        $location = $request->input('location', 'header');

        $rootItems = MenuItem::with(['children' => fn ($q) => $q->orderBy('sort_order'), 'page'])
            ->where('location', $location)
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();

        $allPages = Page::orderBy('title_en')->get();

        return view('admin.menu-items.index', [
            'location' => $location,
            'items' => $rootItems,
            'allPages' => $allPages,
        ]);
    }

    public function trash()
    {
        return view('admin.menu-items.trash', [
            'items' => MenuItem::onlyTrashed()->with(['parent', 'deletedBy'])->orderByDesc('deleted_at')->paginate(30),
        ]);
    }

    public function create()
    {
        return view('admin.menu-items.form', [
            'item' => new MenuItem(['location' => 'header', 'type' => 'page', 'target' => '_self', 'is_active' => true]),
            'parents' => MenuItem::whereNull('parent_id')->orderBy('location')->orderBy('sort_order')->get(),
            'pages' => Page::orderBy('title_en')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        // Auto-fill titles if linked to page and titles are empty
        if ($data['type'] === 'page' && ! empty($data['page_id'])) {
            $page = Page::find($data['page_id']);
            if ($page) {
                $data['title_en'] = $data['title_en'] ?: $page->title_en;
                $data['title_ar'] = $data['title_ar'] ?: $page->title_ar;
            }
        }

        if (empty($data['sort_order'])) {
            $maxOrder = MenuItem::where('location', $data['location'])->where('parent_id', $data['parent_id'] ?? null)->max('sort_order') ?? 0;
            $data['sort_order'] = $maxOrder + 1;
        }

        MenuItem::create($data);

        return redirect()->route('admin.menu-items.index', ['location' => $data['location']])->with('success', 'تم إنشاء عنصر القائمة بنجاح.');
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
            'pages' => Page::orderBy('title_en')->get(),
        ]);
    }

    public function update(Request $request, MenuItem $menu_item)
    {
        $data = $this->validatedData($request);

        if ($data['type'] === 'page' && ! empty($data['page_id'])) {
            $page = Page::find($data['page_id']);
            if ($page) {
                $data['title_en'] = $data['title_en'] ?: $page->title_en;
                $data['title_ar'] = $data['title_ar'] ?: $page->title_ar;
            }
        }

        $menu_item->update($data);

        return redirect()->route('admin.menu-items.index', ['location' => $menu_item->location])->with('success', 'تم تحديث عنصر القائمة بنجاح.');
    }

    public function duplicate(MenuItem $menu_item)
    {
        $copy = $menu_item->replicate();
        $copy->title_en = trim($menu_item->title_en . ' Copy');
        $copy->title_ar = trim($menu_item->title_ar . ' - نسخة');
        $copy->sort_order = $menu_item->sort_order + 1;
        $copy->is_active = false;
        $copy->save();

        // Duplicate child submenus if present
        foreach ($menu_item->children as $child) {
            $childCopy = $child->replicate();
            $childCopy->parent_id = $copy->id;
            $childCopy->save();
        }

        return redirect()->route('admin.menu-items.index', ['location' => $copy->location])
            ->with('success', 'تم تكرار عنصر القائمة بنجاح.');
    }

    public function destroy(MenuItem $menu_item)
    {
        $location = $menu_item->location;
        $menu_item->forceFill(['deleted_by' => auth()->id()])->save();
        $menu_item->delete();

        return redirect()->route('admin.menu-items.index', ['location' => $location])->with('success', 'تم نقل عنصر القائمة إلى سلة المهملات.');
    }

    public function restore(int $menu_item)
    {
        $item = MenuItem::onlyTrashed()->findOrFail($menu_item);
        $item->restore();
        $item->forceFill(['deleted_by' => null])->save();

        return redirect()->route('admin.menu-items.trash')->with('success', 'تم استعادة عنصر القائمة بنجاح.');
    }

    public function forceDestroy(int $menu_item)
    {
        $item = MenuItem::onlyTrashed()->findOrFail($menu_item);
        $item->forceDelete();

        return redirect()->route('admin.menu-items.trash')->with('success', 'تم حذف عنصر القائمة نهائياً.');
    }

    public function reorder(Request $request)
    {
        $orderData = $request->input('order', []);
        if (is_array($orderData)) {
            foreach ($orderData as $index => $itemData) {
                $itemId = is_array($itemData) ? ($itemData['id'] ?? null) : $itemData;
                $parentId = is_array($itemData) ? ($itemData['parent_id'] ?? null) : null;
                if ($itemId) {
                    MenuItem::where('id', $itemId)->update([
                        'sort_order' => $index + 1,
                        'parent_id' => $parentId ?: null,
                    ]);
                }
            }
        }

        return response()->json(['status' => 'success', 'message' => 'تم حفظ الترتيب الجديد بنجاح.']);
    }

    protected function validatedData(Request $request): array
    {
        $data = $request->validate([
            'location' => ['required', 'in:header,footer'],
            'parent_id' => ['nullable'],
            'type' => ['required', 'in:page,custom,section,submenu'],
            'page_id' => ['nullable', 'exists:pages,id'],
            'title_en' => ['required_without:title_ar', 'nullable', 'string', 'max:255'],
            'title_ar' => ['required_without:title_en', 'nullable', 'string', 'max:255'],
            'url' => ['nullable', 'string', 'max:500'],
            'route_name' => ['nullable', 'string', 'max:255'],
            'target' => ['nullable', 'string', 'max:20'],
            'icon' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $data['parent_id'] = $request->filled('parent_id') ? (int) $request->input('parent_id') : null;
        $data['page_id'] = $request->filled('page_id') ? (int) $request->input('page_id') : null;
        $data['title_en'] = $data['title_en'] ?? $data['title_ar'] ?? '';
        $data['title_ar'] = $data['title_ar'] ?? $data['title_en'] ?? '';
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
