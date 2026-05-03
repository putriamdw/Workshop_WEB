@extends('layouts.main')
@section('title', 'Scan QR Code Customer')
@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center"
                 style="background: linear-gradient(135deg, #667eea, #764ba2);">
                <h4 class="card-title mb-0 text-white">
                    <i class="mdi mdi-qrcode-scan"></i> Scan QR Code Customer
                </h4>
                <a href="{{ route('vendor.pesanan.index') }}" class="btn btn-light btn-sm">
                    ← Kembali
                </a>
            </div>
            <div class="card-body">

                {{-- Instruksi --}}
                <div id="instruksi" class="alert alert-info d-flex align-items-center mb-3" role="alert">
                    <i class="mdi mdi-information me-2" style="font-size:1.2rem;"></i>
                    <small>Klik <strong>Mulai Scan</strong> lalu arahkan kamera ke QR Code dari HP customer.</small>
                </div>

                {{-- Area kamera --}}
                <div id="reader" style="width:100%; border-radius:12px; overflow:hidden; border:2px dashed #dee2e6;"></div>

                {{-- Tombol --}}
                <div id="tombolScan" class="text-center mt-3 d-flex gap-2 justify-content-center">
                    <button id="btnMulai" class="btn btn-primary px-4">
                        <i class="mdi mdi-camera"></i> Mulai Scan
                    </button>
                    <button id="btnStop" class="btn btn-secondary px-4" style="display:none;">
                        <i class="mdi mdi-stop"></i> Stop
                    </button>
                </div>

                {{-- Hasil ditemukan --}}
                <div id="hasilScan" class="mt-4" style="display:none;">
                    <div class="alert alert-success d-flex align-items-center">
                        <i class="mdi mdi-check-circle me-2" style="font-size:1.5rem;"></i>
                        <strong>Pesanan Ditemukan!</strong>
                    </div>

                    <div class="card border-success mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">ID Pesanan</span>
                                <code id="resId"></code>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Pembeli</span>
                                <span id="resPembeli"></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Total</span>
                                <strong class="text-success" id="resTotal"></strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Status</span>
                                <span id="resStatus"></span>
                            </div>
                        </div>
                    </div>

                    <p class="fw-bold mb-2">Menu yang Dipesan:</p>
                    <div class="card mb-3">
                        <div class="card-body p-0">
                            <table class="table table-sm mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Menu</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody id="resMenu"></tbody>
                            </table>
                        </div>
                    </div>

                    <div class="text-center">
                        <button id="btnScanLagi" class="btn btn-outline-primary">
                            <i class="mdi mdi-refresh"></i> Scan Lagi
                        </button>
                    </div>
                </div>

                {{-- Tidak ditemukan --}}
                <div id="tidakDitemukan" class="mt-4" style="display:none;">
                    <div class="alert alert-danger d-flex align-items-center">
                        <i class="mdi mdi-alert-circle me-2" style="font-size:1.5rem;"></i>
                        <span id="pesanError"></span>
                    </div>
                    <div class="text-center">
                        <button id="btnScanLagi2" class="btn btn-outline-primary">
                            <i class="mdi mdi-refresh"></i> Scan Lagi
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<audio id="beep" src="{{ asset('assets/sounds/beep.mp3') }}" preload="auto"></audio>
@endsection

@section('script-page')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
let html5QrCode = null;
let sudahScan   = false;

const btnMulai       = document.getElementById('btnMulai');
const btnStop        = document.getElementById('btnStop');
const btnScanLagi    = document.getElementById('btnScanLagi');
const btnScanLagi2   = document.getElementById('btnScanLagi2');
const hasilScan      = document.getElementById('hasilScan');
const tidakDitemukan = document.getElementById('tidakDitemukan');
const reader         = document.getElementById('reader');
const instruksi      = document.getElementById('instruksi');
const tombolScan     = document.getElementById('tombolScan');
const beep           = document.getElementById('beep');

function sembunyikanAreaScan() {
    reader.setAttribute('style', 'display:none !important');
    instruksi.setAttribute('style', 'display:none !important');
    tombolScan.setAttribute('style', 'display:none !important');
}

