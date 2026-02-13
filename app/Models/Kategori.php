<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;

    protected $table = 'kategori';
    protected $primaryKey = 'idkategori';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = [
        'nama_kategori'
    ];

    public $timestamps = false;

    public function buku()
    {
        return $this->hasMany(Buku::class);
    }
}
