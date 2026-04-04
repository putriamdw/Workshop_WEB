<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barang', function (Blueprint $table) {
            $table->increments('id');
            $table->string('id_barang', 8)->unique()->nullable();
            $table->string('nama', 100)->nullable();
            $table->decimal('harga', 12, 2)->nullable();
            $table->timestamps();
        });

        // Sequence untuk auto-generate id_barang (BRG0001, BRG0002, dst)
        DB::statement("CREATE SEQUENCE IF NOT EXISTS barang_seq START 1");

        // Trigger function
        DB::statement("
            CREATE OR REPLACE FUNCTION trigger_id_barang()
            RETURNS TRIGGER AS \$\$
            DECLARE
                next_id INTEGER;
            BEGIN
                next_id := nextval('barang_seq');
                NEW.id_barang := 'BRG' || LPAD(next_id::TEXT, 4, '0');
                RETURN NEW;
            END;
            \$\$ LANGUAGE plpgsql;
        ");

        // Pasang trigger ke tabel barang
        DB::statement("
            CREATE TRIGGER trigger_id_barang
            BEFORE INSERT ON barang
            FOR EACH ROW
            EXECUTE FUNCTION trigger_id_barang();
        ");

        // Insert data awal
        DB::statement("
            INSERT INTO barang (nama, harga) VALUES
            ('pensil', 2500),
            ('penghapus', 2000),
            ('bolpoin', 6500),
            ('buku gambar', 3000),
            ('kamus', 175000),
            ('kotak pensil', 20000),
            ('pensil warna', 15000),
            ('krayon', 150000),
            ('stabilo', 7000),
            ('penggaris', 4000)
        ");
    }

    public function down(): void
    {
        DB::statement("DROP TRIGGER IF EXISTS trigger_id_barang ON barang");
        DB::statement("DROP FUNCTION IF EXISTS trigger_id_barang");
        DB::statement("DROP SEQUENCE IF EXISTS barang_seq");
        Schema::dropIfExists('barang');
    }
};