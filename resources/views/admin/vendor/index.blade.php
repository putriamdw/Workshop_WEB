@extends('layouts.main')
@section('title', 'Data Vendor')
@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between mb-4">
        <h2>Data Semua Vendor</h2>
        <form class="d-flex gap-2">
            <input type="text" name="search" class="form-control form-control-sm"
                   placeholder="Cari nama kantin..." value="{{ request('search') }}">
            <button class="btn btn-sm btn-primary">Cari</button>
        </form>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nama Kantin</th>
                        <th>Pemilik</th>
                        <th class="text-center">Jumlah Menu</th>
                        <th class="text-center">Pesanan Lunas</th>
                        <th class="text-end">Total Pendapatan</th>
                        <th class="text-center">Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vendors as $v)
                    <tr>
                        <td class="fw-bold">{{ $v->nama_kantin }}</td>
                        <td>{{ $v->user->name }}</td>
                        <td class="text-center">{{ $v->menus_count }}</td>
                        <td class="text-center">{{ $v->pesanan_lunas }}</td>
                        <td class="text-end">Rp {{ number_format($v->total_pendapatan ?? 0, 0, ',', '.') }}</td>
                        <td class="text-center">
                            <span class="badge {{ $v->aktif ? 'bg-success' : 'bg-secondary' }}">
                                {{ $v->aktif ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('admin.vendor.show', $v->id_vendor) }}"
                               class="btn btn-sm btn-outline-primary">Detail</a>
                            <form action="{{ route('admin.vendor.toggle', $v->id_vendor) }}"
                                  method="POST" class="d-inline">
                                @csrf @method('PATCH')
                                <button class="btn btn-sm {{ $v->aktif ? 'btn-outline-danger' : 'btn-outline-success' }}">
                                    {{ $v->aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">Belum ada vendor</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $vendors->links() }}</div>
</div>
@endsection