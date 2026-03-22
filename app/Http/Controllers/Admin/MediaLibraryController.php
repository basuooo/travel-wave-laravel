<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MediaAsset;
use App\Support\MediaLibraryService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class MediaLibraryController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:media.manage');
    }

    public function index(Request $request)
    {
        MediaLibraryService::syncKnownReferences();

        $query = MediaAsset::query()->latest();

        if ($search = trim((string) $request->query('q'))) {
            $query->where(function ($builder) use ($search) {
                $builder->where('title', 'like', '%' . $search . '%')
                    ->orWhere('file_name', 'like', '%' . $search . '%')
                    ->orWhere('path', 'like', '%' . $search . '%');
            });
        }

        if ($extension = trim((string) $request->query('extension'))) {
            $query->where('extension', $extension);
        }

        return view('admin.media-library.index', [
            'items' => $query->paginate(24)->withQueryString(),
            'extensions' => MediaAsset::query()->whereNotNull('extension')->distinct()->orderBy('extension')->pluck('extension'),
        ]);
    }

    public function library(Request $request)
    {
        MediaLibraryService::syncKnownReferences();

        $query = MediaAsset::query()->latest();

        if ($search = trim((string) $request->query('q'))) {
            $query->where(function ($builder) use ($search) {
                $builder->where('title', 'like', '%' . $search . '%')
                    ->orWhere('file_name', 'like', '%' . $search . '%')
                    ->orWhere('path', 'like', '%' . $search . '%');
            });
        }

        return response()->json([
            'items' => $query->limit(60)->get()->map(function (MediaAsset $asset) {
                return [
                    'id' => $asset->id,
                    'title' => $asset->title,
                    'file_name' => $asset->file_name,
                    'path' => $asset->path,
                    'url' => $asset->url,
                    'public_url' => $asset->public_url,
                    'exists' => $asset->file_exists,
                    'mime_type' => $asset->mime_type,
                    'extension' => $asset->extension,
                    'size' => $asset->size,
                    'dimensions' => trim(implode(' × ', array_filter([$asset->width, $asset->height]))),
                    'uploaded_at' => optional($asset->created_at)->format('Y-m-d H:i'),
                    'usage' => MediaLibraryService::usageReferences($asset->path),
                ];
            })->values(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'files' => ['required', 'array'],
            'files.*' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,svg'],
        ]);

        $assets = collect($data['files'])->map(function ($file) {
            $path = $file->store('media-library', 'public');
            $asset = MediaLibraryService::registerUploadedFile($file, $path, 'media-library');

            return [
                'id' => $asset?->id,
                'title' => $asset?->title,
                'file_name' => $asset?->file_name,
                'path' => $asset?->path ?: MediaLibraryService::normalizePath($path),
                'url' => $asset?->url ?: Storage::disk('public')->url(MediaLibraryService::normalizePath($path)),
            ];
        })->values();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => __('admin.media_uploaded'),
                'items' => $assets,
            ]);
        }

        return back()->with('success', __('admin.media_uploaded'));
    }

    public function update(Request $request, MediaAsset $media_library)
    {
        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'alt_text' => ['nullable', 'string', 'max:255'],
            'caption' => ['nullable', 'string'],
            'is_favorite' => ['nullable', 'boolean'],
        ]);

        $media_library->update([
            'title' => $data['title'] ?? null,
            'alt_text' => $data['alt_text'] ?? null,
            'caption' => $data['caption'] ?? null,
            'is_favorite' => $request->boolean('is_favorite'),
        ]);

        return back()->with('success', __('admin.media_updated'));
    }

    public function destroy(MediaAsset $media_library)
    {
        if (MediaLibraryService::pathInUse($media_library->path)) {
            return back()->withErrors(__('admin.media_delete_blocked'));
        }

        if (Storage::disk($media_library->disk ?: 'public')->exists($media_library->normalized_path)) {
            Storage::disk($media_library->disk ?: 'public')->delete($media_library->normalized_path);
        }

        $media_library->delete();

        return back()->with('success', __('admin.media_deleted'));
    }

    public function preview(MediaAsset $media_library)
    {
        $disk = Storage::disk($media_library->disk ?: 'public');

        if ($disk->exists($media_library->normalized_path)) {
            return $disk->response($media_library->normalized_path, null, [
                'Cache-Control' => 'public, max-age=86400',
            ]);
        }

        $placeholder = <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" width="640" height="420" viewBox="0 0 640 420" fill="none">
  <rect width="640" height="420" rx="28" fill="#F5F6FB"/>
  <rect x="88" y="92" width="464" height="236" rx="20" fill="#FFFFFF" stroke="#E7E9F2" stroke-width="2"/>
  <circle cx="188" cy="172" r="34" fill="#ECE9FF"/>
  <path d="M166 282L258 204L332 258L392 214L474 282H166Z" fill="#EDEFFD"/>
  <text x="320" y="360" text-anchor="middle" fill="#7C859D" font-family="Segoe UI, Arial, sans-serif" font-size="22">Media Preview Unavailable</text>
</svg>
SVG;

        return response($placeholder, Response::HTTP_OK, [
            'Content-Type' => 'image/svg+xml; charset=UTF-8',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
        ]);
    }
}
