@extends('layouts.main')
@section('title', 'Pembayaran')
@section('content')
<div class="container py-5" style="max-width:600px">
    <div class="card shadow-sm border-0">
        <div class="card-header fw-bold">Detail Pesanan #{{ $pesanan->id_pesanan }}</div>
        <div class="card-body">
            <table class="table table-sm mb-3">
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
            <div class="d-grid">
                <button id="pay-button" class="btn btn-success btn-lg">💳 Bayar Sekarang</button>
            </div>
            <p class="text-center text-muted small mt-3">
                Mendukung Virtual Account (BCA, BNI, BRI, Mandiri) & QRIS
            </p>
        </div>
    </div>
</div>

<script src="{{ $isProduction
    ? 'https://app.midtrans.com/snap/snap.js'
    : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
    data-client-key="{{ $clientKey }}"></script>

<script>
document.getElementById('pay-button').addEventListener('click', function () {
    snap.pay('{{ $pesanan->midtrans_token }}', {
        onSuccess: function(result) {
            window.location.href = '{{ route("customer.sukses", $pesanan->id_pesanan) }}';
        },
        onPending: function(result) {
            startPolling();
        },
        onError: function(result) {
            alert('Terjadi kesalahan. Silakan coba lagi.');
        },
        onClose: function() {
            startPolling();
        }
    });
});

function startPolling() {
    const interval = setInterval(async () => {
        const res  = await fetch('{{ route("customer.cek-status", $pesanan->id_pesanan) }}');
        const data = await res.json();
        if (data.status === 'lunas' && data.redirect_url) {
            clearInterval(interval);
            window.location.href = data.redirect_url;
        }
    }, 5000);
    setTimeout(() => clearInterval(interval), 600000); // stop setelah 10 menit
}
</script>
@endsection