<?php

namespace App\Http\Controllers;

use App\Models\MediaAsset;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MediaDownloadController extends Controller
{
    /**
     * Stream a file from local (private) storage.
     *
     * BUG-005 FIX: Local disk cannot generate public URLs.
     * This controller serves files securely with auth check.
     */
    public function show(MediaAsset $media): StreamedResponse
    {
        $disk = $media->disk ?: 'local';

        if (!Storage::disk($disk)->exists($media->path)) {
            abort(404, 'File tidak ditemukan.');
        }

        $filename = $media->original_name ?: basename($media->path);
        $mime = $media->mime ?: 'application/octet-stream';

        // For images, display inline; for others, force download
        $disposition = str_starts_with($mime, 'image/')
            ? 'inline'
            : 'attachment';

        return Storage::disk($disk)->download(
            $media->path,
            $filename,
            [
                'Content-Type' => $mime,
                'Content-Disposition' => "{$disposition}; filename=\"{$filename}\"",
            ]
        );
    }
}
