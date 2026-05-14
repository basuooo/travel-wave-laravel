<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappConfig extends Model
{
    protected $table = 'whatsapp_configs';

    protected $fillable = [
        'enabled',
        'access_token',
        'phone_number_id',
        'business_account_id',
        'verify_token',
        'handover_keyword',
        'human_handover_enabled',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'human_handover_enabled' => 'boolean',
    ];

    public static function get(): self
    {
        return self::firstOrCreate(['id' => 1]);
    }
}
