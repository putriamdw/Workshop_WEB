<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void // menambahkan kolom
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('id_google', 256)->nullable();
            $table->string('otp', 6)->nullable();
        });
    }

    public function down(): void // menghapus kolom jika rollback
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['id_google', 'otp']);
        });
    }
};
