@extends('layouts.main')

@section('content')
<div class="row justify-content-center">
<div class="col-xl-10">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-bold mb-0">🛒 Point of Sales — jQuery AJAX</h4>
            <small class="text-muted">Input kode barang → tekan Enter → isi jumlah → Tambahkan</small>
        </div>
        <a href="{{ route('pos.axios') }}" class="btn btn-sm btn-outline-success">
            Lihat versi Axios →
        </a>
    </div>

    <!-- Card form input -->
    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Kode Barang</label>
                    <input type="text" id="inp-kode" class="form-control text-uppercase"
                           placeholder="BRG0001" autocomplete="off"
                           onkeydown="if(event.key==='Enter'){event.preventDefault();cariBarang();}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Nama Barang</label>
                    <input type="text" id="inp-nama" class="form-control"
                           style="background:#fff0f0" readonly placeholder="Otomatis terisi">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Harga Satuan</label>
                    <input type="text" id="inp-harga" class="form-control"
                           style="background:#fff0f0" readonly placeholder="Rp 0">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Jumlah</label>
                    <input type="number" id="inp-jumlah" class="form-control"
                           value="1" min="1" oninput="cekTombolTambah()">
                </div>
                <div class="col-md-2 d-grid">
                    <button class="btn btn-success fw-semibold"
                            id="btn-tambah" onclick="tambahKeKeranjang()" disabled>
                        + Tambahkan
                    </button>
                </div>
            </div>
            <div id="alert-cari" class="mt-3 d-none"></div>
        </div>
    </div>

    <!-- Card keranjang -->
    <div class="card">
        <div class="card-body">
            <h6 class="fw-semibold mb-3">Keranjang Belanja</h6>
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama</th>
                            <th>Harga</th>
                            <th style="width:100px">Jumlah</th>
                            <th>Subtotal</th>
                            <th style="width:130px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-keranjang">
                        <tr id="tr-kosong">
                            <td colspan="6" class="text-center text-muted py-4">
                                Belum ada barang ditambahkan
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="table-light">
                            <td colspan="4" class="text-end fw-bold">TOTAL</td>
                            <td colspan="2" class="fw-bold text-success fs-6"
                                id="lbl-total">Rp 0</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="d-flex justify-content-end mt-3">
                <button class="btn btn-primary px-5 py-2 fw-semibold"
                        id="btn-bayar" onclick="bayar()" disabled>
                    💳 Bayar
                </button>
            </div>
        </div>
    </div>

</div>
</div>

<!-- Modal Edit -->
<div class="modal-edit-overlay" id="modal-edit">
    <div class="modal-edit-box">
        <h6 class="fw-bold mb-1">Ubah Jumlah Barang</h6>
        <p class="text-muted small mb-3" id="modal-nama-barang"></p>
        <input type="hidden" id="modal-idx">
        <div class="mb-3">
            <label class="form-label fw-semibold">Jumlah Baru</label>
            <input type="number" id="modal-jumlah" class="form-control" min="1"
                   onkeydown="if(event.key==='Enter') simpanEdit()">
        </div>
        <div class="d-flex gap-2 justify-content-end">
            <button class="btn btn-secondary" onclick="tutupModal()">Batal</button>
            <button class="btn btn-primary"   onclick="simpanEdit()">Simpan</button>
        </div>
    </div>
</div>
@endsection

