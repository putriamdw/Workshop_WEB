@extends('layouts.main')
@section('title', 'Admin Dashboard')
@section('content')
<div class="container-fluid py-4 px-4">
    <h2 class="mb-4">Dashboard Admin</h2>
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card text-white bg-primary text-center py-3">
                <div class="card-body">
                    <h4>{{ $totalVendor }}</h4><small>Total Vendor</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-white bg-info text-center py-3">
                <div class="card-body">
                    <h4>{{ $totalCustomer }}</h4><small>Total Customer</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-white bg-warning text-center py-3">
                <div class="card-body">
                    <h4>{{ $totalPesanan }}</h4><small>Total Pesanan</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-white bg-success text-center py-3">
                <div class="card-body">
                    <h4>Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h4>
                    <small>Total Pendapatan</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="fw-bold">Pesanan Terbaru</span>
                    <a href="{{ route('admin.pesanan.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr><th>ID</th><th>Pembeli</th><th>Kantin</th><th>Total</th><th>Status</th></tr>
                        </thead>
                        <tbody>
                            @foreach($pesananTerbaru as $p)
                            <tr>
                                <td><a href="{{ route('admin.pesanan.show', $p->id_pesanan) }}">
                                    <code>{{ $p->id_pesanan }}</code></a></td>
                                <td>{{ $p->nama_pembeli }}</td>
                                <td>{{ $p->vendor->nama_kantin }}</td>
                                <td>{{ $p->total_format }}</td>
                                <td>
                                    <span class="badge {{ $p->status_bayar === 'lunas' ? 'bg-success' : ($p->status_bayar === 'expired' ? 'bg-danger' : 'bg-warning text-dark') }}">
                                        {{ ucfirst(str_replace('_', ' ', $p->status_bayar)) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-header fw-bold">🏆 Top Vendor</div>
                <ul class="list-group list-group-flush">
                    @foreach($vendorTop as $i => $v)
                    <li class="list-group-item d-flex justify-content-between">
                        <div>
                            <span class="text-muted me-2">#{{ $i+1 }}</span>
                            <a href="{{ route('admin.vendor.show', $v->id_vendor) }}">{{ $v->nama_kantin }}</a>
                            <br><small class="text-muted">{{ $v->total_pesanan }} pesanan</small>
                        </div>
                        <span class="text-success fw-bold small">
                            Rp {{ number_format($v->total_pendapatan ?? 0, 0, ',', '.') }}
                        </span>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection