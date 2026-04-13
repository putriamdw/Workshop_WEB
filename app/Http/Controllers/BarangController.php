<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class BarangController extends Controller
{
    
    public function index()
    {
        $barang = Barang::orderBy('created_at', 'desc')->get();
        return view('barang.index', compact('barang'));
    }

    public function create()
    {
        return view('barang.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'  => 'required',
            'harga' => 'required|numeric',
        ]);

        Barang::create($request->only('nama', 'harga'));

        return redirect()->route('barang.index')
            ->with('success', 'Data berhasil ditambahkan');
    }

    public function edit($id)
    {
        $barang = Barang::findOrFail($id);
        return view('barang.edit', compact('barang'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama'  => 'required',
            'harga' => 'required|numeric',
        ]);

        $barang = Barang::findOrFail($id);
        $barang->update($request->only('nama', 'harga'));

        return redirect()->route('barang.index')
            ->with('success', 'Data berhasil diupdate');
    }

    public function destroy($id)
    {
        Barang::destroy($id);

        return redirect()->route('barang.index')
            ->with('success', 'Data berhasil dihapus');
    }
}