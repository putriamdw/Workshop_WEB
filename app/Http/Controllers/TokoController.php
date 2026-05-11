<?php

namespace App\Http\Controllers;

use App\Models\Toko;
use App\Models\Kunjungan;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\SvgWriter;

class TokoController extends Controller
{
    // ── Admin: List semua toko ────────────────────────────────────────────────
    public function index()
    {
        $tokos = Toko::with('vendor')->withCount('kunjungan')->latest()->get();
        return view('toko.index', compact('tokos'));
    }

    // ── Admin: Form tambah toko ───────────────────────────────────────────────
    public function create()
    {
        $vendors = Vendor::select('id_vendor', 'nama_kantin')->get();
        return view('toko.create', compact('vendors'));
    }

    // ── Admin: Simpan toko baru ───────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'nama_toko' => 'required|string|max:100',
            'alamat'    => 'nullable|string',
            'accuracy'  => 'required|integer|min:10|max:1000',
            'id_vendor' => 'nullable|exists:vendor,id_vendor',
        ]);

        Toko::create([
            'kode_toko' => Toko::generateKode(),
            'nama_toko' => $request->nama_toko,
            'alamat'    => $request->alamat,
            'accuracy'  => $request->accuracy,
            'id_vendor' => $request->id_vendor ?: null,
        ]);

        return redirect()->route('toko.index')
            ->with('success', 'Toko berhasil ditambahkan!');
    }

    // ── Admin: Form edit toko ─────────────────────────────────────────────────
    public function edit($id)
    {
        $toko    = Toko::findOrFail($id);
        $vendors = Vendor::select('id_vendor', 'nama_kantin')->get();
        return view('toko.edit', compact('toko', 'vendors'));
    }

    // ── Admin: Update toko ────────────────────────────────────────────────────
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_toko' => 'required|string|max:100',
            'alamat'    => 'nullable|string',
            'accuracy'  => 'required|integer|min:10|max:1000',
            'id_vendor' => 'nullable|exists:vendor,id_vendor',
        ]);

        $toko = Toko::findOrFail($id);
        $toko->update($request->only('nama_toko', 'alamat', 'accuracy', 'id_vendor'));

        return redirect()->route('toko.index')
            ->with('success', 'Toko berhasil diperbarui!');
    }

    // ── Admin: Hapus toko ─────────────────────────────────────────────────────
    public function destroy($id)
    {
        Toko::destroy($id);
        return redirect()->route('toko.index')
            ->with('success', 'Toko berhasil dihapus!');
    }

    // ── Admin: Halaman input titik awal ───────────────────────────────────────
    public function titikAwal($id)
    {
        $toko = Toko::findOrFail($id);
        return view('toko.titik-awal', compact('toko'));
    }

    // ── Admin: Simpan koordinat toko ──────────────────────────────────────────
    public function simpanTitikAwal(Request $request, $id)
    {
        $request->validate([
            'latitude'    => 'required|numeric',
            'longitude'   => 'required|numeric',
            'accuracy_gps'=> 'nullable|numeric',
        ]);

        $toko = Toko::findOrFail($id);
        $toko->update([
            'latitude'    => $request->latitude,
            'longitude'   => $request->longitude,
            'accuracy_gps'=> $request->accuracy_gps ?? 0,
        ]);

        return redirect()->route('toko.index')
            ->with('success', 'Koordinat toko berhasil disimpan!');
    }

    // ── Admin: Generate QR Code toko ─────────────────────────────────────────
    public function qrcode($id)
    {
        $toko   = Toko::findOrFail($id);
        $qrCode = new QrCode($toko->kode_toko);
        $writer = new SvgWriter();
        $result = $writer->write($qrCode);
        $qrSvg  = base64_encode($result->getString());

        return view('toko.qrcode', compact('toko', 'qrSvg'));
    }

    // ── Admin: Riwayat kunjungan ──────────────────────────────────────────────
    public function riwayat(Request $request)
    {
        $kunjungan = Kunjungan::with('toko')
            ->when($request->toko, fn($q) => $q->where('id_toko', $request->toko))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest('waktu_kunjungan')
            ->paginate(20);

        $tokos = Toko::select('id_toko', 'nama_toko')->get();
        return view('toko.riwayat', compact('kunjungan', 'tokos'));
    }

    // ── Vendor: Halaman input titik awal kantinnya sendiri ────────────────────
    public function titikAwalVendor()
    {
        $vendor = auth()->user()->vendor;
        $toko   = Toko::where('id_vendor', $vendor->id_vendor)->first();

        return view('toko.titik-awal-vendor', compact('vendor', 'toko'));
    }

    // ── Vendor: Simpan koordinat kantinnya sendiri ────────────────────────────
    public function simpanTitikAwalVendor(Request $request)
    {
        $request->validate([
            'latitude'    => 'required|numeric',
            'longitude'   => 'required|numeric',
            'accuracy_gps'=> 'nullable|numeric',
        ]);

        $vendor = auth()->user()->vendor;
        $toko   = Toko::where('id_vendor', $vendor->id_vendor)->first();

        if (!$toko) {
            return redirect()->route('vendor.titik-awal')
                ->with('error', 'Toko belum ditetapkan untuk akun kamu. Hubungi admin.');
        }

        $toko->update([
            'latitude'    => $request->latitude,
            'longitude'   => $request->longitude,
            'accuracy_gps'=> $request->accuracy_gps ?? 0,
        ]);

        return redirect()->route('vendor.titik-awal')
            ->with('success', 'Koordinat kantin berhasil disimpan!');
    }

    // ── Vendor: Lihat QR Code kantinnya sendiri ───────────────────────────────
    public function qrcodeVendor()
    {
        $vendor = auth()->user()->vendor;
        $toko   = Toko::where('id_vendor', $vendor->id_vendor)->first();

        if (!$toko) {
            return redirect()->route('vendor.titik-awal')
                ->with('error', 'Toko belum ditetapkan. Hubungi admin.');
        }

        $qrCode = new QrCode($toko->kode_toko);
        $writer = new SvgWriter();
        $result = $writer->write($qrCode);
        $qrSvg  = base64_encode($result->getString());

        return view('toko.qrcode-vendor', compact('toko', 'qrSvg', 'vendor'));
    }

    // ── Vendor: Riwayat kunjungan kantinnya sendiri ───────────────────────────
    public function riwayatVendor(Request $request)
    {
        $vendor = auth()->user()->vendor;
        $toko   = Toko::where('id_vendor', $vendor->id_vendor)->first();

        if (!$toko) {
            return redirect()->route('vendor.titik-awal')
                ->with('error', 'Toko belum ditetapkan. Hubungi admin.');
        }

        $kunjungan = Kunjungan::where('id_toko', $toko->id_toko)
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest('waktu_kunjungan')
            ->paginate(15);

        return view('toko.riwayat-vendor', compact('kunjungan', 'toko', 'vendor'));
    }

    // ── Customer: Halaman scan QR ─────────────────────────────────────────────
    public function kunjungan()
    {
        return view('toko.kunjungan');
    }

    // ── Customer: Simpan kunjungan ────────────────────────────────────────────
    public function simpanKunjungan(Request $request)
    {
        $request->validate([
            'kode_toko'           => 'required|string',
            'latitude_kunjungan'  => 'required|numeric',
            'longitude_kunjungan' => 'required|numeric',
            'accuracy_kunjungan'  => 'nullable|numeric',
        ]);

        $toko = Toko::where('kode_toko', $request->kode_toko)->first();

        if (!$toko) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Kode toko tidak ditemukan.',
            ], 404);
        }

        if (!$toko->latitude || !$toko->longitude) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Koordinat toko belum diatur.',
            ], 422);
        }

        $jarak = $this->hitungJarak(
            $toko->latitude, $toko->longitude,
            $request->latitude_kunjungan, $request->longitude_kunjungan
        );

        // Sesuai Lampiran 3:
        // threshold_efektif = threshold (admin set) + acc GPS toko + acc GPS sales/customer
        $thresholdAdmin   = $toko->accuracy;
        $accGpsToko       = $toko->accuracy_gps ?? 0;
        $accGpsSales      = $request->accuracy_kunjungan ?? 0;
        $thresholdEfektif = $thresholdAdmin + $accGpsToko + $accGpsSales;

        $status = $jarak <= $thresholdEfektif ? 'diterima' : 'ditolak';

        Kunjungan::create([
            'id_toko'             => $toko->id_toko,
            'nama_pengunjung'     => auth()->check() ? auth()->user()->name : 'Guest',
            'latitude_kunjungan'  => $request->latitude_kunjungan,
            'longitude_kunjungan' => $request->longitude_kunjungan,
            'jarak_meter'         => round($jarak, 2),
            'status'              => $status,
        ]);

        $jarakBulat = round($jarak, 1);

        return response()->json([
            'status'          => $status,
            'jarak'           => $jarakBulat,
            'threshold'       => $thresholdEfektif,          // total threshold efektif
            'threshold_admin' => $thresholdAdmin,             // threshold yg ditentukan admin
            'acc_toko'        => round($accGpsToko, 1),       // akurasi GPS toko saat input titik awal
            'acc_sales'       => round($accGpsSales, 1),      // akurasi GPS sales/customer saat kunjungan
            'nama_toko'       => $toko->nama_toko,
            'message'         => $status === 'diterima'
                ? "Kunjungan diterima! Kamu berada {$jarakBulat} meter dari {$toko->nama_toko}."
                : "Kunjungan ditolak. Kamu berada {$jarakBulat} meter dari toko, batas {$thresholdEfektif} meter.",
        ]);
    }

    // ── Helper: Haversine formula ─────────────────────────────────────────────
    private function hitungJarak(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) * sin($dLat / 2)
           + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
           * sin($dLng / 2) * sin($dLng / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }
}