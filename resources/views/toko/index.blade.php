@extends('layouts.main')
@section('title', 'Data Toko')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0"><i class="mdi mdi-store"></i> Data Toko</h4>
                <a href="{{ route('toko.create') }}" class="btn btn-primary btn-sm">
                    <i class="mdi mdi-plus"></i> Tambah Toko
                </a>
            </div>
            <div class="card-body p-0">
                @if(session('success'))
                    <div class="alert alert-success m-3">{{ session('success') }}</div>
                @endif
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Kode</th>
                                <th>Nama Toko</th>
                                <th>Alamat</th>
                                <th class="text-center">Koordinat</th>
                                <th class="text-center">Radius</th>
                                <th class="text-center">Kunjungan</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tokos as $t)
                            <tr>
                                <td><code>{{ $t->kode_toko }}</code></td>
                                <td>{{ $t->nama_toko }}</td>
                                <td>{{ $t->alamat ?? '-' }}</td>
                                <td class="text-center">
                                    @if($t->latitude && $t->longitude)
                                        <span class="badge bg-success">Sudah diatur</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Belum diatur</span>
                                    @endif
                                </td>
                                <td class="text-center">{{ $t->accuracy }} m</td>
                                <td class="text-center">{{ $t->kunjungan_count }}</td>
                                <td class="text-center">
                                    <div class="d-flex gap-1 justify-content-center">
                                        <a href="{{ route('toko.titik-awal', $t->id_toko) }}"
                                           class="btn btn-info btn-sm" title="Input Titik Awal">
                                            <i class="mdi mdi-map-marker"></i>
                                        </a>
                                        <a href="{{ route('toko.qrcode', $t->id_toko) }}"
                                           class="btn btn-success btn-sm" title="QR Code">
                                            <i class="mdi mdi-qrcode"></i>
                                        </a>
                                        <a href="{{ route('toko.edit', $t->id_toko) }}"
                                           class="btn btn-warning btn-sm" title="Edit">
                                            <i class="mdi mdi-pencil"></i>
                                        </a>
                                        <form action="{{ route('toko.destroy', $t->id_toko) }}"
                                              method="POST"
                                              onsubmit="return confirm('Hapus toko ini?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-danger btn-sm" title="Hapus">
                                                <i class="mdi mdi-delete"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    Belum ada data toko
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection