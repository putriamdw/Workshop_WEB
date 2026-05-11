<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kunjungan', function (Blueprint $table) {
            $table->increments('id_kunjungan');
            $table->unsignedInteger('id_toko');
            $table->string('nama_pengunjung', 100)->nullable(); // bisa guest
            $table->decimal('latitude_kunjungan', 10, 7);
            $table->decimal('longitude_kunjungan', 10, 7);
            $table->decimal('jarak_meter', 8, 2); // jarak hasil perhitungan
            $table->enum('status', ['diterima', 'ditolak']);
            $table->timestamp('waktu_kunjungan')->useCurrent();
            $table->timestamps();

            $table->foreign('id_toko')
                  ->references('id_toko')
                  ->on('toko')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kunjungan');
    }
};