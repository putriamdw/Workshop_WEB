<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('kategori', function (Blueprint $table) {
        $table->increments('idkategori');
        $table->string('nama_kategori', 100);
    });

    Schema::create('buku', function (Blueprint $table) {
        $table->increments('idbuku');
        $table->string('kode', 20)->unique();
        $table->string('judul', 500);
        $table->string('pengarang', 200);
        $table->unsignedInteger('idkategori');
        $table->foreign('idkategori')
              ->references('idkategori')
              ->on('kategori')
              ->onUpdate('cascade')
              ->onDelete('restrict');
    });
}

public function down(): void
{
    Schema::dropIfExists('buku');
    Schema::dropIfExists('kategori');
}
};