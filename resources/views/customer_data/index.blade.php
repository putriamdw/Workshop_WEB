@extends('layouts.main')
@section('title', 'Data Customer')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title">Data Customer</h4>
                    <div>
                        <a href="{{ route('customer-data.tambah1') }}" class="btn btn-primary btn-sm me-2">
                            <i class="mdi mdi-camera"></i> Tambah Customer 1 (Blob)
                        </a>
                        <a href="{{ route('customer-data.tambah2') }}" class="btn btn-success btn-sm">
                            <i class="mdi mdi-camera"></i> Tambah Customer 2 (File)
                        </a>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                {{-- Search manual --}}
                <div class="mb-3">
                    <input type="text" id="searchInput" class="form-control"
                           placeholder="Cari nama atau alamat...">
                </div>

                <div class="table-responsive">
                    <table class="table table-hover" id="tabelCustomer">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Foto</th>
                                <th>Nama</th>
                                <th>Alamat</th>
                                <th>Tipe Foto</th>
                                <th>Dibuat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customers as $i => $c)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>
                                    @if($c->foto_blob)
                                        <img src="{{ route('customer-data.foto-blob', $c->id_customer) }}"
                                             width="60" height="60"
                                             style="object-fit:cover; border-radius:50%;">
                                    @elseif($c->foto_path)
                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($c->foto_path) }}"
                                             width="60" height="60"
                                             style="object-fit:cover; border-radius:50%;">
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $c->nama }}</td>
                                <td>
                                    {{ $c->alamat ?? '-' }}
                                    @if($c->kecamatan), {{ $c->kecamatan }}@endif
                                    @if($c->kota), {{ $c->kota }}@endif
                                    @if($c->kodepos) {{ $c->kodepos }}@endif
                                </td>
                                <td>
                                    @if($c->foto_blob)
                                        <span class="badge bg-primary">Blob</span>
                                    @elseif($c->foto_path)
                                        <span class="badge bg-success">File</span>
                                    @else
                                        <span class="badge bg-secondary">-</span>
                                    @endif
                                </td>
                                <td>{{ $c->created_at->format('d M Y H:i') }}</td>
                            </tr>
                            @empty
                            <tr id="emptyRow">
                                <td colspan="6" class="text-center text-muted">Belum ada data customer</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Info jumlah --}}
                <small class="text-muted">
                    Total: <span id="totalRows">{{ $customers->count() }}</span> customer
                </small>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script-page')
<script>
// Search manual tanpa DataTables
document.getElementById('searchInput').addEventListener('keyup', function () {
    const keyword = this.value.toLowerCase();
    const rows    = document.querySelectorAll('#tabelCustomer tbody tr');

    rows.forEach(row => {
        const nama   = row.cells[2]?.textContent.toLowerCase() ?? '';
        const alamat = row.cells[3]?.textContent.toLowerCase() ?? '';
        row.style.display = (nama.includes(keyword) || alamat.includes(keyword))
            ? '' : 'none';
    });
});
</script>
@endsection