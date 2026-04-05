<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PesananDetail extends Model
{
    protected $table      = 'pesanan_detail';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_pesanan', 'id_menu', 'nama_menu',
        'harga_satuan', 'jumlah', 'subtotal',
    ];

    protected $casts = [
        'harga_satuan' => 'float',
        'subtotal'     => 'float',
    ];

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan', 'id_pesanan');
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'id_menu', 'id_menu');
    }

    public function getSubtotalFormatAttribute(): string
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }
}