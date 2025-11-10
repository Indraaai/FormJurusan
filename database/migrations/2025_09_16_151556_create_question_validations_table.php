<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('question_validations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('questions')->cascadeOnDelete();
            $table->enum('validation_type', [
                'text_length',
                'regex',
                'number_range',
                'date_range',
                'time_range',
                'file_type',
                'file_size',
                'required'
            ]);
            $table->decimal('min_value', 15, 4)->nullable();
            $table->decimal('max_value', 15, 4)->nullable();
            $table->string('pattern', 255)->nullable();
            $table->string('message', 255)->nullable();
            $table->json('extras')->nullable(); // mime types, step, dll.
            $table->timestamps();

            $table->index('question_id', 'idx_qval_question');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('question_validations');
    }
};
