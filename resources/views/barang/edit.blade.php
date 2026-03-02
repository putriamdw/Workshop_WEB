@extends('layouts.main')

@section('title','Edit Barang')

@section('content')
<div class="card">
<div class="card-body">

<h3>Edit Barang</h3>

<form action="{{ route('barang.update',$barang->id_barang) }}" method="POST">
@csrf
@method('PUT')

<div class="mb-3">
<label>Nama Barang</label>
<input type="text" name="nama" class="form-control"
value="{{ $barang->nama }}">
</div>

<div class="mb-3">
<label>Harga</label>
<input type="number" name="harga" class="form-control"
value="{{ $barang->harga }}">
</div>

<button type="submit" class="btn btn-primary">
Update
</button>

</form>

</div>
</div>
@endsection