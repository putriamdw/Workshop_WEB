@extends('layouts.main')

@section('title', 'Edit Buku')

@section('content')
<div class="card">
    <div class="card-body">
        <h3>Edit Buku</h3>

        <form action="{{ route('buku.update', $buku->idbuku) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label>Kategori</label>
                <select name="idkategori" class="form-control">
                    @foreach($kategori as $k)
                        <option value="{{ $k->idkategori }}" 
                            {{ $buku->idkategori == $k->idkategori ? 'selected' : '' }}>
                            {{ $k->nama_kategori }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label>Kode</label>
                <input type="text" name="kode" class="form-control"
                       value="{{ $buku->kode }}">
            </div>

            <div class="mb-3">
                <label>Judul</label>
                <input type="text" name="judul" class="form-control"
                       value="{{ $buku->judul }}">
            </div>

            <div class="mb-3">
                <label>Pengarang</label>
                <input type="text" name="pengarang" class="form-control"
                       value="{{ $buku->pengarang }}">
            </div>

            <button type="submit" class="btn btn-primary">
                Update
            </button>
        </form>
    </div>
</div>
@endsection
