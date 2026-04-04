@extends('layouts.main')
@section('title', 'Detail Pesanan')
@section('content')
<div class="container py-4" style="max-width:700px">
    <h2 class="mb-4">Detail Pesanan</h2>

    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body">
            <p><strong>ID Pesanan:</strong> <code>{{ $pesanan->id_pesanan }}</code></p>
            <p><strong>Pembeli:</strong> {{ $pesanan->nama_pembeli }}</p>
            <p><strong>Metode Bayar:</strong> {{ strtoupper($pesanan->metode_bayar ?? '-') }}</p>
            @if($pesanan->va_number)
            <p><strong>No. VA:</strong> {{ $pesanan->va_number }}</p>
            @endif
            <p><strong>Total:</strong> {{ $pesanan->total_format }}</p>
            <p><strong>Dibayar:</strong> {{ $pesanan->paid_at?->format('d M Y H:i') }}</p>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header fw-bold">Detail Item</div>
        <div class="card-body p-0">
            <table class="table mb-0">
                <thead class="table-light">
                    <tr><th>Menu</th><th class="text-center">Qty</th><th class="text-end">Subtotal</th></tr>
                </thead>
                <tbody>
                    @foreach($pesanan->details as $d)
                    <tr>
                        <td>{{ $d->nama_menu }}</td>
                        <td class="text-center">{{ $d->jumlah }}</td>
                        <td class="text-end">{{ $d->subtotal_format }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="fw-bold">
                        <td colspan="2">Total</td>
                        <td class="text-end">{{ $pesanan->total_format }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <a href="{{ route('vendor.pesanan.index') }}" class="btn btn-secondary mt-3">← Kembali</a>
</div>
@endsection