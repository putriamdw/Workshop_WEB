@extends('layouts.main')

@section('title','Data Barang')

@section('content')
<div class="card">
    <div class="card-body">
        <h3 class="mb-3">Data Barang</h3>

        <div class="mb-3">
            <a href="{{ route('barang.create') }}" class="btn btn-primary">Tambah Barang</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Form utama: cetak --}}
        <form action="{{ route('barang.cetak') }}" method="POST" target="_blank">
            @csrf
            
            <div class="alert alert-info border-0 shadow-sm">
                <div class="row align-items-end">
                    <div class="col-md-5">
                        <b>Petunjuk Cetak:</b><br>
                        Kertas TnJ No 108 (5 kolom x 8 baris).<br>
                        Tentukan posisi awal (X,Y) lalu centang barang di tabel.
                    </div>
                    <div class="col-md-2">
                        <label class="small fw-bold">Kolom (X)</label>
                        <input type="number" name="x" class="form-control" min="1" max="5" value="1" required>
                    </div>
                    <div class="col-md-2">
                        <label class="small fw-bold">Baris (Y)</label>
                        <input type="number" name="y" class="form-control" min="1" max="8" value="1" required>
                    </div>
                    <div class="col-md-3">
                        {{-- Button cetak --}}
                        <button type="submit" class="btn btn-success w-100"> Cetak Label
                        </button>
                    </div>
                </div>
            </div>

            <div class="table-responsive mt-4">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th width="50" class="text-center">Pilih</th>
                            <th>ID</th>
                            <th>Nama Barang</th>
                            <th>Harga</th>
                            <th width="150">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($barang as $b)
                        <tr>
                            <td class="text-center">
                                <input type="checkbox" name="selected[]" value="{{ $b->id_barang }}">
                            </td>
                            <td>{{ $b->id_barang }}</td>
                            <td>{{ $b->nama }}</td>
                            <td>Rp {{ number_format($b->harga,0,',','.') }}</td>
                            <td>
                                <a href="{{ route('barang.edit',$b->id_barang) }}" class="btn btn-warning btn-sm">Edit</a>
                                
                                {{-- Tombol hapus --}}
                                <button type="submit" form="delete-{{ $b->id_barang }}" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus?')">
                                    Hapus
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </form>

        {{-- Form hapus terpisah (di luar form utama) --}}
        @foreach($barang as $b)
        <form id="delete-{{ $b->id_barang }}" action="{{ route('barang.destroy',$b->id_barang) }}" method="POST" style="display:none;">
            @csrf
            @method('DELETE')
        </form>
        @endforeach

    </div>
</div>
@endsection