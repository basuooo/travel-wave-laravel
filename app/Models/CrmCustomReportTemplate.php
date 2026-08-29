<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class CrmCustomReportTemplate extends Model
{
    use HasFactory;

    protected $table = 'crm_custom_report_templates';

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'entity_type',
        'selected_columns',
        'filters',
        'group_by',
        'chart_type',
        'is_shared',
    ];

    protected $casts = [
        'selected_columns' => 'array',
        'filters' => 'array',
        'is_shared' => 'boolean',
        'user_id' => 'integer',
    ];

    public static function ensureTableExists(): void
    {
        if (!Schema::hasTable('crm_custom_report_templates')) {
            Schema::create('crm_custom_report_templates', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('title');
                $table->text('description')->nullable();
                $table->string('entity_type')->default('inquiries');
                $table->json('selected_columns')->nullable();
                $table->json('filters')->nullable();
                $table->string('group_by')->nullable();
                $table->string('chart_type')->default('none');
                $table->boolean('is_shared')->default(true);
                $table->timestamps();
            });
        }
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
