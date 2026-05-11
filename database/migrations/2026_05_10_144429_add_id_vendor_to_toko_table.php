<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('toko', function (Blueprint $table) {
            $table->unsignedInteger('id_vendor')->nullable()->after('accuracy');
            $table->foreign('id_vendor')
                ->references('id_vendor')
                ->on('vendor')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('toko', function (Blueprint $table) {
            $table->dropForeign(['id_vendor']);
            $table->dropColumn('id_vendor');
        });
    }
};
