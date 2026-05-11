@extends('layouts.main')
@section('title', 'Riwayat Kunjungan Kantin Saya')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="card-title mb-0">
                        <i class="mdi mdi-history"></i> Riwayat Kunjungan
                    </h4>
                    <small class="text-muted">{{ $toko->nama_toko }}</small>
                </div>
                <a href="{{ route('vendor.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                    ← Kembali
                </a>
            </div>
            <div class="card-body">

                {{-- Filter status --}}
                <form method="GET" class="row g-2 mb-3">
                    <div class="col-md-3">
                        <select name="status" class="form-select" onchange="this.form.submit()">
                            <option value="">Semua Status</option>
                            <option value="diterima" {{ request('status') == 'diterima' ? 'selected' : '' }}>
                                ✅ Diterima
                            </option>
                            <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>
                                ❌ Ditolak
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('vendor.riwayat') }}" class="btn btn-outline-secondary w-100">
                            Reset
                        </a>
                    </div>
                </form>

                {{-- Ringkasan --}}
                @php
                    $totalDiterima = $kunjungan->where('status', 'diterima')->count();
                    $totalDitolak  = $kunjungan->where('status', 'ditolak')->count();
                @endphp
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <div class="card border-0 bg-success bg-opacity-10 text-center p-3">
                            <div class="fs-4 fw-bold text-success">{{ $kunjungan->total() }}</div>
                            <small class="text-muted">Total Kunjungan</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 bg-success bg-opacity-10 text-center p-3">
                            <div class="fs-4 fw-bold text-success">
                                {{ $kunjungan->getCollection()->where('status', 'diterima')->count() }}
                            </div>
                            <small class="text-muted">Diterima (halaman ini)</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 bg-danger bg-opacity-10 text-center p-3">
                            <div class="fs-4 fw-bold text-danger">
                                {{ $kunjungan->getCollection()->where('status', 'ditolak')->count() }}
                            </div>
                            <small class="text-muted">Ditolak (halaman ini)</small>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Waktu</th>
                                <th>Pengunjung</th>
                                <th class="text-center">Jarak</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($kunjungan as $k)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($k->waktu_kunjungan)->format('d M Y H:i') }}</td>
                                <td>{{ $k->nama_pengunjung }}</td>
                                <td class="text-center">{{ number_format($k->jarak_meter, 1) }} m</td>
                                <td class="text-center">
                                    @if($k->status === 'diterima')
                                        <span class="badge bg-success">✅ Diterima</span>
                                    @else
                                        <span class="badge bg-danger">❌ Ditolak</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    Belum ada riwayat kunjungan
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $kunjungan->links('pagination::bootstrap-5') }}</div>
            </div>
        </div>
    </div>
</div>
@endsection