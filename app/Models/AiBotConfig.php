<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiBotConfig extends Model
{
    protected $table = 'ai_bot_configs';

    protected $fillable = [
        'key',
        'name',
        'enabled',
        'provider',
        'system_prompt_ar',
        'system_prompt_en',
        'openai_api_key',
        'openai_model',
        'gemini_api_key',
        'gemini_model',
        'deepseek_api_key',
        'deepseek_model',
        'claude_api_key',
        'claude_model',
        'max_tokens',
        'temperature',
        'fallback_to_keyword',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'fallback_to_keyword' => 'boolean',
        'max_tokens' => 'integer',
        'temperature' => 'float',
    ];

    public static function getDefault(): self
    {
        return self::firstOrCreate(['key' => 'default'], [
            'enabled' => false,
            'provider' => 'openai',
            'openai_model' => 'gpt-4o-mini',
            'gemini_model' => 'gemini-1.5-flash',
            'deepseek_model' => 'deepseek-chat',
            'claude_model' => 'claude-3-haiku-20240307',
            'max_tokens' => 1000,
            'temperature' => 0.7,
            'fallback_to_keyword' => true,
        ]);
    }
}