function tampilkanAreaScan() {
    reader.setAttribute('style', 'width:100%; border-radius:12px; overflow:hidden; border:2px dashed #dee2e6;');
    instruksi.setAttribute('style', 'display:flex !important');
    tombolScan.setAttribute('style', 'display:flex; justify-content:center; gap:8px; margin-top:12px;');
}

function resetTampilan() {
    hasilScan.style.display      = 'none';
    tidakDitemukan.style.display = 'none';
    tampilkanAreaScan();
    btnMulai.style.display = 'inline-block';
    btnStop.style.display  = 'none';
    sudahScan = false;
}

function mulaiScanner() {
    resetTampilan();
    html5QrCode = new Html5Qrcode("reader");
    btnMulai.style.display = 'none';
    btnStop.style.display  = 'inline-block';

    html5QrCode.start(
        { facingMode: "environment" },
        { fps: 10, qrbox: { width: 280, height: 280 } },
        async (decodedText) => {
            if (sudahScan) return;
            sudahScan = true;

            beep.currentTime = 0;
            beep.play();

            // Sembunyikan dulu SEBELUM stop scanner
            sembunyikanAreaScan();

            // Stop scanner di background
            html5QrCode.stop().catch(err => console.error(err));

            // Ekstrak id_pesanan
            let idPesanan = decodedText;
            const match   = decodedText.match(/\/pesan\/sukses\/([^\/\?#]+)/);
            if (match) idPesanan = match[1];

            try {
                const res  = await fetch(`{{ url('/vendor/pesanan/cari') }}/${idPesanan}`);
                const data = await res.json();

                if (data.found) {
                    document.getElementById('resId').textContent      = data.id_pesanan;
                    document.getElementById('resPembeli').textContent = data.nama_pembeli;
                    document.getElementById('resTotal').textContent   = data.total_format;

                    const statusEl = document.getElementById('resStatus');
                    statusEl.innerHTML = data.status_bayar === 'lunas'
                        ? '<span class="badge bg-success">✅ Lunas</span>'
                        : '<span class="badge bg-warning text-dark">⏳ Belum Bayar</span>';

                    const tbody = document.getElementById('resMenu');
                    tbody.innerHTML = '';
                    data.details.forEach(d => {
                        tbody.innerHTML += `
                            <tr>
                                <td>${d.nama_menu}</td>
                                <td class="text-center">×${d.jumlah}</td>
                                <td class="text-end">${d.subtotal}</td>
                            </tr>`;
                    });

                    hasilScan.style.display = 'block';
                } else {
                    document.getElementById('pesanError').textContent = data.message;
                    tidakDitemukan.style.display = 'block';
                }
            } catch (e) {
                alert('Gagal menghubungi server: ' + e.message);
            }
        },
        (error) => {}
    ).catch(err => alert('Kamera tidak bisa diakses: ' + err));
}

btnMulai.addEventListener('click',  mulaiScanner);
btnStop.addEventListener('click',   () => {
    html5QrCode.stop().catch(err => console.error(err));
    resetTampilan();
});
btnScanLagi.addEventListener('click',  mulaiScanner);
btnScanLagi2.addEventListener('click', mulaiScanner);
</script>

{{-- Floating nav untuk HP --}}
<div class="d-md-none" style="position:fixed; bottom:20px; right:20px; z-index:9999;">
    <div class="dropdown dropup">
        <button class="btn btn-purple rounded-circle shadow"
                style="width:56px; height:56px; font-size:1.4rem; background:#764ba2; color:white;"
                data-bs-toggle="dropdown">
            ☰
        </button>
        <ul class="dropdown-menu dropdown-menu-end mb-2">
            <li><a class="dropdown-item" href="{{ route('vendor.dashboard') }}">🏠 Dashboard</a></li>
            <li><a class="dropdown-item" href="{{ route('vendor.menu.index') }}">🍽️ Kelola Menu</a></li>
            <li><a class="dropdown-item" href="{{ route('vendor.pesanan.index') }}">📋 Pesanan Lunas</a></li>
            <li><a class="dropdown-item" href="{{ route('vendor.pesanan.scan-qr') }}">🔍 Scan QR</a></li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="dropdown-item text-danger">🚪 Logout</button>
                </form>
            </li>
        </ul>
    </div>
</div>
@endsection