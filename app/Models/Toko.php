<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Toko extends Model
{
    protected $table      = 'toko';
    protected $primaryKey = 'id_toko';

    protected $fillable = [
        'kode_toko',
        'nama_toko',
        'alamat',
        'latitude',
        'longitude',
        'accuracy',
        'id_vendor',
    ];

    public function kunjungan()
    {
        return $this->hasMany(Kunjungan::class, 'id_toko', 'id_toko');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'id_vendor', 'id_vendor');
    }

    public static function generateKode(): string
    {
        $last = self::orderBy('id_toko', 'desc')->first();
        $num  = $last ? ((int) substr($last->kode_toko, 3)) + 1 : 1;
        return 'TKO' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }
}