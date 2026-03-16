<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use Carbon\Carbon;

class PosController extends Controller
{
    public function index()
    {
        return view('pos.index');
    }

    // AJAX: cari barang berdasarkan kode
    public function cariBarang(Request $req)
    {
        $barang = Barang::where('id_barang', strtoupper(trim($req->kode)))->first();

        if (!$barang) {
            return response()->json([
                'status'  => 'error',
                'code'    => 404,
                'message' => 'Barang tidak ditemukan',
                'data'    => null
            ], 404);
        }

        return response()->json([
            'status'  => 'success',
            'code'    => 200,
            'message' => 'Barang ditemukan',
            'data'    => $barang
        ]);
    }

    public function bayar(Request $req)
{
    // jQuery kirim items sebagai JSON string, Axios kirim sebagai array
    // Handle keduanya di sini
    $items = $req->items;
    if (is_string($items)) {
        $items = json_decode($items, true);
    }

    // Validasi manual
    if (empty($items) || !is_array($items)) {
        return response()->json([
            'status'  => 'error',
            'code'    => 422,
            'message' => 'Items tidak boleh kosong',
            'data'    => null
        ], 422);
    }

    $total = $req->total;
    if (empty($total)) {
        return response()->json([
            'status'  => 'error',
            'code'    => 422,
            'message' => 'Total tidak boleh kosong',
            'data'    => null
        ], 422);
    }

    try {
        $penjualan = Penjualan::create([
            'timestamp' => Carbon::now(),
            'total'     => $total,
        ]);

        foreach ($items as $item) {
            PenjualanDetail::create([
                'id_penjualan' => $penjualan->id_penjualan,
                'id_barang'    => $item['id_barang'],
                'jumlah'       => $item['jumlah'],
                'subtotal'     => $item['subtotal'],
            ]);
        }

        return response()->json([
            'status'  => 'success',
            'code'    => 200,
            'message' => 'Transaksi berhasil disimpan',
            'data'    => ['id_penjualan' => $penjualan->id_penjualan]
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status'  => 'error',
            'code'    => 500,
            'message' => 'Gagal menyimpan: ' . $e->getMessage(),
            'data'    => null
        ], 500);
    }
}

    public function indexAxios()
    {
        return view('pos.index-axios');
    }
}