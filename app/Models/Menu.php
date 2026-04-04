<?php
// app/Models/Menu.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table      = 'menu';
    protected $primaryKey = 'id_menu';

    protected $fillable = [
        'id_vendor', 'nama_menu', 'deskripsi', 'harga', 'foto', 'tersedia',
    ];

    protected $casts = [
        'harga'    => 'float',
        'tersedia' => 'boolean',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'id_vendor', 'id_vendor');
    }

    public function pesananDetail()
    {
        return $this->hasMany(PesananDetail::class, 'id_menu', 'id_menu');
    }

    // Accessor: format harga ke Rupiah
    public function getHargaFormatAttribute(): string
    {
        return 'Rp ' . number_format($this->harga, 0, ',', '.');
    }
}