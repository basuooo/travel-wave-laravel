<?php

namespace App\Models;

use App\Support\HasLocalizedContent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class MenuItem extends Model
{
    use HasFactory;
    use HasLocalizedContent;
    use SoftDeletes;

    protected $fillable = [
        'location',
        'parent_id',
        'type',
        'page_id',
        'title_en',
        'title_ar',
        'url',
        'route_name',
        'target',
        'icon',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order');
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'page_id');
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function frontendUrl(): ?string
    {
        if (! $this->is_active || $this->trashed()) {
            return '#';
        }

        // Type A: Linked to an existing Page model
        if ($this->type === 'page' || filled($this->page_id)) {
            $linkedPage = $this->page ?? Page::find($this->page_id);
            if ($linkedPage) {
                return $linkedPage->frontendUrl() ?: ($linkedPage->slug ? route('pages.show', $linkedPage->slug) : '#');
            }
        }

        // Type C: Anchor / Section link
        if ($this->type === 'section' && filled($this->url)) {
            $anchor = Str::startsWith($this->url, '#') ? $this->url : '#' . ltrim($this->url, '#');
            if ($this->page_id && ($linkedPage = $this->page ?? Page::find($this->page_id))) {
                $baseUrl = $linkedPage->frontendUrl() ?: route('home');
                return $baseUrl . $anchor;
            }
            return route('home') . $anchor;
        }

        // Type B: Custom URL
        if (filled($this->url)) {
            return $this->url;
        }

        if (filled($this->route_name) && Route::has($this->route_name)) {
            return route($this->route_name);
        }

        return '#';
    }
}
