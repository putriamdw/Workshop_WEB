<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class BarangController extends Controller
{
    public function index()
    {
        $barang = Barang::orderBy('created_at','desc')->get();
        return view('barang.index', compact('barang'));
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

        Barang::create($request->only('nama','harga'));

        return redirect()->route('barang.index')
            ->with('success','Data berhasil ditambahkan');
    }

    public function edit($id)
    {
        $barang = Barang::findOrFail($id);
        return view('barang.edit', compact('barang'));
    }

    public function update(Request $request, $id)
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
        Barang::destroy($id);

        return redirect()->route('barang.index')
            ->with('success','Data berhasil dihapus');
    }

    public function cetak(Request $request)
    {
    // Validasi input
    $request->validate([
        'selected' => 'required|array|min:1',
        'x' => 'required|numeric|min:1|max:5',
        'y' => 'required|numeric|min:1|max:8',
    ], [
        'selected.required' => 'Pilih minimal satu barang untuk dicetak!',
    ]);

    // Ambil data barang berdasarkan ID yang dicentang
    $barang = Barang::whereIn('id_barang', $request->selected)->get();

    // Hitung posisi index awal (TnJ 108: 5 kolom, 8 baris)
    // Rumus: ((Baris - 1) * Total_Kolom) + (Kolom - 1)
    $startIndex = (($request->y - 1) * 5) + ($request->x - 1);
    $mm = 2.83465;
    $pdf = Pdf::loadView('barang.cetak', [
        'barang' => $barang,
        'startIndex' => $startIndex
    ])->setPaper([0, 0, 210 * $mm, 167 * $mm], 'portrait');

    return $pdf->stream('label-barang.pdf');
    }
}