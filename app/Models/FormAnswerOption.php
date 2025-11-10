<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormAnswerOption extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'answer_id',
        'option_id',
        'option_label_snapshot',
    ];

    public function answer()
    {
        return $this->belongsTo(FormAnswer::class, 'answer_id');
    }

    public function option()
    {
        return $this->belongsTo(QuestionOption::class, 'option_id');
    }
}
