<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('form_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->unique()->constrained('forms')->cascadeOnDelete();
            $table->boolean('require_sign_in')->default(true);  // respondent wajib login
            $table->boolean('collect_emails')->default(true);   // snapshot email user
            $table->boolean('limit_one_response')->default(false);
            $table->boolean('allow_edit_after_submit')->default(false);
            $table->boolean('show_progress_bar')->default(true);
            $table->boolean('shuffle_question_order')->default(false);
            $table->boolean('response_receipt_enabled')->default(false);
            $table->string('confirmation_message', 500)->nullable();
            $table->dateTime('start_at')->nullable();
            $table->dateTime('end_at')->nullable();
            $table->boolean('captcha_enabled')->default(false);
            $table->json('theme')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('form_settings');
    }
};
