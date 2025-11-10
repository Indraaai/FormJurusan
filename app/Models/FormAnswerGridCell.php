<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormAnswerGridCell extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'answer_id',
        'row_option_id',
        'col_option_id',
        'selected',
        'row_label_snapshot',
        'col_label_snapshot',
    ];

    protected $casts = [
        'selected' => 'boolean',
    ];

    public function answer()
    {
        return $this->belongsTo(FormAnswer::class, 'answer_id');
    }

    public function row()
    {
        return $this->belongsTo(QuestionOption::class, 'row_option_id');
    }

    public function col()
    {
        return $this->belongsTo(QuestionOption::class, 'col_option_id');
    }
}
