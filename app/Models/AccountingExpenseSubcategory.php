<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountingExpenseSubcategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'accounting_expense_category_id',
        'name_ar',
        'name_en',
        'slug',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'accounting_expense_category_id' => 'integer',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(AccountingExpenseCategory::class, 'accounting_expense_category_id');
    }

    public function localizedName(?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        return $locale === 'ar'
            ? ($this->name_ar ?: ($this->name_en ?: $this->slug))
            : ($this->name_en ?: ($this->name_ar ?: $this->slug));
    }
}
