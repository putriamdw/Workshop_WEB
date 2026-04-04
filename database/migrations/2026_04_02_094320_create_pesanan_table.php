<?php
// database/migrations/xxxx_create_pesanan_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesanan', function (Blueprint $table) {
            $table->string('id_pesanan', 30)->primary();
            $table->unsignedBigInteger('id_user')->nullable(); // null = guest
            $table->string('nama_guest', 50)->nullable();      // "Guest_0000001"
            $table->unsignedInteger('id_vendor');
            $table->decimal('total', 12, 2);
            $table->string('status_bayar', 20)->default('belum_bayar');
            // status_bayar: belum_bayar | lunas | expired
            $table->string('metode_bayar', 20)->nullable(); // virtual_account | qris
            $table->string('bank', 20)->nullable();
            $table->string('va_number', 50)->nullable();
            $table->text('qr_string')->nullable();
            $table->string('midtrans_order_id', 100)->nullable();
            $table->text('midtrans_token')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->foreign('id_user')
                  ->references('id')
                  ->on('users')
                  ->nullOnDelete();

            $table->foreign('id_vendor')
                  ->references('id_vendor')
                  ->on('vendor');
        });

        // Sequence PostgreSQL untuk nomor guest otomatis
        DB::statement("CREATE SEQUENCE IF NOT EXISTS guest_seq START 1");
    }

    public function down(): void
    {
        Schema::dropIfExists('pesanan');
        DB::statement("DROP SEQUENCE IF EXISTS guest_seq");
    }
};