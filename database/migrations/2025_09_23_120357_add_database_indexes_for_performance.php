<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('forms', function (Blueprint $table) {
            $table->index(['is_published', 'created_at']);
            $table->index('created_by');
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->index(['section_id', 'position']);
        });

        Schema::table('question_options', function (Blueprint $table) {
            $table->index('question_id');
        });

        Schema::table('form_responses', function (Blueprint $table) {
            $table->index(['form_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('forms', function (Blueprint $table) {
            $table->dropIndex(['is_published', 'created_at']);
            $table->dropIndex(['created_by']);
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->dropIndex(['section_id', 'position']);
        });

        Schema::table('question_options', function (Blueprint $table) {
            $table->dropIndex(['question_id']);
        });

        Schema::table('form_responses', function (Blueprint $table) {
            $table->dropIndex(['form_id', 'created_at']);
        });
    }
};
