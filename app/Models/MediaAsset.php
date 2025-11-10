<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class MediaAsset extends Model
{
    public const TYPES = ['image', 'video', 'audio', 'file'];
    // ⬇️ tambahkan 'answer'
    public const ATTACHED_TYPES = ['form', 'section', 'question', 'answer'];

    protected $fillable = [
        'form_id',
        'attached_type',
        'attached_id',
        'type',
        'disk',            // ⬅️ baru
        'path',
        'mime',
        'original_name',   // ⬅️ baru
        'sha256',          // ⬅️ baru
        'size_kb',
        'width',
        'height',
        'duration_seconds',
        'created_by',
    ];

    protected $casts = [
        'size_kb' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
        'duration_seconds' => 'integer',
    ];

    public function form()
    {
        return $this->belongsTo(Form::class);
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Helper opsional untuk mendapatkan entity terlampir (manual)
    public function attached()
    {
        return match ($this->attached_type) {
            'form'     => Form::find($this->attached_id),
            'section'  => FormSection::find($this->attached_id),
            'question' => Question::find($this->attached_id),
            'answer'   => FormAnswer::find($this->attached_id), // ⬅️ baru
            default    => null,
        };
    }

    // URL file (butuh storage:link)
    public function getUrlAttribute(): ?string
    {
        if (!$this->path) {
            return null;
        }
        $disk = $this->disk ?: config('filesystems.default', 'public');
        return Storage::disk($disk)->url($this->path);
    }
}
