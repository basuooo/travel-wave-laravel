<?php

namespace App\Models;

use App\Support\HasLocalizedContent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Route;

class MenuItem extends Model
{
    use HasFactory;
    use HasLocalizedContent;
    use SoftDeletes;

    protected $fillable = [
        'location',
        'parent_id',
        'title_en',
        'title_ar',
        'url',
        'route_name',
        'target',
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

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function frontendUrl(): ?string
    {
        if (! $this->is_active || $this->trashed()) {
            return null;
        }

        if (filled($this->url)) {
            return $this->url;
        }

        if (filled($this->route_name) && Route::has($this->route_name)) {
            return route($this->route_name);
        }

        return null;
    }
}
