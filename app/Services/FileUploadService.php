<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class FileUploadService
{
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx'];
    private const ALLOWED_MIMES = [
        'image/jpeg',
        'image/png',
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
    ];
    private const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB
    private const ALLOWED_DIRECTORIES = ['form_uploads', 'temp_uploads'];

    public function uploadFile(UploadedFile $file, string $directory = 'form_uploads'): string
    {
        // Validate directory to prevent path traversal
        if (!in_array($directory, self::ALLOWED_DIRECTORIES)) {
            throw new \InvalidArgumentException('Invalid upload directory');
        }

        // Sanitize directory name (remove any path traversal attempts)
        $directory = str_replace(['..', '/', '\\'], '', $directory);

        // Validate file
        $this->validateFile($file);

        // Generate secure filename
        $filename = $this->generateSecureFilename($file);

        // Store file with local disk (private storage)
        $path = $file->storeAs($directory, $filename, 'local');

        // Verify file was stored successfully
        if (!Storage::disk('local')->exists($path)) {
            throw new \RuntimeException('File upload failed - file not stored');
        }

        // Optimize image jika file adalah gambar
        if ($this->isImage($file)) {
            $fullPath = Storage::disk('local')->path($path);
            $this->optimizeImage($fullPath);
        }

        return $path;
    }

    private function validateFile(UploadedFile $file): void
    {
        // Check if file is a valid upload
        if (!$file->isValid()) {
            throw new \InvalidArgumentException('File upload gagal atau corrupt');
        }

        // Check file size
        if ($file->getSize() > self::MAX_FILE_SIZE) {
            throw new \InvalidArgumentException('File terlalu besar. Maksimal 5MB');
        }

        // Check extension
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, self::ALLOWED_EXTENSIONS)) {
            throw new \InvalidArgumentException('Tipe file tidak diizinkan. Hanya: ' . implode(', ', self::ALLOWED_EXTENSIONS));
        }

        // Check MIME type (more secure than extension check)
        $mimeType = $file->getMimeType();
        if (!in_array($mimeType, self::ALLOWED_MIMES)) {
            throw new \InvalidArgumentException('MIME type file tidak valid');
        }

        // Additional check: verify file content matches declared MIME
        $this->validateFileContent($file);
    }

    private function validateFileContent(UploadedFile $file): void
    {
        // Use finfo to detect actual file type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $detectedMime = finfo_file($finfo, $file->getRealPath());
        finfo_close($finfo);

        // Check if detected MIME matches uploaded file MIME
        if ($detectedMime !== $file->getMimeType()) {
            throw new \InvalidArgumentException('File content tidak sesuai dengan ekstensi file');
        }
    }

    private function generateSecureFilename(UploadedFile $file): string
    {
        // Use ULID for unique filename + keep original extension
        $extension = strtolower($file->getClientOriginalExtension());
        return Str::ulid() . '.' . $extension;
    }

    private function isImage(UploadedFile $file): bool
    {
        return in_array(
            strtolower($file->getClientOriginalExtension()),
            ['jpg', 'jpeg', 'png']
        );
    }

    private function optimizeImage(string $path): void
    {
        try {
            $manager = new ImageManager(new Driver());
            $image = $manager->read($path);

            // Resize if too large (max 1920px on longest side)
            if ($image->width() > 1920 || $image->height() > 1920) {
                $image->scale(width: 1920);
            }

            // Save with optimized quality
            $image->save($path, quality: 85);
        } catch (\Exception $e) {
            // Log error but don't fail the upload
            \Illuminate\Support\Facades\Log::warning('Failed to optimize image', [
                'path' => $path,
                'error' => $e->getMessage()
            ]);
        }
    }
}
