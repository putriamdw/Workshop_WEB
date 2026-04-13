<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('customer', function (Blueprint $table) {
        $table->text('alamat')->nullable()->after('email');
        $table->string('provinsi', 100)->nullable()->after('alamat');
        $table->string('kota', 100)->nullable()->after('provinsi');
        $table->string('kecamatan', 100)->nullable()->after('kota');
        $table->string('kodepos', 10)->nullable()->after('kecamatan');
        $table->string('kelurahan', 100)->nullable()->after('kodepos');
    });
}

public function down(): void
{
    Schema::table('customer', function (Blueprint $table) {
        $table->dropColumn(['alamat','provinsi','kota','kecamatan','kodepos','kelurahan']);
    });
}
};
