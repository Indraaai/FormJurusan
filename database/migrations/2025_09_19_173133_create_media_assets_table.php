<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('media_assets', function (Blueprint $table) {
            $table->id();

            // Relasi ke form (agar mudah filter asset per form)
            $table->foreignId('form_id')->constrained('forms')->cascadeOnDelete();

            // Polymorphic manual: form / section / question / answer
            $table->enum('attached_type', ['form', 'section', 'question', 'answer']);
            $table->unsignedBigInteger('attached_id');

            // Jenis media
            $table->enum('type', ['image', 'video', 'audio', 'file']);

            // Lokasi file & metadata
            $table->string('disk', 50)->default('public');
            $table->string('path', 500);
            $table->string('mime', 150)->nullable();
            $table->string('original_name', 255)->nullable();
            $table->char('sha256', 64)->nullable(); // untuk dedup/cek integritas
            $table->integer('size_kb')->nullable();

            // Opsional (untuk image/video)
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->integer('duration_seconds')->nullable();

            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            // Index
            $table->index(['attached_type', 'attached_id'], 'idx_media_attach');
            $table->index('form_id', 'idx_media_form');
            $table->index('sha256', 'idx_media_sha');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_assets');
    }
};
