@extends('layouts.main')
@section('title', 'Edit Toko')
@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header">
                <h4 class="card-title mb-0"><i class="mdi mdi-store-edit"></i> Edit Toko</h4>
            </div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">{{ $errors->first() }}</div>
                @endif
                <form action="{{ route('toko.update', $toko->id_toko) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Kode Toko</label>
                        <input type="text" class="form-control" value="{{ $toko->kode_toko }}" disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Toko <span class="text-danger">*</span></label>
                        <input type="text" name="nama_toko" class="form-control"
                               value="{{ old('nama_toko', $toko->nama_toko) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea name="alamat" class="form-control" rows="3">{{ old('alamat', $toko->alamat) }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Radius Toleransi (meter) <span class="text-danger">*</span></label>
                        <input type="number" name="accuracy" class="form-control"
                               value="{{ old('accuracy', $toko->accuracy) }}" min="10" max="1000" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Assign ke Vendor</label>
                        <select name="id_vendor" class="form-select">
                            <option value="">-- Tidak di-assign --</option>
                            @foreach($vendors as $v)
                                <option value="{{ $v->id_vendor }}"
                                    {{ old('id_vendor', $toko->id_vendor) == $v->id_vendor ? 'selected' : '' }}>
                                    {{ $v->nama_kantin }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">
                            Pilih vendor pemilik toko ini agar vendor bisa input koordinat sendiri.
                        </small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-warning">Update</button>
                        <a href="{{ route('toko.index') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection