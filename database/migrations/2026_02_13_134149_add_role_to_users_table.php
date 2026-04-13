<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

public function up(): void // Dijalankan saat migrate
{
    if (!Schema::hasColumn('users', 'role')) { // Cek jika kolom role belum ada di tabel users, baru ditambahkan
        Schema::table('users', function (Blueprint $table) { 
            $table->string('role')->default('user'); // Menambahkan kolom role dengan default user
        });
    }
}

public function down(): void // Dijalankan saat rollback
{
    if (Schema::hasColumn('users', 'role')) { // Cek jika kolom role ada di tabel users, baru dihapus
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
}

};
