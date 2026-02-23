<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Http\Request;

class BukuController extends Controller
{
    // READ (Tampilkan semua buku)
    public function index()
    {
        $bukus = Buku::with('kategori')->get();
        return view('buku.index', compact('bukus'));
    }

    // CREATE (Form tambah buku)
    public function create()
    {
        $kategori = Kategori::all();
        return view('buku.create', compact('kategori'));
    }

    // STORE (Simpan buku baru)
    public function store(Request $request)
    {
        $request->validate([
            'idkategori' => 'required',
            'kode' => 'required',
            'judul' => 'required',
            'pengarang' => 'required',
        ]);

        Buku::create($request->all());

        return redirect()->route('buku.index')
                         ->with('success', 'Buku berhasil ditambahkan');
    }

    // EDIT (Form edit buku)
    public function edit($idbuku)
    {
        $buku = Buku::findOrFail($idbuku);
        $kategori = Kategori::all();

        return view('buku.edit', compact('buku', 'kategori'));
    }

    // UPDATE (Simpan perubahan buku)
    public function update(Request $request, $idbuku)
    {
        $request->validate([
            'idkategori' => 'required',
            'kode' => 'required',
            'judul' => 'required',
            'pengarang' => 'required',
        ]);

        $buku = Buku::findOrFail($idbuku);
        $buku->update($request->all());

        return redirect()->route('buku.index')
                         ->with('success', 'Buku berhasil diupdate');
    }

    // DELETE (Hapus buku)
    public function destroy($idbuku)
    {
        $buku = Buku::findOrFail($idbuku);
        $buku->delete();

        return redirect()->route('buku.index')
                         ->with('success', 'Buku berhasil dihapus');
    }
}
