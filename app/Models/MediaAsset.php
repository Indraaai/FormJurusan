<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class MediaAsset extends Model
{
    public const TYPES = ['image', 'video', 'audio', 'file'];
    //  tambahkan 'answer'
    public const ATTACHED_TYPES = ['form', 'section', 'question', 'answer'];

    protected $fillable = [
        'form_id',
        'attached_type',
        'attached_id',
        'type',
        'disk',
        'path',
        'mime',
        'original_name',
        'sha256',
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
            'answer'   => FormAnswer::find($this->attached_id),
            default    => null,
        };
    }

    /**
     * Get download/view URL for the file.
     *
     * BUG-005 FIX: Local disk does not support url().
     * Use signed route for secure file download instead.
     */
    public function getUrlAttribute(): ?string
    {
        if (!$this->path) {
            return null;
        }

        $disk = $this->disk ?: config('filesystems.default', 'public');

        // Local disk doesn't support public URLs — use download route
        if ($disk === 'local') {
            return route('media.download', ['media' => $this->id]);
        }

        // Public disk can generate URLs directly
        return Storage::disk($disk)->url($this->path);
    }
}
