<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('form_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained('forms')->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->integer('position');
            $table->timestamps();

            $table->index(['form_id', 'position'], 'idx_section_form_pos');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('form_sections');
    }
};
