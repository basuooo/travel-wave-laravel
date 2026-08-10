<?php

namespace App\Models\PopupManager;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PopupAnalytic extends Model
{
    use HasFactory;

    protected $table = 'popup_analytics';

    public const EVENT_IMPRESSION = 'impression';
    public const EVENT_CLICK = 'click';
    public const EVENT_CONVERSION = 'conversion';
    public const EVENT_CLOSE = 'close';

    protected $fillable = [
        'popup_id',
        'event_type',
        'page_url',
        'device',
        'utm_source',
        'utm_campaign',
        'ip_address',
        'user_agent',
    ];

    protected static function booted(): void
    {
        static::ensureTableSchema();
    }

    public static function ensureTableSchema(): void
    {
        if (Schema::hasTable('popup_analytics')) {
            return;
        }

        Schema::create('popup_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('popup_id')->constrained('popups')->cascadeOnDelete();
            $table->string('event_type');
            $table->text('page_url')->nullable();
            $table->string('device')->nullable();
            $table->string('utm_source')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });
    }

    public function popup(): BelongsTo
    {
        return $this->belongsTo(Popup::class, 'popup_id');
    }
}
