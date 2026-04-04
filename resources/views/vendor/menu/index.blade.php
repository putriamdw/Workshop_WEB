@extends('layouts.main')
@section('title', 'Kelola Menu')
@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Daftar Menu — {{ $vendor->nama_kantin }}</h2>
        <a href="{{ route('vendor.menu.create') }}" class="btn btn-primary">+ Tambah Menu</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row g-3">
        @forelse($menus as $menu)
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0">
                @if($menu->foto)
                    <img src="{{ Storage::url($menu->foto) }}"
                         class="card-img-top" style="height:180px;object-fit:cover">
                @endif
                <div class="card-body">
                    <h5 class="fw-bold">{{ $menu->nama_menu }}</h5>
                    <p class="text-muted small">{{ $menu->deskripsi }}</p>
                    <p class="fw-bold text-success">{{ $menu->harga_format }}</p>
                    <span class="badge {{ $menu->tersedia ? 'bg-success' : 'bg-secondary' }}">
                        {{ $menu->tersedia ? 'Tersedia' : 'Habis' }}
                    </span>
                </div>
                <div class="card-footer d-flex gap-2">
                    <a href="{{ route('vendor.menu.edit', $menu->id_menu) }}"
                       class="btn btn-sm btn-warning flex-fill">Edit</a>
                    <form action="{{ route('vendor.menu.destroy', $menu->id_menu) }}"
                          method="POST" onsubmit="return confirm('Hapus menu ini?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center text-muted py-5">
            Belum ada menu. <a href="{{ route('vendor.menu.create') }}">Tambahkan sekarang</a>.
        </div>
        @endforelse
    </div>
</div>
@endsection