<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('form_answer_grid_cells', function (Blueprint $t) {
            $t->id();
            $t->foreignId('answer_id')->constrained('form_answers')->cascadeOnDelete();

            $t->foreignId('row_option_id')->nullable()->constrained('question_options')->nullOnDelete();
            $t->foreignId('col_option_id')->nullable()->constrained('question_options')->nullOnDelete();

            $t->string('row_label_snapshot', 500)->nullable();
            $t->string('col_label_snapshot', 500)->nullable();

            $t->timestamps();

            $t->unique(['answer_id', 'row_option_id', 'col_option_id'], 'uq_grid_cell');
            $t->index(['answer_id', 'row_option_id'], 'idx_grid_row');
            $t->index(['answer_id', 'col_option_id'], 'idx_grid_col');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('form_answer_grid_cells');
    }
};
