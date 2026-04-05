<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesanan_detail', function (Blueprint $table) {
            $table->increments('id');
            $table->string('id_pesanan', 30);
            $table->unsignedInteger('id_menu');
            $table->string('nama_menu', 100);     // snapshot nama saat pesan
            $table->decimal('harga_satuan', 12, 2); // snapshot harga saat pesan
            $table->integer('jumlah');
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();

            $table->foreign('id_pesanan')
                  ->references('id_pesanan')
                  ->on('pesanan')
                  ->onDelete('cascade');

            $table->foreign('id_menu')
                  ->references('id_menu')
                  ->on('menu');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesanan_detail');
    }
};