@extends('layouts.main')
@section('title', 'Semua Pesanan')
@section('content')
<div class="container py-4">
    <h2 class="mb-4">Semua Pesanan</h2>

    <form class="row g-2 mb-4">
        <div class="col-md-3">
            <select name="status" class="form-select form-select-sm">
                <option value="">Semua Status</option>
                <option value="belum_bayar" {{ request('status') === 'belum_bayar' ? 'selected' : '' }}>Belum Bayar</option>
                <option value="lunas" {{ request('status') === 'lunas' ? 'selected' : '' }}>Lunas</option>
                <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
            </select>
        </div>
        <div class="col-md-3">
            <select name="vendor" class="form-select form-select-sm">
                <option value="">Semua Vendor</option>
                @foreach($vendors as $v)
                <option value="{{ $v->id_vendor }}" {{ request('vendor') == $v->id_vendor ? 'selected' : '' }}>
                    {{ $v->nama_kantin }}
                </option>
                @endforeach
            </select>
        </div>
        <div class="col-auto">
            <button class="btn btn-sm btn-primary">Filter</button>
            <a href="{{ route('admin.pesanan.index') }}" class="btn btn-sm btn-secondary">Reset</a>
        </div>
    </form>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID Pesanan</th>
                        <th>Pembeli</th>
                        <th>Kantin</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Dibayar</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pesanan as $p)
                    <tr>
                        <td><code>{{ $p->id_pesanan }}</code></td>
                        <td>{{ $p->nama_pembeli }}</td>
                        <td>{{ $p->vendor->nama_kantin }}</td>
                        <td>{{ $p->total_format }}</td>
                        <td>
                            <span class="badge {{ $p->status_bayar === 'lunas' ? 'bg-success' : ($p->status_bayar === 'expired' ? 'bg-danger' : 'bg-warning text-dark') }}">
                                {{ ucfirst(str_replace('_', ' ', $p->status_bayar)) }}
                            </span>
                        </td>
                        <td>{{ $p->paid_at?->format('d M Y H:i') ?? '-' }}</td>
                        <td>
                            <a href="{{ route('admin.pesanan.show', $p->id_pesanan) }}"
                               class="btn btn-sm btn-outline-primary">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">Belum ada pesanan</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $pesanan->links() }}</div>
</div>
@endsection