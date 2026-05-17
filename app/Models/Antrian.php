<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Antrian extends Model
{
    protected $table = 'antrian';

    protected $fillable = [
        'nomor',
        'nama',
        'poli',
        'ruangan',
        'loket',
        'status',
        'jam_daftar',
        'jam_panggil',
    ];

    protected $casts = [
        'jam_daftar'  => 'datetime',
        'jam_panggil' => 'datetime',
    ];

    /**
     * Generate nomor antrian berikutnya untuk hari ini.
     * Format: 001, 002, 003, ...
     */
    public static function generateNomor(): string
    {
        $lastToday = static::whereDate('jam_daftar', today())
                           ->orderByDesc('nomor')
                           ->value('nomor');

        $next = $lastToday ? ((int) $lastToday) + 1 : 1;

        return str_pad($next, 3, '0', STR_PAD_LEFT);
    }
}