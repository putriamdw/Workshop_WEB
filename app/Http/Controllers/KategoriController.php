<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kategori;

class KategoriController extends Controller
{
    // READ (Tampilkan semua kategori)
    public function index()
    {
        $kategoris = Kategori::all();
        return view('kategori.index', compact('kategoris'));
    }

    // CREATE (Form tambah kategori)
    public function create()
    {
        return view('kategori.create');
    }

    // STORE (Simpan kategori baru)
    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
        ]);

        Kategori::create($request->all());

        return redirect()->route('kategori.index')
                         ->with('success', 'Kategori berhasil ditambahkan!');
    }

    // EDIT (Form edit)
    public function edit($idkategori)
    {
        $kategori = Kategori::findOrFail($idkategori);
        return view('kategori.edit', compact('kategori'));
    }

    // UPDATE (Simpan perubahan)
    public function update(Request $request, $idkategori)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
        ]);

        $kategori = Kategori::findOrFail($idkategori);
        $kategori->update($request->all());

        return redirect()->route('kategori.index')
                         ->with('success', 'Kategori berhasil diupdate!');
    }

    // DELETE (Hapus)
    public function destroy($idkategori)
    {
        $kategori = Kategori::findOrFail($idkategori);
        $kategori->delete();

        return redirect()->route('kategori.index')
                         ->with('success', 'Kategori berhasil dihapus!');
    }
}
