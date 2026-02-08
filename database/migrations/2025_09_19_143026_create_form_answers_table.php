<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('form_answers', function (Blueprint $t) {
            $t->id();

            // FK utama
            $t->foreignId('response_id')->constrained('form_responses')->cascadeOnDelete();
            $t->foreignId('question_id')->constrained('questions')->cascadeOnDelete();

            // Nilai serbaguna (isi sesuai tipe pertanyaan)
            $t->text('long_text_value')->nullable();
            $t->string('text_value', 1000)->nullable();
            $t->decimal('number_value', 18, 6)->nullable();
            $t->boolean('boolean_value')->nullable();
            $t->date('date_value')->nullable();
            $t->time('time_value')->nullable();
            $t->dateTime('datetime_value')->nullable();

            // Single-choice (radio/dropdown) — opsional
            $t->foreignId('option_id')->nullable()->constrained('question_options')->nullOnDelete();
            $t->string('option_label_snapshot', 1000)->nullable();

            // Snapshot pertanyaan
            $t->string('question_text_snapshot', 1000)->nullable();

            $t->timestamps();

            // 1 jawaban per (response, question)
            $t->unique(['response_id', 'question_id'], 'uq_form_answer_resp_q');
            $t->index('question_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('form_answers');
    }
};
