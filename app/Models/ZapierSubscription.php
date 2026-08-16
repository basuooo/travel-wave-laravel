<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ZapierSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'event',
        'target_url',
        'is_active',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Ensure zapier_subscriptions table exists in database automatically.
     */
    public static function ensureTableExists(): bool
    {
        try {
            if (! Schema::hasTable('zapier_subscriptions')) {
                Schema::create('zapier_subscriptions', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('user_id')->nullable();
                    $table->string('event');
                    $table->text('target_url');
                    $table->boolean('is_active')->default(true);
                    $table->timestamps();

                    $table->index(['event', 'is_active']);
                });

                return true;
            }
        } catch (\Throwable $e) {
            // Log or ignore if table creation fails due to permissions
        }

        return false;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