@section('script-page')
<style>
    .modal-edit-overlay {
        display: none; position: fixed; inset: 0;
        background: rgba(0,0,0,0.45); z-index: 9999;
        align-items: center; justify-content: center;
    }
    .modal-edit-overlay.show { display: flex; }
    .modal-edit-box {
        background: #fff; border-radius: 14px;
        padding: 28px; width: 340px;
        box-shadow: 0 8px 32px rgba(0,0,0,.18);
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // ── State ─────────────────────────────────────────────────
let barangAktif = null;
let keranjang   = [];
const csrf      = $('meta[name="csrf-token"]').attr('content');
    // ── Format Rupiah ─────────────────────────────────────────
    function rupiah(n) {
        return 'Rp ' + parseInt(n).toLocaleString('id-ID');
    }

    // ── Cek tombol Tambahkan ──────────────────────────────────
    function cekTombolTambah() {
        const jml = parseInt(document.getElementById('inp-jumlah').value);
        document.getElementById('btn-tambah').disabled = !(barangAktif && jml > 0);
    }

    // ── Tampil alert ──────────────────────────────────────────
    function tampilAlert(tipe, html) {
        const el = document.getElementById('alert-cari');
        el.className = 'mt-3 alert alert-' + tipe;
        el.innerHTML = html;
    }

    // Cari Barang jQuery AJAX
    function cariBarang() {
        const kode = document.getElementById('inp-kode').value.trim().toUpperCase();
        if (!kode) return;

        barangAktif = null;
        cekTombolTambah();
        document.getElementById('inp-nama').value  = '';
        document.getElementById('inp-harga').value = '';
        document.getElementById('alert-cari').classList.add('d-none');

        $.ajax({
            method  : 'POST',
            url     : "{{ route('pos.cari') }}",
            data    : { _token: csrf, kode: kode },
            success : function(res) {
                console.log('jQuery response cari:', res);
                if (res.status === 'success') {
                    barangAktif = res.data;
                    document.getElementById('inp-nama').value   = barangAktif.nama;
                    document.getElementById('inp-harga').value  = rupiah(barangAktif.harga);
                    document.getElementById('inp-jumlah').value = 1;
                    cekTombolTambah();
                    tampilAlert('success',
                        '✅ Barang ditemukan: <strong>' + barangAktif.nama + '</strong>');
                    document.getElementById('inp-jumlah').focus();
                    document.getElementById('inp-jumlah').select();
                } else {
                    tampilAlert('danger', '❌ ' + res.message);
                }
            },
            error   : function(xhr) {
                console.log('jQuery error cari:', xhr);
                const msg = xhr.responseJSON
                    ? xhr.responseJSON.message
                    : 'Barang tidak ditemukan';
                tampilAlert('danger', '❌ ' + msg);
            }
        });
    }

    // ── Tambah ke Keranjang ───────────────────────────────────
    function tambahKeKeranjang() {
        if (!barangAktif) return;
        const jumlah = parseInt(document.getElementById('inp-jumlah').value);
        if (jumlah < 1) return;

        const idx = keranjang.findIndex(i => i.id_barang === barangAktif.id_barang);
        if (idx >= 0) {
            keranjang[idx].jumlah  += jumlah;
            keranjang[idx].subtotal = keranjang[idx].jumlah * keranjang[idx].harga;
        } else {
            keranjang.push({
                id_barang : barangAktif.id_barang,
                nama      : barangAktif.nama,
                harga     : parseInt(barangAktif.harga),
                jumlah    : jumlah,
                subtotal  : parseInt(barangAktif.harga) * jumlah
            });
        }
        renderKeranjang();
        resetFormInput();
    }

    // ── Render Tabel ──────────────────────────────────────────
    function renderKeranjang() {
        const tbody = document.getElementById('tbody-keranjang');
        tbody.innerHTML = '';

        if (keranjang.length === 0) {
            tbody.innerHTML = `<tr>
                <td colspan="6" class="text-center text-muted py-4">
                    Belum ada barang ditambahkan
                </td></tr>`;
            document.getElementById('lbl-total').textContent = 'Rp 0';
            document.getElementById('btn-bayar').disabled = true;
            return;
        }

        keranjang.forEach(function(item, idx) {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="fw-semibold">${item.id_barang}</td>
                <td>${item.nama}</td>
                <td>${rupiah(item.harga)}</td>
                <td class="text-center fw-semibold">${item.jumlah}</td>
                <td class="fw-semibold">${rupiah(item.subtotal)}</td>
                <td>
                    <div class="d-flex gap-1">
                        <button class="btn btn-warning btn-sm"
                                onclick="bukaModalEdit(${idx})">Ubah</button>
                        <button class="btn btn-danger btn-sm"
                                onclick="hapusBaris(${idx})">Hapus</button>
                    </div>
                </td>
            `;
            tbody.appendChild(tr);
        });

        const total = keranjang.reduce((s, i) => s + i.subtotal, 0);
        document.getElementById('lbl-total').textContent = rupiah(total);
        document.getElementById('btn-bayar').disabled = false;
    }

    // ── Modal Edit ────────────────────────────────────────────
    function bukaModalEdit(idx) {
        document.getElementById('modal-idx').value = idx;
        document.getElementById('modal-nama-barang').textContent =
            keranjang[idx].nama + ' — ' + rupiah(keranjang[idx].harga);
        document.getElementById('modal-jumlah').value = keranjang[idx].jumlah;
        document.getElementById('modal-edit').classList.add('show');
        setTimeout(function() {
            document.getElementById('modal-jumlah').focus();
            document.getElementById('modal-jumlah').select();
        }, 100);
    }

    function tutupModal() {
        document.getElementById('modal-edit').classList.remove('show');
    }

    function simpanEdit() {
        const idx = parseInt(document.getElementById('modal-idx').value);
        const jml = parseInt(document.getElementById('modal-jumlah').value);
        if (jml < 1) return;
        keranjang[idx].jumlah   = jml;
        keranjang[idx].subtotal = jml * keranjang[idx].harga;
        tutupModal();
        renderKeranjang();
    }

    // ── Hapus Baris ───────────────────────────────────────────
    function hapusBaris(idx) {
        keranjang.splice(idx, 1);
        renderKeranjang();
    }

    // ── Bayar — jQuery AJAX ───────────────────────────────────
    function bayar() {
        if (keranjang.length === 0) return;
        const total    = keranjang.reduce((s, i) => s + i.subtotal, 0);
        const btnBayar = document.getElementById('btn-bayar');

        btnBayar.disabled  = true;
        btnBayar.innerHTML =
            '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Memproses...';

        $.ajax({
            method  : 'POST',
            url     : "{{ route('pos.bayar') }}",
            data    : {
                _token : csrf,
                total  : total,
                items  : JSON.stringify(keranjang.map(i => ({
                    id_barang : i.id_barang,
                    jumlah    : i.jumlah,
                    subtotal  : i.subtotal
                })))
            },
            success : function(res) {
                console.log('jQuery response bayar:', res);
                btnBayar.disabled  = false;
                btnBayar.innerHTML = '💳 Bayar';
                Swal.fire({
                    icon             : 'success',
                    title            : 'Pembayaran Berhasil!',
                    html             : 'Transaksi <strong>#' + res.data.id_penjualan
                                     + '</strong> berhasil disimpan.<br>'
                                     + '<span class="text-muted">Total: ' + rupiah(total) + '</span>',
                    confirmButtonText: 'Transaksi Baru',
                    confirmButtonColor: '#0d6efd'
                }).then(function() { resetSemua(); });
            },
            error   : function(xhr) {
                console.log('jQuery error bayar:', xhr);
                const msg = xhr.responseJSON
                    ? xhr.responseJSON.message
                    : 'Terjadi kesalahan pada server';
                Swal.fire({ icon: 'error', title: 'Pembayaran Gagal', text: msg });
                btnBayar.disabled  = false;
                btnBayar.innerHTML = '💳 Bayar';
            }
        });
    }

    // ── Reset Form Input ──────────────────────────────────────
    function resetFormInput() {
        barangAktif = null;
        document.getElementById('inp-kode').value   = '';
        document.getElementById('inp-nama').value   = '';
        document.getElementById('inp-harga').value  = '';
        document.getElementById('inp-jumlah').value = 1;
        document.getElementById('btn-tambah').disabled = true;
        document.getElementById('alert-cari').classList.add('d-none');
        document.getElementById('inp-kode').focus();
    }

    // ── Reset Semua ───────────────────────────────────────────
    function resetSemua() {
        keranjang = [];
        renderKeranjang();
        resetFormInput();
    }
</script>
@endsection