<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionOption extends Model
{
    // role: opsi biasa, atau elemen grid (row/column)
    public const ROLES = ['option', 'row', 'column'];

    protected $fillable = [
        'question_id',
        'label',
        'value',
        'position',
        'role',
        'is_other',
    ];

    protected $casts = [
        'is_other' => 'boolean',
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
