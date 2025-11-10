<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormResponse extends Model
{
    use SoftDeletes;

    public const STATUSES = ['draft', 'submitted'];

    protected $fillable = [
        'uid',
        'form_id',
        'status',
        'started_at',
        'submitted_at',
        'duration_seconds',
        'respondent_user_id',
        'respondent_email',
        'edit_token',
        'source_ip',
        'user_agent',
        'is_spam',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'duration_seconds' => 'integer',
        'is_spam' => 'boolean',
    ];

    public function getRouteKeyName(): string
    {
        return 'uid'; // Route model binding pakai {response:uid}
    }

    // ===== Relations
    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    public function respondent()
    {
        return $this->belongsTo(User::class, 'respondent_user_id');
    }

    public function answers()
    {
        return $this->hasMany(FormAnswer::class, 'response_id');
    }
}
