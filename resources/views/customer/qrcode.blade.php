<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code — {{ $pesanan->id_pesanan }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
        .qr-card { border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
        .badge-lunas { background: #28a745; }
        .badge-belum { background: #ffc107; color: #333; }
        .qr-img { border: 4px solid #f0f0f0; border-radius: 12px; padding: 8px; background: white; }
        .id-pesanan { font-family: monospace; background: #f8f9fa; padding: 6px 12px; border-radius: 8px; font-size: 0.9rem; }
    </style>
</head>
<body>
<div class="container py-5 d-flex justify-content-center align-items-center" style="min-height:100vh;">
    <div class="card qr-card" style="max-width:380px; width:100%;">
        <div class="card-body p-4 text-center">

            {{-- Header --}}
            <div class="mb-3">
                <div style="font-size:2.5rem;">🎫</div>
                <h5 class="fw-bold mb-1">Bukti Pesanan</h5>
                <span class="id-pesanan">{{ $pesanan->id_pesanan }}</span>
            </div>

            {{-- QR Code --}}
            <div class="mb-3">
                <img src="data:image/svg+xml;base64,{{ $qrSvg }}"
                     alt="QR Code"
                     class="qr-img"
                     style="width:220px; height:220px;">
                <p class="text-muted small mt-2 mb-0">Tunjukkan ke vendor untuk verifikasi</p>
            </div>

            <hr>

            {{-- Info Pesanan --}}
            <div class="text-start mb-3">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Kantin</span>
                    <strong>{{ $pesanan->vendor->nama_kantin }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Pembeli</span>
                    <span>{{ $pesanan->nama_pembeli }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Total</span>
                    <strong class="text-success">{{ $pesanan->total_format }}</strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Status</span>
                    @if($pesanan->status_bayar === 'lunas')
                        <span class="badge badge-lunas">✅ Lunas</span>
                    @else
                        <span class="badge badge-belum">⏳ Belum Bayar</span>
                    @endif
                </div>
            </div>

            <hr>

            {{-- Menu --}}
            <div class="text-start mb-3">
                <p class="text-muted small fw-bold mb-2">MENU YANG DIPESAN</p>
                @foreach($pesanan->details as $d)
                <div class="d-flex justify-content-between mb-1">
                    <span>{{ $d->nama_menu }} <span class="text-muted">×{{ $d->jumlah }}</span></span>
                    <span>{{ $d->subtotal_format }}</span>
                </div>
                @endforeach
            </div>

            {{-- Tombol --}}
            <a href="{{ route('customer.sukses', $pesanan->id_pesanan) }}"
               class="btn btn-outline-secondary btn-sm w-100">
                Lihat Halaman Sukses
            </a>

        </div>
    </div>
</div>
</body>
</html>