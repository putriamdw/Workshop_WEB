@extends('layouts.main')
@section('title', 'Riwayat Kunjungan')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0"><i class="mdi mdi-history"></i> Riwayat Kunjungan</h4>
            </div>
            <div class="card-body">
                {{-- Filter --}}
                <form method="GET" class="row g-2 mb-3">
                    <div class="col-md-4">
                        <select name="toko" class="form-select">
                            <option value="">Semua Toko</option>
                            @foreach($tokos as $t)
                                <option value="{{ $t->id_toko }}"
                                    {{ request('toko') == $t->id_toko ? 'selected' : '' }}>
                                    {{ $t->nama_toko }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="diterima" {{ request('status') == 'diterima' ? 'selected' : '' }}>Diterima</option>
                            <option value="ditolak"  {{ request('status') == 'ditolak'  ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('toko.riwayat') }}" class="btn btn-outline-secondary w-100">Reset</a>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Waktu</th>
                                <th>Toko</th>
                                <th>Pengunjung</th>
                                <th class="text-center">Jarak</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($kunjungan as $k)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($k->waktu_kunjungan)->format('d M Y H:i') }}</td>
                                <td>{{ $k->toko->nama_toko ?? '-' }}</td>
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
                                <td colspan="5" class="text-center text-muted py-4">
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