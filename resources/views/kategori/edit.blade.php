@extends('layouts.main')

@section('title', 'Edit Kategori')

@section('content')
<div class="card">
    <div class="card-body">
        <h3>Edit Kategori</h3>

        <form action="{{ route('kategori.update', $kategori->idkategori) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label>Nama Kategori</label>
                <input type="text"
                       name="nama_kategori"
                       class="form-control"
                       value="{{ $kategori->nama_kategori }}">
            </div>

            <button type="submit" class="btn btn-primary">
                Update
            </button>
        </form>

    </div>
</div>
@endsection
