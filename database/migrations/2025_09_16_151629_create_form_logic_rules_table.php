<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('form_logic_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained('forms')->cascadeOnDelete();
            $table->foreignId('source_question_id')->constrained('questions')->cascadeOnDelete();
            $table->enum('operator', ['=', '!=', 'contains', 'in', 'between', '>=', '<=', 'answered', 'not_answered']);
            $table->text('value_text')->nullable();
            $table->decimal('value_number', 15, 4)->nullable();
            $table->foreignId('option_id')->nullable()->constrained('question_options')->nullOnDelete();
            $table->foreignId('target_section_id')->nullable()->constrained('form_sections')->nullOnDelete();
            $table->enum('action', ['goto_section', 'submit'])->default('goto_section');
            $table->integer('priority')->default(0);
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();

            $table->index(['form_id', 'source_question_id', 'priority'], 'idx_logic_form_src');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('form_logic_rules');
    }
};
