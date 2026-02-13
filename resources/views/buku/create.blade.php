@extends('layouts.main')

@section('title', 'Tambah Buku')

@section('content')
<h4>Tambah Buku</h4>

@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

<form action="{{ route('buku.store') }}" method="POST">
    @csrf

    <div class="mb-3">
        <label>Kategori</label>
        <select name="idkategori" class="form-control" required>
            <option value="">-- Pilih Kategori --</option>
            @foreach($kategori as $k)
                <option value="{{ $k->idkategori }}">{{ $k->nama_kategori }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label>Kode</label>
        <input type="text" name="kode" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Judul</label>
        <input type="text" name="judul" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Pengarang</label>
        <input type="text" name="pengarang" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-primary">Simpan</button>
</form>
@endsection
