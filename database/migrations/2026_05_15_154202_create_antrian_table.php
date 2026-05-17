<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('antrian', function (Blueprint $table) {
            $table->id();
            $table->string('nomor', 10);
            $table->string('nama');
            $table->string('poli');
            $table->enum('status', ['menunggu', 'dipanggil', 'selesai', 'terlambat'])
                  ->default('menunggu');
            $table->timestamp('jam_daftar')->useCurrent();
            $table->timestamp('jam_panggil')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('antrian');
    }
};