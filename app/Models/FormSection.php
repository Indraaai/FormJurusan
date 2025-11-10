<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormSection extends Model
{
    protected $fillable = [
        'form_id',
        'title',
        'description',
        'position',
    ];

    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class, 'section_id')->orderBy('position');
    }
}
