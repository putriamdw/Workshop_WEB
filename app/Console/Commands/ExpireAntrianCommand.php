<?php

namespace App\Console\Commands;

use App\Models\Antrian;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ExpireAntrianCommand extends Command
{
    protected $signature   = 'antrian:expire';
    protected $description = 'Otomatis tandai antrian dipanggil > 15 menit menjadi terlambat';

    const TOLERANSI_MENIT = 15;

    public function handle(): int
    {
        $batasWaktu = now()->subMinutes(self::TOLERANSI_MENIT);

        $expired = Antrian::where('status', 'dipanggil')
                          ->where('jam_panggil', '<=', $batasWaktu)
                          ->get();

        if ($expired->isEmpty()) {
            return Command::SUCCESS;
        }

        foreach ($expired as $antrian) {
            $antrian->update(['status' => 'terlambat']);
            $this->line("  → #{$antrian->nomor} {$antrian->nama} → terlambat");
        }

        $this->broadcastState();
        $this->info("✔ {$expired->count()} antrian di-expire otomatis.");

        return Command::SUCCESS;
    }

    private function broadcastState(): void
    {
        $list = Antrian::whereDate('jam_daftar', today())
                       ->orderBy('nomor')
                       ->get(['id', 'nomor', 'nama', 'poli', 'status', 'jam_daftar', 'jam_panggil'])
                       ->toArray();

        $dipanggil = collect($list)
            ->where('status', 'dipanggil')
            ->sortByDesc('jam_panggil')
            ->first();

        Cache::put('antrian_data', [
            'list'      => array_values($list),
            'dipanggil' => $dipanggil,
            'counts'    => [
                'menunggu'  => collect($list)->where('status', 'menunggu')->count(),
                'dipanggil' => collect($list)->where('status', 'dipanggil')->count(),
                'terlambat' => collect($list)->where('status', 'terlambat')->count(),
                'selesai'   => collect($list)->where('status', 'selesai')->count(),
            ],
        ], now()->addHours(12));
    }
}