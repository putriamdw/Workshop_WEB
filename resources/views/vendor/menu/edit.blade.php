@extends('layouts.main')
@section('title', 'Edit Menu')
@section('content')
<div class="container py-4" style="max-width:600px">
    <h2 class="mb-4">Edit Menu</h2>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('vendor.menu.update', $menu->id_menu) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div class="mb-3">
            <label class="form-label">Nama Menu *</label>
            <input type="text" name="nama_menu" class="form-control" value="{{ old('nama_menu', $menu->nama_menu) }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi', $menu->deskripsi) }}</textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Harga (Rp) *</label>
            <input type="number" name="harga" class="form-control" value="{{ old('harga', $menu->harga) }}" min="0" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Foto Menu</label>
            @if($menu->foto)
                <div class="mb-2">
                    <img src="{{ Storage::url($menu->foto) }}" style="height:100px;object-fit:cover" class="rounded">
                </div>
            @endif
            <input type="file" name="foto" class="form-control" accept="image/*">
        </div>
        <div class="mb-3 form-check">
            <input type="checkbox" name="tersedia" class="form-check-input" id="tersedia"
                   {{ old('tersedia', $menu->tersedia) ? 'checked' : '' }}>
            <label class="form-check-label" for="tersedia">Menu tersedia</label>
        </div>
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('vendor.menu.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection