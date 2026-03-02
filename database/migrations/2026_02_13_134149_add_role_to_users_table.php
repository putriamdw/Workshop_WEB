<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void // dijalankan saat migrate
{
    if (!Schema::hasColumn('users', 'role')) { // cek jika kolom role belum ada di tabel users, baru ditambahkan
        Schema::table('users', function (Blueprint $table) { 
            $table->string('role')->default('user'); // menambahkan kolom role dengan default user
        });
    }
}

public function down(): void // dijalankan saat rollback
{
    if (Schema::hasColumn('users', 'role')) { // cek jika kolom role ada di tabel users, baru dihapus
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
}

};
