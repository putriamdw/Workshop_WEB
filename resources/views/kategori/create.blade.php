@extends('layouts.main')

@section('title', 'Tambah Kategori')

@section('content')
<div class="card">
    <div class="card-body">
        <h3>Tambah Kategori</h3>

        <form action="{{ route('kategori.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label>Nama Kategori</label>
                <input type="text" name="nama_kategori" class="form-control">
            </div>

            <button type="submit" class="btn btn-primary">
                Simpan
            </button>
        </form>

    </div>
</div>
@endsection
