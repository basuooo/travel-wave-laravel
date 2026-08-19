<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FunnelResponseAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'response_id',
        'element_id',
        'question_label',
        'answer_value',
        'score_given',
    ];

    protected $casts = [
        'score_given' => 'integer',
    ];

    public function response()
    {
        return $this->belongsTo(FunnelResponse::class, 'response_id');
    }

    public function element()
    {
        return $this->belongsTo(FunnelElement::class, 'element_id');
    }
}
