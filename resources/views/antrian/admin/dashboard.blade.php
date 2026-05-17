@extends('layouts.main')

@section('title', 'Dashboard Antrian')

@section('styles')
<style>
    .stat-card {
        border: none;
        border-radius: 14px;
        transition: transform .15s;
    }
    .stat-card:hover { transform: translateY(-2px); }
    .call-banner {
        border-radius: 14px;
        border-left: 5px solid #ffc107;
        background: #fffbf0;
    }
    .table-antrian th {
        font-size: .78rem;
        text-transform: uppercase;
        letter-spacing: .5px;
        color: #6c757d;
        font-weight: 600;
        border-top: none;
    }
    .table-antrian td { vertical-align: middle; }
    .nomor-col {
        font-weight: 700;
        font-size: 1.05rem;
        color: #1a3c6b;
    }
    tr.row-terlambat {
        background: #fff5f5;
        cursor: pointer;
    }
    tr.row-terlambat:hover { background: #ffe0e0; }
    .hint-terlambat {
        font-size: .75rem;
        color: #dc3545;
    }
    .btn-panggil-next {
        background: linear-gradient(135deg, #1a3c6b, #0d6efd);
        color: #fff;
        border: none;
        border-radius: 10px;
        padding: 10px 24px;
        font-weight: 600;
        font-size: 1rem;
        transition: opacity .2s;
    }
    .btn-panggil-next:hover    { opacity: .85; color: #fff; }
    .btn-panggil-next:disabled { opacity: .5; cursor: not-allowed; }
</style>
@endsection

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- Header --}}
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h3 class="page-title fw-bold mb-0">
        <span class="page-title-icon bg-gradient-primary text-white me-2">
            <i class="mdi mdi-clipboard-list"></i>
        </span>
        Dashboard Antrian
    </h3>
    <div class="d-flex gap-2 flex-wrap">
        <button class="btn-panggil-next" id="btnPanggilBerikutnya" onclick="panggilBerikutnya()">
            <i class="mdi mdi-bell-ring"></i> Panggil Berikutnya
        </button>
        <a href="{{ route('antrian.papan') }}" target="_blank"
           class="btn btn-outline-primary btn-sm">
            <i class="mdi mdi-monitor"></i> Buka Papan Antrian
        </a>
        <a href="{{ route('antrian.guest') }}" target="_blank"
           class="btn btn-outline-success btn-sm">
            <i class="mdi mdi-account-plus"></i> Form Daftar Guest
        </a>
    </div>
</div>

{{-- Statistik --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card stat-card shadow-sm" style="background:#e3f2fd;">
            <div class="card-body text-center py-3">
                <i class="mdi mdi-clock-outline text-info" style="font-size:2rem;"></i>
                <h2 class="fw-bold mb-0 text-info" id="cntMenunggu">{{ $counts['menunggu'] }}</h2>
                <small class="text-muted">Menunggu</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card stat-card shadow-sm" style="background:#fff3cd;">
            <div class="card-body text-center py-3">
                <i class="mdi mdi-bell-ring text-warning" style="font-size:2rem;"></i>
                <h2 class="fw-bold mb-0 text-warning" id="cntDipanggil">{{ $counts['dipanggil'] }}</h2>
                <small class="text-muted">Dipanggil</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card stat-card shadow-sm" style="background:#fce4ec;">
            <div class="card-body text-center py-3">
                <i class="mdi mdi-clock-alert text-danger" style="font-size:2rem;"></i>
                <h2 class="fw-bold mb-0 text-danger" id="cntTerlambat">{{ $counts['terlambat'] }}</h2>
                <small class="text-muted">Terlambat</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card stat-card shadow-sm" style="background:#e8f5e9;">
            <div class="card-body text-center py-3">
                <i class="mdi mdi-check-circle text-success" style="font-size:2rem;"></i>
                <h2 class="fw-bold mb-0 text-success" id="cntSelesai">{{ $counts['selesai'] }}</h2>
                <small class="text-muted">Selesai</small>
            </div>
        </div>
    </div>
</div>

{{-- Banner antrian aktif --}}
<div class="card call-banner shadow-sm mb-4 px-4 py-3">
    <div class="d-flex align-items-center gap-3">
        <i class="mdi mdi-bell-ring text-warning" style="font-size:2.2rem;"></i>
        <div>
            <small class="text-muted text-uppercase fw-semibold d-block" style="font-size:.7rem;">
                Sedang Dipanggil
            </small>
            <div id="currentCallText" class="fw-bold fs-5">—</div>
        </div>
    </div>
</div>

{{-- Tabel --}}
<div class="card shadow-sm" style="border-radius:14px;">
    <div class="card-header bg-white border-0 pt-4 pb-2 px-4 d-flex align-items-center gap-2 flex-wrap">
        <h5 class="mb-0 fw-bold">Daftar Antrian Hari Ini</h5>
        <span id="badgeTotal" class="badge bg-primary rounded-pill">{{ $list->count() }}</span>
        <span id="hintTerlambat" class="ms-auto hint-terlambat" style="display:none;">
            💡 Double-click baris merah untuk memanggil ulang antrian terlambat
        </span>
    </div>
    <div class="card-body px-2 pt-0">
        <div class="table-responsive">
            <table class="table table-hover table-antrian align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">No.</th>
                        <th>Nama Pasien</th>
                        <th>Poli / Layanan</th>
                        <th>Jam Daftar</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tbodyAntrian">
                    @forelse ($list as $item)
                    <tr class="{{ $item->status === 'terlambat' ? 'row-terlambat' : '' }}"
                        @if($item->status === 'terlambat')
                            ondblclick="panggilById({{ $item->id }}, '{{ $item->nomor }}', '{{ addslashes($item->nama) }}')"
                            title="Double-click untuk panggil ulang"
                        @endif>
                        <td class="ps-4 nomor-col">{{ $item->nomor }}</td>
                        <td>{{ $item->nama }}</td>
                        <td><small class="text-muted">Poli {{ $item->poli }}</small></td>
                        <td><small>{{ $item->jam_daftar->format('H:i') }}</small></td>
                        <td>{!! statusBadge($item->status) !!}</td>
                        <td class="text-center">{!! aksiButtons($item->id, $item->status) !!}</td>
                    </tr>
                    @empty
                    @endforelse
                </tbody>
            </table>
        </div>
        <p id="emptyMsg" class="text-center text-muted py-4 {{ $list->count() ? 'd-none' : '' }}">
            Belum ada antrian hari ini.
        </p>
    </div>
</div>

{{-- Modal Panggil dengan Ruangan & Loket --}}
<div class="modal fade" id="modalPanggil" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:14px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">
                    <i class="mdi mdi-bell-ring text-warning"></i>
                    Panggil Antrian
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="modalAntrianId" value="">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nomor Ruangan</label>
                    <input type="text" id="inputRuangan" class="form-control"
                           placeholder="cth: Ruang 1, Ruang Dokter A">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Loket</label>
                    <select id="inputLoket" class="form-select">
                        <option value="">— Pilih Loket —</option>
                        <option value="Loket 1">Loket 1</option>
                        <option value="Loket 2">Loket 2</option>
                        <option value="Loket 3">Loket 3</option>
                        <option value="Loket 4">Loket 4</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-warning fw-bold" onclick="submitPanggil()">
                    <i class="mdi mdi-bell-ring"></i> Panggil Sekarang
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@php
function statusBadge(string $status): string {
    return match($status) {
        'menunggu'  => '<span class="badge bg-info text-dark">Menunggu</span>',
        'dipanggil' => '<span class="badge bg-warning text-dark">Dipanggil</span>',
        'selesai'   => '<span class="badge bg-success">Selesai</span>',
        'terlambat' => '<span class="badge bg-danger">Terlambat</span>',
        default     => '<span class="badge bg-secondary">' . $status . '</span>',
    };
}
function aksiButtons(int $id, string $status): string {
    $html = '';
    if ($status === 'menunggu') {
        $html .= "<button class='btn btn-warning btn-sm me-1'
                          title='Panggil' onclick='bukaModalPanggil({$id})'>
                      <i class='mdi mdi-bell'></i></button>";
    }
    if (in_array($status, ['menunggu','dipanggil','terlambat'])) {
        $html .= "<button class='btn btn-success btn-sm me-1'
                          title='Selesai' onclick='doAksi({$id},\"selesai\")'>
                      <i class='mdi mdi-check'></i></button>";
    }
    if (in_array($status, ['menunggu','dipanggil'])) {
        $html .= "<button class='btn btn-danger btn-sm'
                          title='Tandai Terlambat' onclick='doAksi({$id},\"terlambat\")'>
                      <i class='mdi mdi-clock-alert'></i></button>";
    }
    return $html;
}
@endphp

@section('script-page')
<script>
const csrf = document.querySelector('meta[name="csrf-token"]').content;

// ── SSE ──────────────────────────────────────────────────────────────────
const source = new EventSource('/sse/antrian');

source.addEventListener('queue-update', function(event) {
    const data = JSON.parse(event.data);
    renderStats(data.counts   || {});
    renderBanner(data.dipanggil);
    renderTable(data.list     || []);
    updateBtnState(data.counts);
});

source.onerror = function(error) {
    console.error('SSE error:', error);
};

// ── Panggil berikutnya — buka modal ──────────────────────────────────────
function panggilBerikutnya() {
    // Reset modal & set id = 0 sebagai penanda "berikutnya"
    document.getElementById('modalAntrianId').value = '0';
    document.getElementById('inputRuangan').value   = '';
    document.getElementById('inputLoket').value     = '';
    if (!modalPanggilInstance) {
        modalPanggilInstance = new bootstrap.Modal(document.getElementById('modalPanggil'));
    }
    modalPanggilInstance.show();
}

// ── Submit modal panggil ──────────────────────────────────────────────────
async function submitPanggil() {
    const id      = document.getElementById('modalAntrianId').value;
    const ruangan = document.getElementById('inputRuangan').value;
    const loket   = document.getElementById('inputLoket').value;

    // id = '0' artinya panggil berikutnya, selain itu panggil by ID
    const url = id === '0'
        ? '/admin/panggil-berikutnya'
        : `/admin/antrian/${id}/panggil-detail`;

    const btn = document.getElementById('btnPanggilBerikutnya');
    if (id === '0') btn.disabled = true;

    try {
        const res  = await fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrf,
                'Accept':       'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ ruangan, loket }),
        });
        const json = await res.json();
        if (!json.success) alert(json.message || 'Tidak ada antrian menunggu.');
        else modalPanggilInstance.hide();
    } catch (err) {
        alert('Gagal: ' + err.message);
    } finally {
        if (id === '0') setTimeout(() => btn.disabled = false, 1500);
    }
}

// ── Panggil ulang terlambat (double-click baris merah) ───────────────────
async function panggilById(id, nomor, nama) {
    if (!confirm(`Panggil ulang antrian No.${nomor} — ${nama}?`)) return;
    await doAksi(id, 'panggil');
}

// ── Render statistik ──────────────────────────────────────────────────────
function renderStats(c) {
    document.getElementById('cntMenunggu').textContent  = c.menunggu  ?? 0;
    document.getElementById('cntDipanggil').textContent = c.dipanggil ?? 0;
    document.getElementById('cntTerlambat').textContent = c.terlambat ?? 0;
    document.getElementById('cntSelesai').textContent   = c.selesai   ?? 0;
}

function updateBtnState(counts) {
    const btn = document.getElementById('btnPanggilBerikutnya');
    if (!btn.disabled)
        btn.style.opacity = (counts && counts.menunggu > 0) ? '1' : '0.5';
}

// ── Render banner ─────────────────────────────────────────────────────────
function renderBanner(item) {
    const el = document.getElementById('currentCallText');
    if (!item) { el.innerHTML = '—'; return; }

    el.innerHTML = `<span class="text-warning fw-bold">${esc(item.nomor)}</span>
        &nbsp;—&nbsp;${esc(item.nama)}
        &nbsp;<small class="text-muted fw-normal">(Poli ${esc(item.poli)}${item.ruangan ? ' — ' + esc(item.ruangan) : ''}${item.loket ? ' | ' + esc(item.loket) : ''})</small>`;
}

// ── Render tabel ──────────────────────────────────────────────────────────
function renderTable(list) {
    const tbody = document.getElementById('tbodyAntrian');
    const empty = document.getElementById('emptyMsg');
    const hint  = document.getElementById('hintTerlambat');

    document.getElementById('badgeTotal').textContent = list.length;
    hint.style.display = list.some(a => a.status === 'terlambat') ? 'inline' : 'none';

    if (!list.length) {
        tbody.innerHTML = '';
        empty.classList.remove('d-none');
        return;
    }
    empty.classList.add('d-none');

    tbody.innerHTML = list.map(item => {
        const jam = item.jam_daftar
            ? new Date(item.jam_daftar)
                .toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', timeZone: 'Asia/Jakarta' })
            : '—';
        const isTelat = item.status === 'terlambat';
        const dbl     = isTelat
            ? `ondblclick="panggilById(${item.id},'${esc(item.nomor)}','${esc(item.nama)}')"
               title="Double-click untuk panggil ulang"` : '';
        return `
        <tr class="${isTelat ? 'row-terlambat' : ''}" ${dbl}>
            <td class="ps-4 nomor-col">${esc(item.nomor)}</td>
            <td>${esc(item.nama)}</td>
            <td><small class="text-muted">Poli ${esc(item.poli)}</small></td>
            <td><small>${jam}</small></td>
            <td>${statusBadge(item.status)}</td>
            <td class="text-center">${aksiButtons(item.id, item.status)}</td>
        </tr>`;
    }).join('');
}

function statusBadge(status) {
    const map = {
        menunggu:  '<span class="badge bg-info text-dark">Menunggu</span>',
        dipanggil: '<span class="badge bg-warning text-dark">Dipanggil</span>',
        selesai:   '<span class="badge bg-success">Selesai</span>',
        terlambat: '<span class="badge bg-danger">Terlambat</span>',
    };
    return map[status] ?? `<span class="badge bg-secondary">${status}</span>`;
}

function aksiButtons(id, status) {
    let h = '';
    if (status === 'menunggu')
        h += `<button class="btn btn-warning btn-sm me-1" title="Panggil"
                       onclick="bukaModalPanggil(${id})">
                  <i class="mdi mdi-bell"></i></button>`;
    if (['menunggu','dipanggil','terlambat'].includes(status))
        h += `<button class="btn btn-success btn-sm me-1" title="Selesai"
                       onclick="doAksi(${id},'selesai')">
                  <i class="mdi mdi-check"></i></button>`;
    if (['menunggu','dipanggil'].includes(status))
        h += `<button class="btn btn-danger btn-sm" title="Tandai Terlambat"
                       onclick="doAksi(${id},'terlambat')">
                  <i class="mdi mdi-clock-alert"></i></button>`;
    return h;
}

// ── Kirim aksi ────────────────────────────────────────────────────────────
async function doAksi(id, aksi) {
    const konfirmasi = {
        selesai:   'Tandai antrian ini sebagai selesai?',
        terlambat: 'Tandai antrian ini sebagai terlambat?',
    };
    if (konfirmasi[aksi] && !confirm(konfirmasi[aksi])) return;
    try {
        const res = await fetch(`/admin/antrian/${id}/${aksi}`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
        });
        if (!res.ok) throw new Error(`Server error: ${res.status}`);
    } catch (err) {
        alert('Gagal: ' + err.message);
    }
}

// ── Modal panggil ─────────────────────────────────────────────────────────
let modalPanggilInstance = null;

function bukaModalPanggil(id) {
    document.getElementById('modalAntrianId').value = id;
    document.getElementById('inputRuangan').value   = '';
    document.getElementById('inputLoket').value     = '';
    if (!modalPanggilInstance) {
        modalPanggilInstance = new bootstrap.Modal(document.getElementById('modalPanggil'));
    }
    modalPanggilInstance.show();
}

function esc(s) {
    return String(s)
        .replace(/&/g,'&amp;').replace(/</g,'&lt;')
        .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
</script>
@endsection