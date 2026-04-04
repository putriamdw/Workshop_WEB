<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $table      = 'vendor';
    protected $primaryKey = 'id_vendor';

    protected $fillable = [
        'id_user', 'nama_kantin', 'deskripsi', 'foto', 'aktif',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }

    public function menus()
    {
        return $this->hasMany(Menu::class, 'id_vendor', 'id_vendor');
    }

    public function pesanan()
    {
        return $this->hasMany(Pesanan::class, 'id_vendor', 'id_vendor');
    }
}
