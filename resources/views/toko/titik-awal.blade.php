@extends('layouts.main')
@section('title', 'Input Titik Awal')
@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header">
                <h4 class="card-title mb-0">
                    <i class="mdi mdi-map-marker-plus"></i>
                    Input Titik Awal — {{ $toko->nama_toko }}
                </h4>
            </div>
            <div class="card-body">

                <div class="alert alert-info">
                    <i class="mdi mdi-information me-1"></i>
                    Klik tombol di bawah untuk mengambil koordinat GPS posisi kamu sekarang
                    sebagai titik lokasi toko. Pastikan kamu berada
                    <strong>di dalam atau tepat di depan toko</strong> saat menekan tombol ini.
                </div>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if($toko->latitude && $toko->longitude)
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

                <form action="{{ route('toko.simpan-titik-awal', $toko->id_toko) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Latitude</label>
                        <input type="text" id="latitude" name="latitude"
                               class="form-control"
                               value="{{ $toko->latitude ?? '' }}"
                               placeholder="Klik Ambil GPS..." readonly required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Longitude</label>
                        <input type="text" id="longitude" name="longitude"
                               class="form-control"
                               value="{{ $toko->longitude ?? '' }}"
                               placeholder="Klik Ambil GPS..." readonly required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Akurasi GPS</label>
                        <input type="text" id="akurasi" class="form-control"
                               placeholder="-" readonly>
                        <small class="text-muted">Makin kecil angkanya, makin akurat.</small>
                    </div>

                    <div id="statusGps" class="mb-3"></div>

                    <div class="d-flex gap-2 mb-3">
                        <button type="button" id="btnAmbilGps" class="btn btn-info">
                            <i class="mdi mdi-crosshairs-gps"></i> Ambil GPS Sekarang
                        </button>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" id="btnSimpan" class="btn btn-primary" disabled>
                            <i class="mdi mdi-content-save"></i> Simpan Koordinat
                        </button>
                        <a href="{{ route('toko.index') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection

@section('script-page')
<script>
const btnAmbil  = document.getElementById('btnAmbilGps');
const btnSimpan = document.getElementById('btnSimpan');
const inputLat  = document.getElementById('latitude');
const inputLng  = document.getElementById('longitude');
const inputAkur = document.getElementById('akurasi');
const statusDiv = document.getElementById('statusGps');

btnAmbil.addEventListener('click', () => {
    if (!navigator.geolocation) {
        statusDiv.innerHTML = '<div class="alert alert-danger">Browser tidak mendukung Geolocation.</div>';
        return;
    }

    statusDiv.innerHTML = '<div class="alert alert-warning"><i class="mdi mdi-loading mdi-spin"></i> Mengambil koordinat GPS...</div>';
    btnAmbil.disabled = true;

    navigator.geolocation.getCurrentPosition(
        (pos) => {
            inputLat.value  = pos.coords.latitude;
            inputLng.value  = pos.coords.longitude;
            inputAkur.value = pos.coords.accuracy.toFixed(1) + ' meter';

            btnSimpan.disabled = false;
            btnAmbil.disabled  = false;

            statusDiv.innerHTML = '<div class="alert alert-success"><i class="mdi mdi-check-circle"></i> Koordinat berhasil diambil! Klik Simpan Koordinat untuk menyimpan.</div>';
        },
        (err) => {
            btnAmbil.disabled = false;
            statusDiv.innerHTML = `<div class="alert alert-danger"><i class="mdi mdi-alert"></i> Gagal ambil GPS: ${err.message}</div>`;
        },
        { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
    );
});

@if($toko->latitude && $toko->longitude)
    btnSimpan.disabled = false;
@endif
</script>
@endsection