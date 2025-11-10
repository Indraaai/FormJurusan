<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormSetting extends Model
{
    protected $fillable = [
        'form_id',
        'require_sign_in',
        'collect_emails',
        'limit_one_response',
        'allow_edit_after_submit',
        'show_progress_bar',
        'shuffle_question_order',
        'response_receipt_enabled',
        'confirmation_message',
        'start_at',
        'end_at',
        'captcha_enabled',
        'theme',
    ];

    protected $casts = [
        'require_sign_in' => 'boolean',
        'collect_emails' => 'boolean',
        'limit_one_response' => 'boolean',
        'allow_edit_after_submit' => 'boolean',
        'show_progress_bar' => 'boolean',
        'shuffle_question_order' => 'boolean',
        'response_receipt_enabled' => 'boolean',
        'captcha_enabled' => 'boolean',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'theme' => 'array',
    ];

    public function form()
    {
        return $this->belongsTo(Form::class);
    }
}
