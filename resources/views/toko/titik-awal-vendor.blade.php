@extends('layouts.main')
@section('title', 'Input Titik Awal Kantin')
@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header"
                 style="background: linear-gradient(135deg, #667eea, #764ba2);">
                <h4 class="card-title mb-0 text-white">
                    <i class="mdi mdi-map-marker-plus"></i>
                    Input Titik Awal — {{ $vendor->nama_kantin }}
                </h4>
            </div>
            <div class="card-body">

                <div class="alert alert-info">
                    <i class="mdi mdi-information me-1"></i>
                    Pastikan kamu sedang berada <strong>tepat di lokasi kantinmu</strong> sebelum klik
                    <strong>Ambil Lokasi Sekarang</strong>. Koordinat yang diambil akan menjadi
                    titik referensi untuk memverifikasi kunjungan customer.
                </div>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                @if($toko && $toko->latitude && $toko->longitude)
                <div class="alert alert-success">
                    <strong>Koordinat tersimpan:</strong><br>
                    Latitude: {{ $toko->latitude }}<br>
                    Longitude: {{ $toko->longitude }}<br>
                    Radius toleransi: {{ $toko->accuracy }} meter
                </div>
                @else
                <div class="alert alert-warning">
                    <i class="mdi mdi-alert me-1"></i>
                    Koordinat belum diatur. Klik tombol di bawah untuk mengambil lokasi sekarang.
                </div>
                @endif

                <form action="{{ route('vendor.simpan-titik-awal') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Latitude</label>
                        <input type="text" id="latitude" name="latitude"
                               class="form-control"
                               value="{{ $toko->latitude ?? '' }}"
                               placeholder="Klik Ambil Lokasi..." readonly required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Longitude</label>
                        <input type="text" id="longitude" name="longitude"
                               class="form-control"
                               value="{{ $toko->longitude ?? '' }}"
                               placeholder="Klik Ambil Lokasi..." readonly required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Akurasi GPS Perangkat</label>
                        <input type="text" id="akurasiTampil" class="form-control"
                               placeholder="-" readonly>
                        <input type="hidden" id="accuracy" name="accuracy">
                        <small class="text-muted">
                            Makin kecil angkanya, makin akurat. Disarankan di bawah 20 meter.
                        </small>
                    </div>

                    <div id="statusGps" class="mb-3"></div>

                    <div class="d-flex gap-2 mb-3">
                        <button type="button" id="btnAmbilGps" class="btn btn-info px-4">
                            <i class="mdi mdi-crosshairs-gps"></i> Ambil Lokasi Sekarang
                        </button>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" id="btnSimpan" class="btn btn-primary" disabled>
                            <i class="mdi mdi-content-save"></i> Simpan Koordinat
                        </button>
                        <a href="{{ route('vendor.dashboard') }}" class="btn btn-outline-secondary">
                            Batal
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection

@section('script-page')
<script>
const btnAmbil      = document.getElementById('btnAmbilGps');
const btnSimpan     = document.getElementById('btnSimpan');
const inputLat      = document.getElementById('latitude');
const inputLng      = document.getElementById('longitude');
const inputAkurasi  = document.getElementById('accuracy');
const tampilAkurasi = document.getElementById('akurasiTampil');
const statusDiv     = document.getElementById('statusGps');

btnAmbil.addEventListener('click', () => {
    if (!navigator.geolocation) {
        statusDiv.innerHTML = '<div class="alert alert-danger">Browser tidak mendukung Geolocation.</div>';
        return;
    }

    statusDiv.innerHTML = '<div class="alert alert-warning"><i class="mdi mdi-loading mdi-spin"></i> Mengambil koordinat GPS, harap tunggu...</div>';
    btnAmbil.disabled = true;

    navigator.geolocation.getCurrentPosition(
        (pos) => {
            inputLat.value      = pos.coords.latitude;
            inputLng.value      = pos.coords.longitude;
            inputAkurasi.value  = pos.coords.accuracy;
            tampilAkurasi.value = pos.coords.accuracy.toFixed(1) + ' meter';

            btnSimpan.disabled = false;
            btnAmbil.disabled  = false;

            const akurasi = pos.coords.accuracy;
            const warna   = akurasi <= 20 ? 'success' : akurasi <= 50 ? 'warning' : 'danger';
            const pesan   = akurasi <= 20
                ? 'GPS akurat! Koordinat siap disimpan.'
                : akurasi <= 50
                    ? 'GPS cukup akurat. Bisa disimpan.'
                    : 'GPS kurang akurat. Coba di tempat terbuka atau tunggu beberapa saat lagi.';

            statusDiv.innerHTML = `<div class="alert alert-${warna}"><i class="mdi mdi-check-circle"></i> ${pesan}</div>`;
        },
        (err) => {
            btnAmbil.disabled = false;
            statusDiv.innerHTML = `<div class="alert alert-danger"><i class="mdi mdi-alert"></i> Gagal ambil GPS: ${err.message}. Pastikan izin lokasi diaktifkan.</div>`;
        },
        { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
    );
});

// Aktifkan tombol simpan kalau koordinat sudah ada
@if($toko && $toko->latitude && $toko->longitude)
    btnSimpan.disabled = false;
@endif
</script>
@endsection