@extends('layouts.main')
@section('title', 'Pilih Kantin')
@section('content')
<div class="container py-5">
    <h2 class="text-center mb-2">🍽️ Pesan Makanan Online</h2>
    <p class="text-center text-muted mb-5">Pilih kantin lalu pesan menu favoritmu</p>
    <div class="row g-4">
        @foreach($vendors as $vendor)
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0">
                @if($vendor->foto)
                    <img src="{{ Storage::url($vendor->foto) }}"
                         class="card-img-top" style="height:200px;object-fit:cover">
                @else
                    <div class="bg-light d-flex align-items-center justify-content-center"
                         style="height:200px;font-size:3rem">🏪</div>
                @endif
                <div class="card-body">
                    <h5 class="fw-bold">{{ $vendor->nama_kantin }}</h5>
                    <p class="text-muted small">{{ $vendor->deskripsi }}</p>
                    <small class="text-muted">
                        {{ $vendor->menus->where('tersedia', true)->count() }} menu tersedia
                    </small>
                </div>
                <div class="card-footer border-0 bg-transparent pb-3">
                    <a href="{{ route('customer.pilih-vendor', $vendor->id_vendor) }}"
                       class="btn btn-primary w-100">Lihat Menu & Pesan</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection