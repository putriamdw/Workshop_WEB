@extends('layouts.main')
@section('title', 'Dashboard Vendor')
@section('content')
<div class="container py-4">
    <h2 class="mb-4">Dashboard — {{ $vendor->nama_kantin }}</h2>
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5>Total Menu</h5>
                    <h2>{{ $totalMenu }}</h2>
                    <a href="{{ route('vendor.menu.index') }}" class="text-white">Kelola Menu →</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5>Pesanan Lunas</h5>
                    <h2>{{ $pesananLunas }}</h2>
                    <a href="{{ route('vendor.pesanan.index') }}" class="text-white">Lihat Pesanan →</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5>Total Pendapatan</h5>
                    <h2>Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header fw-bold">Pesanan Terbaru (Lunas)</div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr><th>ID Pesanan</th><th>Pembeli</th><th>Total</th><th>Waktu</th><th></th></tr>
                </thead>
                <tbody>
                    @forelse($pesananTerbaru as $p)
                    <tr>
                        <td><code>{{ $p->id_pesanan }}</code></td>
                        <td>{{ $p->nama_pembeli }}</td>
                        <td>{{ $p->total_format }}</td>
                        <td>{{ $p->paid_at?->format('d M Y H:i') }}</td>
                        <td>
                            <a href="{{ route('vendor.pesanan.show', $p->id_pesanan) }}"
                               class="btn btn-sm btn-outline-primary">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted py-3">Belum ada pesanan lunas</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection