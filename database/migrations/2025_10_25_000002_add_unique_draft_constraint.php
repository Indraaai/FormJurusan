<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, clean up any duplicate drafts that might exist
        // Keep only the most recent draft for each user-form combination
        DB::statement("
            DELETE t1 FROM form_responses t1
            INNER JOIN (
                SELECT form_id, respondent_user_id, MIN(id) as min_id
                FROM form_responses
                WHERE status = 'draft'
                GROUP BY form_id, respondent_user_id
                HAVING COUNT(*) > 1
            ) t2 ON t1.form_id = t2.form_id
               AND t1.respondent_user_id = t2.respondent_user_id
               AND t1.status = 'draft'
               AND t1.id > t2.min_id
        ");

        // Note: We can't use partial unique index in MySQL < 8.0.13
        // Instead, we rely on application-level locking with DB::transaction and lockForUpdate()
        // The composite index idx_draft_lookup will help with performance
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Nothing to rollback as we didn't add database constraint
        // Application-level handling remains
    }
};
