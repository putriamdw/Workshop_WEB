<?php

namespace App\Http\Controllers;

use App\Models\Barang; // model tabel barang di db
use Illuminate\Http\Request; // menangkap input dari form
use Barryvdh\DomPDF\Facade\Pdf; // library DomPDF untuk generate PDF

class BarangController extends Controller
{
    public function index()
    {
        $barang = Barang::orderBy('created_at','desc')->get(); // ambil semua data barang dari db, diurutkan dari yang terbaru
        return view('barang.index', compact('barang')); // compact: kirim $barang ke view
    }

    public function create()
    {
        return view('barang.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'harga' => 'required|numeric'
        ]);

        Barang::create($request->only('nama','harga')); // hanya field nama dan harga yang diambil dari form

        return redirect()->route('barang.index')
            ->with('success','Data berhasil ditambahkan');
    }

    public function edit($id)
    {
        $barang = Barang::findOrFail($id); // ambil data berdasarkan id, jika tidak ada maka otomatis error
        return view('barang.edit', compact('barang'));
    }

    public function update(Request $request, $id) // sama seperti store: validasi, cari data, update, redirect
    {
        $request->validate([
            'nama' => 'required',
            'harga' => 'required|numeric'
        ]);

        $barang = Barang::findOrFail($id);
        $barang->update($request->only('nama','harga'));

        return redirect()->route('barang.index')
            ->with('success','Data berhasil diupdate');
    }

    public function destroy($id)
    {
        Barang::destroy($id); // hapus berdasarkan id

        return redirect()->route('barang.index')
            ->with('success','Data berhasil dihapus');
    }

    public function cetak(Request $request)
    {

    // validasi input
    $request->validate([
        'selected' => 'required|array|min:1',
        'x' => 'required|numeric|min:1|max:5',
        'y' => 'required|numeric|min:1|max:8',
    ], [
        'selected.required' => 'Pilih minimal satu barang untuk dicetak!',
    ]);

    // ambil data berdasarkan id yang dicentang
    // whereIn() = ambil banyak data sekaligus
    $barang = Barang::whereIn('id_barang', $request->selected)->get();

    // hitung posisi index awal (TnJ 108: 5 kolom, 8 baris)
    // rumus: ((Baris - 1) * Total_Kolom) + (Kolom - 1)
    $startIndex = (($request->y - 1) * 5) + ($request->x - 1);
    $mm = 2.83465;
    $pdf = Pdf::loadView('barang.cetak', [ // kirim data ke view cetak
        'barang' => $barang,
        'startIndex' => $startIndex
    ])->setPaper([0, 0, 210 * $mm, 167 * $mm], 'portrait');

    return $pdf->stream('label-barang.pdf'); // stream = langsung tampil di browser, tidak otomatis download
    }
}