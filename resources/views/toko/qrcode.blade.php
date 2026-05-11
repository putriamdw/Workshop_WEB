@extends('layouts.main')
@section('title', 'QR Code Toko')
@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0"><i class="mdi mdi-qrcode"></i> QR Code Toko</h4>
                <a href="{{ route('toko.index') }}" class="btn btn-outline-secondary btn-sm">← Kembali</a>
            </div>
            <div class="card-body text-center">
                <h5 class="fw-bold mb-1">{{ $toko->nama_toko }}</h5>
                <p class="text-muted small mb-3">{{ $toko->alamat ?? '-' }}</p>

                <div class="border rounded p-3 d-inline-block mb-3">
                    <img src="data:image/svg+xml;base64,{{ $qrSvg }}"
                         style="width:220px; height:220px;"
                         alt="QR Code {{ $toko->kode_toko }}">
                </div>

                <p class="mb-1"><code class="fs-5">{{ $toko->kode_toko }}</code></p>
                <p class="text-muted small">Radius toleransi: {{ $toko->accuracy }} meter</p>

                <button onclick="window.print()" class="btn btn-primary mt-2">
                    <i class="mdi mdi-printer"></i> Cetak QR Code
                </button>
            </div>
        </div>
    </div>
</div>
@endsection