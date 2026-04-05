<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Menu;

class VendorSeeder extends Seeder
{
    public function run(): void
    {
        // Vendor 1
        $u1 = User::create([
            'name'     => 'Pak Budi',
            'email'    => 'budi@kantin.com',
            'password' => Hash::make('password'),
            'role'     => 'vendor',
        ]);

        $v1 = Vendor::create([
            'id_user'     => $u1->id,
            'nama_kantin' => 'Kantin Budi Jaya',
            'deskripsi'   => 'Masakan rumahan enak dan murah',
        ]);

        Menu::insert([
            ['id_vendor' => $v1->id_vendor, 'nama_menu' => 'Nasi Gudeg',    'harga' => 12000, 'tersedia' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id_vendor' => $v1->id_vendor, 'nama_menu' => 'Es Teh Manis',  'harga' => 3000,  'tersedia' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id_vendor' => $v1->id_vendor, 'nama_menu' => 'Tempe Goreng',  'harga' => 2000,  'tersedia' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Vendor 2
        $u2 = User::create([
            'name'     => 'Bu Sari',
            'email'    => 'sari@kantin.com',
            'password' => Hash::make('password'),
            'role'     => 'vendor',
        ]);

        $v2 = Vendor::create([
            'id_user'     => $u2->id,
            'nama_kantin' => 'Warung Bu Sari',
            'deskripsi'   => 'Bakso dan mie ayam spesial',
        ]);

        Menu::insert([
            ['id_vendor' => $v2->id_vendor, 'nama_menu' => 'Bakso Spesial',  'harga' => 15000, 'tersedia' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id_vendor' => $v2->id_vendor, 'nama_menu' => 'Mie Ayam',       'harga' => 13000, 'tersedia' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id_vendor' => $v2->id_vendor, 'nama_menu' => 'Pangsit Goreng', 'harga' => 5000,  'tersedia' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Contoh customer baru
        User::create([
            'name'     => 'Andi Customer',
            'email'    => 'andi@gmail.com',
            'password' => Hash::make('password'),
            'role'     => 'customer',
        ]);
    }
}