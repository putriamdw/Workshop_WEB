@extends('layouts.main')

@section('title', 'Buku')

@section('style-page')
<style>
    h3 {
        color: purple;
    }
</style>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <h3>Data Buku</h3>

        {{-- Tombol Tambah --}}
        <a href="{{ route('buku.create') }}" class="btn btn-primary mb-3">
            + Tambah Buku
        </a>

        {{-- Notifikasi --}}
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th>Kode</th>
                    <th>Judul</th>
                    <th>Pengarang</th>
                    <th>Kategori</th>
                    <th width="15%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bukus as $index => $buku)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $buku->kode }}</td>
                    <td>{{ $buku->judul }}</td>
                    <td>{{ $buku->pengarang }}</td>
                    <td>{{ $buku->kategori->nama_kategori }}</td>
                    <td>
                        {{-- Edit --}}
                        <a href="{{ route('buku.edit', $buku->idbuku) }}"
                           class="btn btn-warning btn-sm">
                            Edit
                        </a>

                        {{-- Hapus --}}
                        <form action="{{ route('buku.destroy', $buku->idbuku) }}"
                              method="POST"
                              style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Yakin ingin menghapus?')">
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>
</div>
@endsection

@section('js-page')
<script>
    console.log("Halaman Buku aktif");
</script>
@endsection
