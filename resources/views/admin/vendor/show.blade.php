@extends('layouts.main')
@section('title', 'Detail Vendor')
@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>{{ $vendor->nama_kantin }}</h2>
        <a href="{{ route('admin.vendor.index') }}" class="btn btn-secondary">← Kembali</a>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header fw-bold">Info Vendor</div>
                <div class="card-body">
                    <p><strong>Pemilik:</strong> {{ $vendor->user->name }}</p>
                    <p><strong>Email:</strong> {{ $vendor->user->email }}</p>
                    <p><strong>Deskripsi:</strong> {{ $vendor->deskripsi ?? '-' }}</p>
                    <p><strong>Status:</strong>
                        <span class="badge {{ $vendor->aktif ? 'bg-success' : 'bg-secondary' }}">
                            {{ $vendor->aktif ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header fw-bold">Daftar Menu</div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr><th>Nama Menu</th><th class="text-end">Harga</th><th class="text-center">Status</th></tr>
                        </thead>
                        <tbody>
                            @forelse($vendor->menus as $menu)
                            <tr>
                                <td>{{ $menu->nama_menu }}</td>
                                <td class="text-end">{{ $menu->harga_format }}</td>
                                <td class="text-center">
                                    <span class="badge {{ $menu->tersedia ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $menu->tersedia ? 'Tersedia' : 'Habis' }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center text-muted">Belum ada menu</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header fw-bold">Semua Pesanan</div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr><th>ID Pesanan</th><th>Pembeli</th><th>Total</th><th>Status</th><th>Dibayar</th></tr>
                </thead>
                <tbody>
                    @forelse($pesanan as $p)
                    <tr>
                        <td><code>{{ $p->id_pesanan }}</code></td>
                        <td>{{ $p->nama_pembeli }}</td>
                        <td>{{ $p->total_format }}</td>
                        <td>
                            <span class="badge {{ $p->status_bayar === 'lunas' ? 'bg-success' : ($p->status_bayar === 'expired' ? 'bg-danger' : 'bg-warning text-dark') }}">
                                {{ ucfirst(str_replace('_', ' ', $p->status_bayar)) }}
                            </span>
                        </td>
                        <td>{{ $p->paid_at?->format('d M Y H:i') ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted py-3">Belum ada pesanan</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $pesanan->links() }}</div>
</div>
@endsection