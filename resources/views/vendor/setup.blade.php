@extends('layouts.main')
@section('title', 'Setup Kantin')
@section('content')
<div class="container py-4" style="max-width:600px">
    <h2 class="mb-4">Setup Profil Kantin</h2>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('vendor.setup.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label class="form-label">Nama Kantin *</label>
            <input type="text" name="nama_kantin" class="form-control" value="{{ old('nama_kantin') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi') }}</textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Foto Kantin</label>
            <input type="file" name="foto" class="form-control" accept="image/*">
        </div>
        <button type="submit" class="btn btn-primary">Simpan</button>
    </form>
</div>
@endsection