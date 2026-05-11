@extends('layouts.main')
@section('title', 'QR Code Kantin Saya')
@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center"
                 style="background: linear-gradient(135deg, #667eea, #764ba2);">
                <h4 class="card-title mb-0 text-white">
                    <i class="mdi mdi-qrcode"></i> QR Code Kantin Saya
                </h4>
                <a href="{{ route('vendor.dashboard') }}" class="btn btn-light btn-sm">← Kembali</a>
            </div>
            <div class="card-body text-center p-4">

                <h5 class="fw-bold mb-1">{{ $toko->nama_toko }}</h5>
                <p class="text-muted small mb-1">{{ $toko->alamat ?? '-' }}</p>
                <p class="text-muted small mb-3">
                    Radius toleransi: <strong>{{ $toko->accuracy }} meter</strong>
                </p>

                @if(!$toko->latitude || !$toko->longitude)
                <div class="alert alert-warning mb-3">
                    <i class="mdi mdi-alert me-1"></i>
                    Koordinat belum diatur.
                    <a href="{{ route('vendor.titik-awal') }}" class="alert-link">Input titik awal dulu</a>
                    agar QR Code ini bisa digunakan untuk kunjungan.
                </div>
                @endif

                <div class="border rounded p-3 d-inline-block mb-3 bg-white">
                    <img src="data:image/svg+xml;base64,{{ $qrSvg }}"
                         style="width:220px; height:220px;"
                         alt="QR Code {{ $toko->kode_toko }}">
                </div>

                <p class="mb-1"><code class="fs-5">{{ $toko->kode_toko }}</code></p>
                <p class="text-muted small mb-4">
                    Tempel QR Code ini di lokasi kantinmu agar customer bisa scan.
                </p>

                <button onclick="window.print()" class="btn btn-primary w-100">
                    <i class="mdi mdi-printer"></i> Cetak QR Code
                </button>

            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .sidebar, .navbar, .footer, .btn, .card-header { display: none !important; }
    .card { box-shadow: none !important; border: none !important; }
    body { background: white !important; }
}
</style>
@endsection