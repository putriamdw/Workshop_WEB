<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('antrian', function (Blueprint $table) {
            $table->string('ruangan')->nullable()->after('poli');
            $table->string('loket')->nullable()->after('ruangan');
        });
    }

    public function down(): void
    {
        Schema::table('antrian', function (Blueprint $table) {
            $table->dropColumn(['ruangan', 'loket']);
        });
    }
};
