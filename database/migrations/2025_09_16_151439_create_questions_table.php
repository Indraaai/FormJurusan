<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained('form_sections')->cascadeOnDelete();
            $table->enum('type', [
                'short_text',
                'long_text',
                'multiple_choice',
                'checkboxes',
                'dropdown',
                'file_upload',
                'linear_scale',
                'mc_grid',
                'checkbox_grid',
                'date',
                'time'
            ]);
            $table->text('title');
            $table->text('description')->nullable();
            $table->boolean('required')->default(false);
            $table->integer('position');
            $table->boolean('shuffle_options')->default(false);
            $table->boolean('other_option_enabled')->default(false);
            $table->json('settings')->nullable(); // skala min/max, label, dll.
            $table->timestamps();

            $table->index(['section_id', 'position'], 'idx_question_section_pos');
            $table->index('type', 'idx_question_type');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
