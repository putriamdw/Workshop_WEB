@extends('layouts.main')
@section('title', 'Pembayaran Berhasil')
@section('content')
<div class="container py-5" style="max-width:560px">

    {{-- Header --}}
    <div class="text-center mb-4">
        <div style="font-size:4rem;">✅</div>
        <h2 class="text-success fw-bold">Pembayaran Berhasil!</h2>
        <p class="text-muted">Pesanan <code class="fw-bold">{{ $pesanan->id_pesanan }}</code> sudah lunas.</p>
    </div>

    {{-- Detail Pesanan --}}
    <div class="card shadow-sm mb-3">
        <div class="card-header fw-bold bg-success text-white">
            <i class="mdi mdi-receipt"></i> Detail Pesanan
        </div>
        <div class="card-body">
            <table class="table table-sm mb-0">
                <tr>
                    <td width="40%" class="text-muted">Kantin</td>
                    <td><strong>{{ $pesanan->vendor->nama_kantin }}</strong></td>
                </tr>
                <tr>
                    <td class="text-muted">Pembeli</td>
                    <td>{{ $pesanan->nama_pembeli }}</td>
                </tr>
                <tr>
                    <td class="text-muted">Metode</td>
                    <td>{{ strtoupper($pesanan->metode_bayar ?? '-') }}</td>
                </tr>
                @if($pesanan->va_number)
                <tr>
                    <td class="text-muted">No. VA</td>
                    <td>{{ $pesanan->va_number }}</td>
                </tr>
                @endif
                <tr>
                    <td class="text-muted">Total</td>
                    <td><strong class="text-success">{{ $pesanan->total_format }}</strong></td>
                </tr>
                <tr>
                    <td class="text-muted">Dibayar</td>
                    <td>{{ $pesanan->paid_at?->format('d M Y H:i') }}</td>
                </tr>
            </table>
        </div>
    </div>

    {{-- Item Pesanan --}}
    <div class="card shadow-sm mb-3">
        <div class="card-header fw-bold">
            <i class="mdi mdi-food"></i> Menu yang Dipesan
        </div>
        <div class="card-body p-0">
            <table class="table table-sm mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Menu</th>
                        <th class="text-center">Qty</th>
                        <th class="text-end">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pesanan->details as $d)
                    <tr>
                        <td>{{ $d->nama_menu }}</td>
                        <td class="text-center">x{{ $d->jumlah }}</td>
                        <td class="text-end">{{ $d->subtotal_format }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- QR Code --}}
    <div class="card shadow-sm mb-4 text-center">
        <div class="card-header fw-bold">
            <i class="mdi mdi-qrcode"></i> QR Code Pesanan
        </div>
        <div class="card-body">
            <p class="text-muted small mb-3">
                Tunjukkan QR Code ini ke vendor untuk verifikasi pesanan.
                <strong>Simpan halaman QR Code agar bisa diakses kembali.</strong>
            </p>
            <img src="data:image/svg+xml;base64,{{ $qrSvg }}"
                 alt="QR Code {{ $pesanan->id_pesanan }}"
                 style="width:200px; height:200px;">
            <p class="text-muted small mt-2 mb-3"><code>{{ $pesanan->id_pesanan }}</code></p>
            <a href="{{ route('customer.qrcode', $pesanan->id_pesanan) }}"
               class="btn btn-outline-primary btn-sm" target="_blank">
                <i class="mdi mdi-open-in-new"></i> Buka & Simpan Halaman QR Code
            </a>
        </div>
    </div>

    {{-- Tombol --}}
    <div class="text-center">
        <a href="{{ route('customer.home') }}" class="btn btn-success px-4">
            <i class="mdi mdi-silverware-fork-knife"></i> Pesan Lagi
        </a>
    </div>

</div>
@endsection