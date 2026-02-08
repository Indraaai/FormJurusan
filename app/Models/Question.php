<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    // Tipe pertanyaan yang didukung
    public const TYPES = [
        'short_text',
        'long_text',
        'number',        // BUG-014 FIX: Tambahkan tipe yang dipakai di view
        'email',         // BUG-014 FIX: Tambahkan tipe yang dipakai di view
        'url',           // BUG-014 FIX: Tambahkan tipe yang dipakai di view
        'multiple_choice',
        'checkboxes',
        'dropdown',
        'file_upload',
        'linear_scale',
        'mc_grid',
        'checkbox_grid',
        'date',
        'time',
        'datetime',      // BUG-014 FIX: Tambahkan tipe yang dipakai di view
    ];

    protected $fillable = [
        'section_id',
        'type',
        'title',
        'description',
        'required',
        'position',
        'shuffle_options',
        'other_option_enabled',
        'settings',
    ];

    protected $casts = [
        'required' => 'boolean',
        'shuffle_options' => 'boolean',
        'other_option_enabled' => 'boolean',
        'settings' => 'array',
    ];

    public function section()
    {
        return $this->belongsTo(FormSection::class, 'section_id');
    }

    public function options()
    {
        return $this->hasMany(QuestionOption::class)->orderBy('position');
    }

    public function validations()
    {
        return $this->hasMany(QuestionValidation::class);
    }

    public function answers()
    {
        return $this->hasMany(FormAnswer::class);
    }
    // app/Models/Question.php

}
