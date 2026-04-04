<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Pesanan extends Model
{
    protected $table      = 'pesanan';
    protected $primaryKey = 'id_pesanan';
    public $incrementing  = false;
    protected $keyType    = 'string';

    protected $fillable = [
        'id_pesanan', 'id_user', 'nama_guest', 'id_vendor',
        'total', 'status_bayar', 'metode_bayar', 'bank',
        'va_number', 'qr_string', 'midtrans_order_id',
        'midtrans_token', 'paid_at',
    ];

    protected $casts = [
        'total'   => 'float',
        'paid_at' => 'datetime',
    ];

    // Generate ID pesanan: PSN-20240101-0001
    public static function generateId(): string
    {
        $tanggal = now()->format('Ymd');
        $prefix  = 'PSN-' . $tanggal . '-';

        $last = self::where('id_pesanan', 'like', $prefix . '%')
            ->orderByDesc('id_pesanan')
            ->value('id_pesanan');

        $urut = $last ? (int) substr($last, -4) + 1 : 1;
        return $prefix . str_pad($urut, 4, '0', STR_PAD_LEFT);
    }

    // Generate nama guest: Guest_0000001
    public static function generateNamaGuest(): string
    {
        $next = DB::selectOne("SELECT nextval('guest_seq') AS val")->val;
        return 'Guest_' . str_pad($next, 7, '0', STR_PAD_LEFT);
    }

    public function isLunas(): bool
    {
        return $this->status_bayar === 'lunas';
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'id_vendor', 'id_vendor');
    }

    public function details()
    {
        return $this->hasMany(PesananDetail::class, 'id_pesanan', 'id_pesanan');
    }

    // Accessor: nama pembeli (user login atau guest)
    public function getNamaPembeliAttribute(): string
    {
        return $this->user?->name ?? $this->nama_guest ?? 'Guest';
    }

    // Accessor: format total rupiah
    public function getTotalFormatAttribute(): string
    {
        return 'Rp ' . number_format($this->total, 0, ',', '.');
    }
}