<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormLogicRule extends Model
{
    public const OPERATORS = ['=', '!=', 'contains', 'in', 'between', '>=', '<=', 'answered', 'not_answered'];
    public const ACTIONS = ['goto_section', 'submit'];

    protected $fillable = [
        'form_id',
        'source_question_id',
        'operator',
        'value_text',
        'value_number',
        'option_id',
        'target_section_id',
        'action',
        'priority',
        'is_enabled',
    ];

    protected $casts = [
        'value_number' => 'decimal:4',
        'priority' => 'integer',
        'is_enabled' => 'boolean',
    ];

    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    public function sourceQuestion()
    {
        return $this->belongsTo(Question::class, 'source_question_id');
    }

    public function option()
    {
        return $this->belongsTo(QuestionOption::class, 'option_id');
    }

    public function targetSection()
    {
        return $this->belongsTo(FormSection::class, 'target_section_id');
    }
}
