<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class CrmLeadWhatsApp extends Model
{
    use HasFactory;

    protected $table = 'crm_lead_whatsapps';

    protected $fillable = [
        'inquiry_id',
        'user_id',
        'log_number',
        'whatsapp_status',
        'comment',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public static function ensureTableExists(): void
    {
        if (!Schema::hasTable('crm_lead_whatsapps')) {
            try {
                Schema::create('crm_lead_whatsapps', function (Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('inquiry_id')->index();
                    $table->unsignedBigInteger('user_id')->nullable()->index();
                    $table->integer('log_number')->default(1);
                    $table->string('whatsapp_status');
                    $table->text('comment')->nullable();
                    $table->timestamps();

                    $table->foreign('inquiry_id')->references('id')->on('inquiries')->onDelete('cascade');
                    $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
                });
            } catch (\Throwable $e) {}
        }
    }

    public function inquiry(): BelongsTo
    {
        return $this->belongsTo(Inquiry::class, 'inquiry_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Formatted 12-hour AM/PM creation time
     */
    public function getFormattedCreatedAtAttribute(): string
    {
        return $this->created_at ? $this->created_at->format('Y-m-d h:i A') : '-';
    }
}
