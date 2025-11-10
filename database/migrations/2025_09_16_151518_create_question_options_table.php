<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('question_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('questions')->cascadeOnDelete();
            $table->text('label');
            $table->string('value', 255)->nullable();
            $table->integer('position');
            $table->enum('role', ['option', 'row', 'column'])->default('option');
            $table->boolean('is_other')->default(false);
            $table->timestamps();

            $table->index(['question_id', 'position'], 'idx_qopt_question_pos');
            $table->index('role', 'idx_qopt_role');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('question_options');
    }
};
