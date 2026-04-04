<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\GeneratePdfController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\WilayahController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\AdminController;
use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

// ── Google OAuth ──────────────────────────────────────────────────────────────
Route::get('/auth/google/redirect', [AuthController::class, 'redirectToGoogle'])
    ->name('google.redirect');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])
    ->name('google.callback');

// ── OTP (tanpa auth) ──────────────────────────────────────────────────────────
Route::get('/verify-otp',  [AuthController::class, 'showOtpForm'])->name('otp.form');
Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])  ->name('otp.verify');

// ── Route yang butuh login ────────────────────────────────────────────────────
// SESUDAH
Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }

    if ($user->role === 'vendor') {
        return redirect()->route('vendor.dashboard');
    }

    // role 'user' / 'customer' → dashboard lama
    return view('dashboard', [
        'totalBuku'     => Buku::count(),
        'totalKategori' => Kategori::count(),
    ]);
})->name('dashboard');

    // Semua user bisa lihat
    Route::get('kategori', [KategoriController::class, 'index'])->name('kategori.index');
    Route::get('buku',     [BukuController::class,     'index'])->name('buku.index');

    // PDF
    Route::get('/generate-sertifikat', [GeneratePdfController::class, 'sertifikat'])->name('pdf.sertifikat');
    Route::get('/generate-undangan',   [GeneratePdfController::class, 'undangan'])  ->name('pdf.undangan');

    // Barang
    Route::post('/barang/cetak', [BarangController::class, 'cetak'])->name('barang.cetak');
    Route::resource('barang', BarangController::class);

    // JS Tugas
    Route::get('/js-tugas/spinner',    fn() => view('tugas_js.js1_spinner'))    ->name('jstugas.tugas1');
    Route::get('/js-tugas/tabel-crud', fn() => view('tugas_js.js2_3_tabel_crud'))->name('jstugas.tugas2_3');
    Route::get('/js-tugas/select',     fn() => view('tugas_js.js4_select'))     ->name('jstugas.tugas4');

    // Wilayah
    Route::get('/wilayah',                                  [WilayahController::class, 'index'])        ->name('wilayah.index');
    Route::get('/wilayah/provinsi',                         [WilayahController::class, 'getProvinsi'])  ->name('wilayah.provinsi');
    Route::get('/wilayah/kota/{id_provinsi}',               [WilayahController::class, 'getKota'])      ->name('wilayah.kota');
    Route::get('/wilayah/kecamatan/{id_kota}',              [WilayahController::class, 'getKecamatan']) ->name('wilayah.kecamatan');
    Route::get('/wilayah/kelurahan/{id_kecamatan}',         [WilayahController::class, 'getKelurahan']) ->name('wilayah.kelurahan');
    Route::get('/wilayah-axios',                            [WilayahController::class, 'indexAxios'])   ->name('wilayah.axios');

    // POS
    Route::get('/pos',              [PosController::class, 'index'])    ->name('pos.index');
    Route::post('/pos/cari-barang', [PosController::class, 'cariBarang'])->name('pos.cari');
    Route::post('/pos/bayar',       [PosController::class, 'bayar'])    ->name('pos.bayar');
    Route::get('/pos-axios',        [PosController::class, 'indexAxios'])->name('pos.axios');

// ── Hanya admin: CRUD kategori & buku ────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('kategori/create',      [KategoriController::class, 'create']) ->name('kategori.create');
    Route::post('kategori',            [KategoriController::class, 'store'])  ->name('kategori.store');
    Route::get('kategori/{id}/edit',   [KategoriController::class, 'edit'])   ->name('kategori.edit');
    Route::put('kategori/{id}',        [KategoriController::class, 'update']) ->name('kategori.update');
    Route::delete('kategori/{id}',     [KategoriController::class, 'destroy'])->name('kategori.destroy');

    Route::get('buku/create',          [BukuController::class, 'create'])->name('buku.create');
    Route::post('buku',                [BukuController::class, 'store']) ->name('buku.store');
    Route::get('buku/{id}/edit',       [BukuController::class, 'edit'])  ->name('buku.edit');
    Route::put('buku/{id}',            [BukuController::class, 'update'])->name('buku.update');
    Route::delete('buku/{id}',         [BukuController::class, 'destroy'])->name('buku.destroy');
});

// ── Customer (tidak perlu login) ──────────────────────────────────────────────
Route::prefix('pesan')->name('customer.')->group(function () {
    Route::get('/',                [PesananController::class, 'index'])      ->name('home');
    Route::get('/kantin/{vendor}', [PesananController::class, 'pilihVendor'])->name('pilih-vendor');
    Route::post('/order',          [PesananController::class, 'store'])      ->name('store');
    Route::get('/bayar/{id}',      [PesananController::class, 'bayar'])      ->name('bayar');
    Route::get('/sukses/{id}',     [PesananController::class, 'sukses'])     ->name('sukses');
    Route::get('/status/{id}',     [PesananController::class, 'cekStatus'])  ->name('cek-status');
});

// ── Webhook Midtrans (tanpa auth, tanpa CSRF) ─────────────────────────────────
Route::post('/webhook/midtrans', [PesananController::class, 'webhook'])
    ->name('webhook.midtrans');

// ── Vendor ────────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:vendor'])->prefix('vendor')->name('vendor.')->group(function () {
    Route::get('/setup',  [VendorController::class, 'setupForm']) ->name('setup');
    Route::post('/setup', [VendorController::class, 'setupStore'])->name('setup.store');

    Route::middleware('vendor.check')->group(function () {
        Route::get('/dashboard', [VendorController::class, 'dashboard'])->name('dashboard');

        Route::prefix('menu')->name('menu.')->group(function () {
            Route::get('/',            [VendorController::class, 'menuIndex'])  ->name('index');
            Route::get('/tambah',      [VendorController::class, 'menuCreate']) ->name('create');
            Route::post('/tambah',     [VendorController::class, 'menuStore'])  ->name('store');
            Route::get('/{menu}/edit', [VendorController::class, 'menuEdit'])   ->name('edit');
            Route::put('/{menu}',      [VendorController::class, 'menuUpdate']) ->name('update');
            Route::delete('/{menu}',   [VendorController::class, 'menuDestroy'])->name('destroy');
        });

        Route::prefix('pesanan')->name('pesanan.')->group(function () {
            Route::get('/',     [VendorController::class, 'pesananIndex'])->name('index');
            Route::get('/{id}', [VendorController::class, 'pesananShow']) ->name('show');
        });
    });
});

// ── Admin ─────────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    Route::prefix('vendor')->name('vendor.')->group(function () {
        Route::get('/',                  [AdminController::class, 'vendorIndex']) ->name('index');
        Route::get('/{vendor}',          [AdminController::class, 'vendorShow'])  ->name('show');
        Route::patch('/{vendor}/toggle', [AdminController::class, 'vendorToggle'])->name('toggle');
    });

    Route::prefix('pesanan')->name('pesanan.')->group(function () {
        Route::get('/',     [AdminController::class, 'pesananIndex'])->name('index');
        Route::get('/{id}', [AdminController::class, 'pesananShow']) ->name('show');
    });
});

require __DIR__.'/auth.php';