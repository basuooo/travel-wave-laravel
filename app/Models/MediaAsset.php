<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Support\MediaLibraryService;

class MediaAsset extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'alt_text',
        'caption',
        'disk',
        'directory',
        'file_name',
        'path',
        'mime_type',
        'extension',
        'size',
        'width',
        'height',
        'is_favorite',
        'uploaded_by',
    ];

    protected $casts = [
        'is_favorite' => 'boolean',
    ];

    protected $appends = [
        'url',
        'public_url',
    ];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getUrlAttribute(): string
    {
        if (Route::has('admin.media-library.preview')) {
            return route('admin.media-library.preview', $this, false);
        }

        return $this->public_url;
    }

    public function getPublicUrlAttribute(): string
    {
        $disk = $this->disk ?: 'public';
        $path = $this->normalized_path;

        if ($path === '') {
            return '';
        }

        if ($disk === 'public') {
            return '/storage/' . ltrim($path, '/');
        }

        return Storage::disk($disk)->url($path);
    }

    public function getFileExistsAttribute(): bool
    {
        return Storage::disk($this->disk ?: 'public')->exists($this->normalized_path);
    }

    public function getNormalizedPathAttribute(): string
    {
        return MediaLibraryService::normalizePath((string) $this->path);
    }
}
