<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table      = 'customer';
    protected $primaryKey = 'id_customer';

    protected $fillable = [
    'nama',
    // 'email',
    'alamat',
    'provinsi',
    'kota',
    'kecamatan',
    'kodepos',
    'kelurahan',
    'foto_blob',
    'foto_path',
];
}