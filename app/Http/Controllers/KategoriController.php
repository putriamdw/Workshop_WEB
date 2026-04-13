<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kategori;

class KategoriController extends Controller
{
    // Read, tampilkan semua kategori
    public function index()
    {
        $kategoris = Kategori::all();
        return view('kategori.index', compact('kategoris'));
    }

    // Form tambah kategori
    public function create()
    {
        return view('kategori.create');
    }

    // Simpan kategori baru
    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
        ]);

        Kategori::create($request->all());

        return redirect()->route('kategori.index')
                         ->with('success', 'Kategori berhasil ditambahkan!');
    }

    // Form edit kategori
    public function edit($idkategori)
    {
        $kategori = Kategori::findOrFail($idkategori);
        return view('kategori.edit', compact('kategori'));
    }

    // Simpan perubahan kategori
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

    // Hapus kategori
    public function destroy($idkategori)
    {
        $kategori = Kategori::findOrFail($idkategori);
        $kategori->delete();

        return redirect()->route('kategori.index')
                         ->with('success', 'Kategori berhasil dihapus!');
    }
}
