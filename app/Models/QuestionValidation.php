<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionValidation extends Model
{
    public const TYPES = [
        'text_length',
        'regex',
        'number_range',
        'date_range',
        'time_range',
        'file_type',
        'file_size',
        'required',
    ];

    protected $fillable = [
        'question_id',
        'validation_type',
        'min_value',
        'max_value',
        'pattern',
        'message',
        'extras',
    ];

    protected $casts = [
        'min_value' => 'decimal:4',
        'max_value' => 'decimal:4',
        'extras' => 'array',
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
