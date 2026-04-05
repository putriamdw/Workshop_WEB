<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu', function (Blueprint $table) {
            $table->increments('id_menu');
            $table->unsignedInteger('id_vendor');
            $table->string('nama_menu', 100);
            $table->text('deskripsi')->nullable();
            $table->decimal('harga', 12, 2);
            $table->string('foto')->nullable();
            $table->boolean('tersedia')->default(true);
            $table->timestamps();

            $table->foreign('id_vendor')
                  ->references('id_vendor')
                  ->on('vendor')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu');
    }
};