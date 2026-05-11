<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kunjungan extends Model
{
    protected $table      = 'kunjungan';
    protected $primaryKey = 'id_kunjungan';

    protected $fillable = [
        'id_toko',
        'nama_pengunjung',
        'latitude_kunjungan',
        'longitude_kunjungan',
        'jarak_meter',
        'status',
    ];

    public function toko()
    {
        return $this->belongsTo(Toko::class, 'id_toko', 'id_toko');
    }
}