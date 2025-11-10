<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Hapus constraint lama jika ada (gunakan DROP CHECK, bukan DROP CONSTRAINT)
        try {
            DB::statement("ALTER TABLE users DROP CHECK chk_users_role_email_domain");
        } catch (\Throwable $e) {
            // abaikan error jika constraint belum ada
        }

        DB::statement("
            ALTER TABLE users
            ADD CONSTRAINT chk_users_role_email_domain
            CHECK (
                (role = 'respondent' AND LOWER(email) LIKE '%@mhs.unimal.ac.id')
                OR role = 'admin'
            )
        ");
    }

    public function down(): void
    {
        try {
            DB::statement("ALTER TABLE users DROP CHECK chk_users_role_email_domain");
        } catch (\Throwable $e) {
            // abaikan error jika constraint belum ada
        }
    }
};
