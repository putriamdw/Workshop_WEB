<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\GeneratePdfController;
use App\Http\Controllers\KategoriController;
use App\Http\Middleware\RoleMiddleware;
use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

// Google OAuth
Route::get('/auth/google/redirect', [AuthController::class, 'redirectToGoogle'])
    ->name('google.redirect');

Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])
    ->name('google.callback');

// OTP (TANPA auth, TANPA guest)
Route::get('/verify-otp', [AuthController::class, 'showOtpForm'])
    ->name('otp.form');

Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])
    ->name('otp.verify');

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

Route::middleware(['auth'])->group(function () {
    Route::get('/generate-sertifikat', 
        [GeneratePdfController::class, 'sertifikat']
    )->name('pdf.sertifikat');

    Route::get('/generate-undangan', 
        [GeneratePdfController::class, 'undangan']
    )->name('pdf.undangan');

});

require __DIR__.'/auth.php';
