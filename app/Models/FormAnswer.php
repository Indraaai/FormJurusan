<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormAnswer extends Model
{
    protected $fillable = [
        'response_id',
        'question_id',
        'text_value',
        'long_text_value',
        'number_value',
        'boolean_value',
        'date_value',
        'time_value',
        'datetime_value',
        'option_id',
        'question_text_snapshot',
        'option_label_snapshot',
    ];

    protected $casts = [
        'number_value' => 'decimal:6',
        'boolean_value' => 'boolean',
        'date_value' => 'date',
        'time_value' => 'string',
        'datetime_value' => 'datetime',
    ];
    protected $touches = ['response']; // updated_at FormResponse ikut berubah saat jawaban berubah

    public function response()
    {
        return $this->belongsTo(FormResponse::class, 'response_id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function option()
    {
        return $this->belongsTo(QuestionOption::class, 'option_id');
    }

    public function fileMedia()
    {
        // satu file per jawaban (jika mau multi, ganti hasMany)
        return $this->hasOne(MediaAsset::class, 'attached_id')
            ->where('attached_type', 'answer');
    }

    public function selectedOptions()
    {
        return $this->hasMany(FormAnswerOption::class, 'answer_id');
    }

    public function gridCells()
    {
        return $this->hasMany(FormAnswerGridCell::class, 'answer_id');
    }
}
