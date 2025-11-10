<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('form_responses', function (Blueprint $t) {
            $t->id();

            // Public identifier for link/edit
            $t->ulid('uid')->unique();

            // Relasi ke form
            $t->foreignId('form_id')->constrained('forms')->cascadeOnDelete();

            // Status & timing
            $t->enum('status', ['draft', 'submitted'])->default('draft');
            $t->timestamp('started_at')->nullable();
            $t->timestamp('submitted_at')->nullable();
            $t->unsignedInteger('duration_seconds')->nullable();

            // Respondent (auth user snapshot)
            $t->foreignId('respondent_user_id')->nullable()->constrained('users')->nullOnDelete();
            $t->string('respondent_email', 190)->nullable();

            // Edit link / anti-abuse
            $t->char('edit_token', 36)->nullable()->unique();
            $t->string('source_ip', 45)->nullable();      // IPv4/IPv6
            $t->string('user_agent', 1024)->nullable();
            $t->boolean('is_spam')->default(false);

            $t->timestamps();
            $t->softDeletes();

            // Index yang umum dipakai
            $t->index(['form_id', 'status', 'submitted_at'], 'idx_resp_form_status_submit');
            $t->index('respondent_user_id', 'idx_resp_user');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('form_responses');
    }
};
