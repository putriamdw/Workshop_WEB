@extends('layouts.main')
@section('title', 'Data Toko')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0"><i class="mdi mdi-store"></i> List Toko</h4>
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
                                <th>Barcode</th>
                                <th>Nama Toko</th>
                                <th class="text-center">Latitude</th>
                                <th class="text-center">Longitude</th>
                                <th class="text-center">Accuracy</th>
                                <th class="text-center">Cetak Barcode</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tokos as $toko)
                            <tr>
                                <td>
                                    <span class="badge bg-secondary font-monospace">
                                        {{ $toko->kode_toko }}
                                    </span>
                                </td>
                                <td>{{ $toko->nama_toko }}</td>
                                <td class="text-center">
                                    {{ $toko->latitude ?? '-' }}
                                </td>
                                <td class="text-center">
                                    {{ $toko->longitude ?? '-' }}
                                </td>
                                <td class="text-center">
                                    {{ $toko->accuracy }} m
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1 flex-wrap">
                                        @if($toko->latitude && $toko->longitude)
                                            <a href="{{ route('toko.qrcode', $toko->id_toko) }}"
                                               class="btn btn-sm btn-success" title="Cetak Barcode">
                                                <i class="mdi mdi-qrcode"></i> Cetak Barcode
                                            </a>
                                        @else
                                            <span class="badge bg-warning text-dark">
                                                Koordinat belum diatur
                                            </span>
                                        @endif
                                        <a href="{{ route('toko.titik-awal', $toko->id_toko) }}"
                                           class="btn btn-sm btn-info" title="Input Titik Awal">
                                            <i class="mdi mdi-crosshairs-gps"></i>
                                        </a>
                                        <a href="{{ route('toko.edit', $toko->id_toko) }}"
                                           class="btn btn-sm btn-warning" title="Edit">
                                            <i class="mdi mdi-pencil"></i>
                                        </a>
                                        <form action="{{ route('toko.destroy', $toko->id_toko) }}"
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('Hapus toko ini?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-danger" title="Hapus">
                                                <i class="mdi mdi-delete"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    Belum ada data toko.
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