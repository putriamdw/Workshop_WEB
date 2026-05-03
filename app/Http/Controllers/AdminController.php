<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Pesanan;

class AdminController extends Controller
{
    
    public function dashboard()
    {
        $totalVendor     = Vendor::count();
        $totalCustomer   = User::whereIn('role', ['customer', 'user'])->count();
        $totalPesanan    = Pesanan::count();
        $totalPendapatan = Pesanan::where('status_bayar', 'lunas')->sum('total');

        $pesananTerbaru = Pesanan::with(['vendor', 'user'])
            ->latest()->take(10)->get();

        $vendorTop = Vendor::withCount(['pesanan as total_pesanan' => fn($q) =>
                $q->where('status_bayar', 'lunas')])
            ->withSum(['pesanan as total_pendapatan' => fn($q) =>
                $q->where('status_bayar', 'lunas')], 'total')
            ->orderByDesc('total_pendapatan')
            ->take(5)->get();

        return view('admin.dashboard', compact(
            'totalVendor', 'totalCustomer', 'totalPesanan',
            'totalPendapatan', 'pesananTerbaru', 'vendorTop'
        ));
    }

    public function vendorIndex(Request $request)
    {
        $vendors = Vendor::with('user')
            ->withCount('menus')
            ->withCount(['pesanan as pesanan_lunas' => fn($q) => $q->where('status_bayar', 'lunas')])
            ->withSum(['pesanan as total_pendapatan' => fn($q) => $q->where('status_bayar', 'lunas')], 'total')
            ->when($request->search, fn($q) => $q->where('nama_kantin', 'ilike', '%' . $request->search . '%'))
            ->latest()->paginate(15);

        return view('admin.vendor.index', compact('vendors'));
    }

    public function vendorShow(Vendor $vendor)
    {
        $vendor->load(['user', 'menus']);
        $pesanan = $vendor->pesanan()->with('details')->latest()->paginate(15);
        return view('admin.vendor.show', compact('vendor', 'pesanan'));
    }

    public function vendorToggle(Vendor $vendor)
    {
        $vendor->update(['aktif' => !$vendor->aktif]);
        $status = $vendor->aktif ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Vendor {$vendor->nama_kantin} berhasil {$status}.");
    }

    public function pesananIndex(Request $request)
    {
        $pesanan = Pesanan::with(['vendor', 'user'])
            ->when($request->status, fn($q) => $q->where('status_bayar', $request->status))
            ->when($request->vendor, fn($q) => $q->where('id_vendor', $request->vendor))
            ->latest()->paginate(20);

        $vendors = Vendor::select('id_vendor', 'nama_kantin')->get();
        return view('admin.pesanan.index', compact('pesanan', 'vendors'));
    }

    public function pesananShow(string $idPesanan)
    {
        $pesanan = Pesanan::with(['vendor', 'user', 'details'])->findOrFail($idPesanan);
        return view('admin.pesanan.show', compact('pesanan'));
    }

    public function scanQr()
    {
        return view('admin.scan-qr');
    }

    // API: cari pesanan berdasarkan id yang di-scan (admin bisa lihat semua vendor)
    public function cariPesanan(string $id)
    {
        $pesanan = Pesanan::with(['details', 'vendor'])
            ->where('id_pesanan', $id)
            ->first();

        if (!$pesanan) {
            return response()->json([
                'found'   => false,
                'message' => 'Pesanan tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'found'        => true,
            'id_pesanan'   => $pesanan->id_pesanan,
            'nama_pembeli' => $pesanan->nama_pembeli,
            'nama_kantin'  => $pesanan->vendor->nama_kantin,
            'status_bayar' => $pesanan->status_bayar,
            'total_format' => $pesanan->total_format,
            'details'      => $pesanan->details->map(fn($d) => [
                'nama_menu' => $d->nama_menu,
                'jumlah'    => $d->jumlah,
                'subtotal'  => $d->subtotal_format,
            ]),
        ]);
    }
}