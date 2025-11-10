<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Form extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uid',
        'title',
        'description',
        'created_by',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function getRouteKeyName(): string
    {
        return 'uid'; // Route model binding pakai {form:uid}
    }
    protected static function booted()
    {
        static::creating(function ($m) {
            if (!$m->uid) $m->uid = (string) Str::ulid();
        });
    }

    // ===== Relations
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function settings()   // <= hasOne ke tabel form_settings
    {
        return $this->hasOne(\App\Models\FormSetting::class);
    }
    public function sections()
    {
        return $this->hasMany(FormSection::class)->orderBy('position');
    }

    public function questions()
    {
        // akses cepat seluruh pertanyaan via sections
        return $this->hasManyThrough(Question::class, FormSection::class, 'form_id', 'section_id');
    }

    public function logicRules()
    {
        return $this->hasMany(FormLogicRule::class);
    }

    public function mediaAssets()
    {
        return $this->hasMany(MediaAsset::class);
    }

    public function responses()
    {
        return $this->hasMany(FormResponse::class);
    }
    public function getRouteKey()
    {
        return $this->uid;
    }
}
