@extends('layouts.main')
@section('title', 'Pembayaran Berhasil')
@section('content')
<div class="container py-5 text-center" style="max-width:500px">
    <div class="display-1 mb-3">✅</div>
    <h2 class="text-success">Pembayaran Berhasil!</h2>
    <p class="text-muted">Pesanan <code>{{ $pesanan->id_pesanan }}</code> sudah lunas.</p>
    <div class="card text-start mb-4">
        <div class="card-body">
            <p><strong>Kantin:</strong> {{ $pesanan->vendor->nama_kantin }}</p>
            <p><strong>Pembeli:</strong> {{ $pesanan->nama_pembeli }}</p>
            <p><strong>Metode:</strong> {{ strtoupper($pesanan->metode_bayar ?? '-') }}</p>
            @if($pesanan->va_number)
            <p><strong>No. VA:</strong> {{ $pesanan->va_number }}</p>
            @endif
            <p><strong>Total:</strong> {{ $pesanan->total_format }}</p>
            <p><strong>Dibayar:</strong> {{ $pesanan->paid_at?->format('d M Y H:i') }}</p>
        </div>
    </div>
    <table class="table table-sm text-start">
        @foreach($pesanan->details as $d)
        <tr>
            <td>{{ $d->nama_menu }}</td>
            <td class="text-center">x{{ $d->jumlah }}</td>
            <td class="text-end">{{ $d->subtotal_format }}</td>
        </tr>
        @endforeach
    </table>
    <a href="{{ route('customer.home') }}" class="btn btn-primary mt-3">Pesan Lagi</a>
</div>
@endsection