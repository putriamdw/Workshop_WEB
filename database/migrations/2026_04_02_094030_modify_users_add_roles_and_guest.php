<?php
use Illuminate\Database\Migrations\Migration;

// Kolom 'role' sudah ada di migration sebelumnya (add_role_to_users_table)
// File ini tidak perlu melakukan apa-apa
return new class extends Migration
{
    public function up(): void
    {
        // Tidak ada yang perlu dilakukan
        // role sudah ada dari migration 2026_02_13_134149_add_role_to_users_table
    }

    public function down(): void
    {
        //
    }
};