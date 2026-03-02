@extends('layouts.main')

@section('title','Tambah Barang')

@section('content')
<div class="card">
<div class="card-body">

<h3>Tambah Barang</h3>

<form action="{{ route('barang.store') }}" method="POST">
@csrf

<div class="mb-3">
<label>Nama Barang</label>
<input type="text" name="nama" class="form-control">
@error('nama')
<small class="text-danger">{{ $message }}</small>
@enderror
</div>

<div class="mb-3">
<label>Harga</label>
<input type="number" name="harga" class="form-control">
@error('harga')
<small class="text-danger">{{ $message }}</small>
@enderror
</div>

<button type="submit" class="btn btn-primary">
Simpan
</button>

</form>

</div>
</div>
@endsection