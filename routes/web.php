<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\BukuController;
use App\Models\Buku;
use App\Models\Kategori;
use App\Http\Middleware\RoleMiddleware;

/*
|--------------------------------------------------------------------------
| Web Routes - Modul 1
|--------------------------------------------------------------------------
*/

// Arahkan halaman awal ke login
Route::get('/', function () {
    return redirect('/login');
});

// Semua user login bisa akses dashboard
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard', [
            'totalBuku' => Buku::count(),
            'totalKategori' => Kategori::count()
        ]);
    })->name('dashboard');

    // Semua user bisa LIHAT (index saja)
    Route::get('kategori', [KategoriController::class, 'index'])->name('kategori.index');
    Route::get('buku', [BukuController::class, 'index'])->name('buku.index');
});


// Hanya admin yang bisa crud
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('kategori/create', [KategoriController::class, 'create'])->name('kategori.create');
    Route::post('kategori', [KategoriController::class, 'store'])->name('kategori.store');
    Route::get('kategori/{id}/edit', [KategoriController::class, 'edit'])->name('kategori.edit');
    Route::put('kategori/{id}', [KategoriController::class, 'update'])->name('kategori.update');
    Route::delete('kategori/{id}', [KategoriController::class, 'destroy'])->name('kategori.destroy');

    Route::get('buku/create', [BukuController::class, 'create'])->name('buku.create');
    Route::post('buku', [BukuController::class, 'store'])->name('buku.store');
    Route::get('buku/{id}/edit', [BukuController::class, 'edit'])->name('buku.edit');
    Route::put('buku/{id}', [BukuController::class, 'update'])->name('buku.update');
    Route::delete('buku/{id}', [BukuController::class, 'destroy'])->name('buku.destroy');
});

require __DIR__.'/auth.php';
