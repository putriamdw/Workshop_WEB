@extends('layouts.main')
@section('title', 'Pesanan Lunas')
@section('content')
<div class="container py-4">
    <h2 class="mb-4">Pesanan Lunas — {{ $vendor->nama_kantin }}</h2>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID Pesanan</th>
                        <th>Pembeli</th>
                        <th>Total</th>
                        <th>Metode</th>
                        <th>Dibayar</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pesanan as $p)
                    <tr>
                        <td><code>{{ $p->id_pesanan }}</code></td>
                        <td>{{ $p->nama_pembeli }}</td>
                        <td>{{ $p->total_format }}</td>
                        <td>{{ strtoupper($p->metode_bayar ?? '-') }}</td>
                        <td>{{ $p->paid_at?->format('d M Y H:i') }}</td>
                        <td>
                            <a href="{{ route('vendor.pesanan.show', $p->id_pesanan) }}"
                               class="btn btn-sm btn-outline-primary">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Belum ada pesanan lunas</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $pesanan->links() }}</div>
</div>
@endsection