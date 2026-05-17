<?php

namespace App\Http\Controllers;

use App\Models\Antrian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AntrianController extends Controller
{
    const POLI_LIST = [
        'Umum'         => 'Poli Umum',
        'Gigi'         => 'Poli Gigi',
        'Jantung'      => 'Poli Jantung',
        'Anak'         => 'Poli Anak',
        'Kandungan'    => 'Poli Kandungan',
        'Mata'         => 'Poli Mata',
        'THT'          => 'Poli THT',
        'Farmasi'      => 'Farmasi / Apotek',
        'Laboratorium' => 'Laboratorium',
        'Kasir'        => 'Kasir / Pembayaran',
    ];

    // =========================================================================
    // GUEST
    // =========================================================================

    public function guestForm()
    {
        return view('antrian.guest.form', [
            'poliList' => self::POLI_LIST,
        ]);
    }

    public function guestStore(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:100',
            'poli' => 'required|string|in:' . implode(',', array_keys(self::POLI_LIST)),
        ]);

        $antrian = Antrian::create([
            'nomor'      => Antrian::generateNomor(),
            'nama'       => $data['nama'],
            'poli'       => $data['poli'],
            'status'     => 'menunggu',
            'jam_daftar' => now(),
        ]);

        $this->broadcastState();

        return redirect()->route('antrian.tiket', $antrian->id);
    }

    public function tiket($id)
    {
        $antrian = Antrian::findOrFail($id);
        return view('antrian.guest.tiket', compact('antrian'));
    }

    // =========================================================================
    // ADMIN
    // =========================================================================

    public function adminDashboard()
    {
        $list = Antrian::whereDate('jam_daftar', today())
                       ->orderBy('nomor')
                       ->get();

        $counts = [
            'menunggu'  => $list->where('status', 'menunggu')->count(),
            'dipanggil' => $list->where('status', 'dipanggil')->count(),
            'terlambat' => $list->where('status', 'terlambat')->count(),
            'selesai'   => $list->where('status', 'selesai')->count(),
        ];

        return view('antrian.admin.dashboard', compact('list', 'counts'));
    }

    /**
     * Panggil berikutnya — otomatis ambil nomor menunggu terkecil.
     */
    public function panggilBerikutnya(Request $request)
    {
        $data = $request->validate([
            'ruangan' => 'nullable|string|max:50',
            'loket'   => 'nullable|string|max:50',
        ]);

        $antrian = Antrian::whereDate('jam_daftar', today())
                        ->where('status', 'menunggu')
                        ->orderBy('nomor')
                        ->first();

        if (!$antrian) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada antrian yang menunggu.'
            ]);
        }

        $antrian->update([
            'status'      => 'dipanggil',
            'jam_panggil' => now(),
            'ruangan'     => $data['ruangan'] ?? null,
            'loket'       => $data['loket'] ?? null,
        ]);

        $this->broadcastState();

        return response()->json(['success' => true, 'antrian' => $antrian->fresh()]);
    }
    
    // Panggil antrian tertentu by ID.
    // Untuk: (1) panggil dari baris menunggu, (2) panggil ulang yang terlambat.
    
    public function panggil($id)
    {
        $antrian = Antrian::findOrFail($id);

        if (in_array($antrian->status, ['menunggu', 'terlambat'])) {
            $antrian->update([
                'status'      => 'dipanggil',
                'jam_panggil' => now(),
            ]);
        }

        $this->broadcastState();

        return response()->json(['success' => true, 'antrian' => $antrian->fresh()]);
    }

    public function selesai($id)
    {
        $antrian = Antrian::findOrFail($id);
        $antrian->update(['status' => 'selesai']);
        $this->broadcastState();
        return response()->json(['success' => true]);
    }

    public function terlambat($id)
    {
        $antrian = Antrian::findOrFail($id);
        $antrian->update(['status' => 'terlambat']);
        $this->broadcastState();
        return response()->json(['success' => true]);
    }

    // =========================================================================
    // PAPAN
    // =========================================================================

    public function papan()
    {
        return view('antrian.papan.index');
    }

    // =========================================================================
    // SSE STREAM — sesuai format modul
    // =========================================================================

    public function stream()
    {
        set_time_limit(0);

        return response()->stream(function () {
            while (true) {
                // Ambil data terbaru dari Cache
                $data = Cache::get('antrian_data');

                // Kalau cache kosong, load dari DB
                if (!$data) {
                    $list = Antrian::whereDate('jam_daftar', today())
                                    ->orderBy('nomor')
                                    ->get(['id', 'nomor', 'nama', 'poli', 'ruangan', 'loket', 'status', 'jam_daftar', 'jam_panggil'])
                                    ->toArray();

                    $dipanggil = collect($list)
                        ->where('status', 'dipanggil')
                        ->sortByDesc('jam_panggil')
                        ->first();

                    $data = [
                        'list'      => array_values($list),
                        'dipanggil' => $dipanggil,
                        'counts'    => [
                            'menunggu'  => collect($list)->where('status', 'menunggu')->count(),
                            'dipanggil' => collect($list)->where('status', 'dipanggil')->count(),
                            'terlambat' => collect($list)->where('status', 'terlambat')->count(),
                            'selesai'   => collect($list)->where('status', 'selesai')->count(),
                        ],
                    ];

                    Cache::put('antrian_data', $data, now()->addHours(12));
                }

                // Kirim event SSE — format sesuai modul 1.4
                echo 'event: queue-update' . PHP_EOL;
                echo 'data: ' . json_encode($data) . PHP_EOL;
                echo PHP_EOL; // baris kosong = akhir pesan

                ob_flush();
                flush();

                // Cek apakah client masih terhubung
                if (connection_aborted()) break;

                sleep(1); // update setiap 1 detik
            }
        }, 200, [
            'Content-Type'      => 'text/event-stream',
            'Cache-Control'     => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Connection'        => 'keep-alive',
        ]);
    }

    // =========================================================================
    // HELPER
    // =========================================================================

    private function broadcastState(): void
    {
        $list = Antrian::whereDate('jam_daftar', today())
                    ->orderBy('nomor')
                    ->get(['id', 'nomor', 'nama', 'poli', 'ruangan', 'loket', 'status', 'jam_daftar', 'jam_panggil'])
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

    public function panggilDenganDetail(Request $request, $id)
    {
        $data = $request->validate([
            'ruangan' => 'nullable|string|max:50',
            'loket'   => 'nullable|string|max:50',
        ]);

        $antrian = Antrian::findOrFail($id);

        if (in_array($antrian->status, ['menunggu', 'terlambat'])) {
            $antrian->update([
                'status'      => 'dipanggil',
                'jam_panggil' => now(),
                'ruangan'     => $data['ruangan'] ?? null,
                'loket'       => $data['loket'] ?? null,
            ]);
        }

        $this->broadcastState();

        return response()->json(['success' => true, 'antrian' => $antrian->fresh()]);
    }
}