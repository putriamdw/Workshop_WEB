@extends('layouts.main')

@section('style-page')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<style>
    /* (a) Cursor pointer saat hover di atas setiap row */
    #tableBarang tbody tr,
    #tableBarangDT tbody tr {
        cursor: pointer;
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">JS 2 & 3 - Tabel & CRUD Modal</h4>
            </div>
            <div class="card-body">

                {{-- Form Tambah --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-5">
                        <label class="form-label">Nama Barang</label>
                        <input type="text" id="namaBrg" class="form-control" placeholder="Nama barang" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Harga (Rp)</label>
                        <input type="number" id="hargaBrg" class="form-control" placeholder="Contoh: 50000" required min="0">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="button" id="btnTambah" class="btn btn-primary w-100" onclick="tambahBarang()">
                            Tambah
                        </button>
                    </div>
                </div>

                {{-- Tabs --}}
                <ul class="nav nav-tabs mb-3" id="tabelTab">
                    <li class="nav-item">
                        <a class="nav-link active" href="#" onclick="gantTab('biasa', this)">Tabel HTML</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="gantTab('datatables', this)">DataTables</a>
                    </li>
                </ul>

                {{-- Tabel Biasa --}}
                <div id="panelBiasa">
                    <table class="table table-bordered table-hover" id="tableBarang">
                        <thead class="table-light">
                            <tr><th>ID Barang</th><th>Nama Barang</th><th>Harga</th></tr>
                        </thead>
                        <tbody id="tbodyBiasa">
                            <tr id="emptyRow">
                                <td colspan="3" class="text-center text-muted">Belum ada data. Klik baris untuk edit/hapus.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- DataTables --}}
                <div id="panelDT" class="d-none">
                    <table class="table table-bordered table-hover" id="tableBarangDT">
                        <thead class="table-light">
                            <tr><th>ID Barang</th><th>Nama Barang</th><th>Harga</th></tr>
                        </thead>
                        <tbody id="tbodyDT"></tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- (b) Modal muncul saat row di-click --}}
<div class="modal fade" id="modalBarang" tabindex="-1" aria-labelledby="modalBarangLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalBarangLabel">Detail Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                {{-- (i) ID Barang readonly --}}
                <div class="mb-3">
                    <label class="form-label">ID Barang</label>
                    <input type="text" id="modalId" class="form-control" readonly>
                </div>
                {{-- (ii) Nama & Harga required --}}
                <div class="mb-3">
                    <label class="form-label">Nama Barang</label>
                    <input type="text" id="modalNama" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Harga (Rp)</label>
                    <input type="number" id="modalHarga" class="form-control" required min="0">
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                {{-- (v) Hapus, row terhapus --}}
                <button type="button" id="btnHapus" class="btn btn-danger" onclick="hapusBarang()">
                    Hapus
                </button>
                {{-- (v) Ubah, data row berubah --}}
                <button type="button" id="btnUbah" class="btn btn-success" onclick="ubahBarang()">
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script-page')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
    let idCounter    = 1;
    let dtTable      = null;
    let selectedRowEl  = null; // untuk tabel biasa
    let selectedDTRow  = null; // untuk DataTables
    let modalInstance  = null;

    $(document).ready(function () {
        dtTable = $('#tableBarangDT').DataTable({
            language: {
                search: "Cari:", lengthMenu: "Tampil _MENU_ data",
                info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                emptyTable: "Belum ada data"
            }
        });
        modalInstance = new bootstrap.Modal(document.getElementById('modalBarang'));
    });

    function tambahBarang() {
        const inputNama  = document.getElementById('namaBrg');
        const inputHarga = document.getElementById('hargaBrg');
        const btn        = document.getElementById('btnTambah');

        inputNama.classList.remove('is-invalid');
        inputHarga.classList.remove('is-invalid');

        // (i) checkValidity
        let valid = true;
        if (!inputNama.checkValidity() || inputNama.value.trim() === '') {
            inputNama.classList.add('is-invalid'); valid = false;
        }
        if (!inputHarga.checkValidity() || inputHarga.value === '') {
            inputHarga.classList.add('is-invalid'); valid = false;
        }

        // (ii) reportValidity
        if (!valid) {
            if (!inputNama.checkValidity()) inputNama.reportValidity();
            else inputHarga.reportValidity();
            return;
        }

        // (iii) Spinner
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Menambah...';

        setTimeout(() => {
            const id       = 'BRG-' + String(idCounter).padStart(3, '0');
            const nama     = inputNama.value.trim();
            const harga    = parseInt(inputHarga.value);
            const hargaStr = 'Rp ' + harga.toLocaleString('id-ID');

            // Tabel biasa
            const emptyRow = document.getElementById('emptyRow');
            if (emptyRow) emptyRow.remove();

            const tr = document.createElement('tr');
            tr.dataset.id    = id;
            tr.dataset.nama  = nama;
            tr.dataset.harga = harga;
            tr.innerHTML = `<td>${id}</td><td>${nama}</td><td>${hargaStr}</td>`;
            tr.addEventListener('click', () => bukaModal(tr, null));
            document.getElementById('tbodyBiasa').appendChild(tr);

            // DataTables
            const row = dtTable.row.add([id, nama, hargaStr]).draw();
            const trDT = row.node();
            $(trDT).attr({ 'data-id': id, 'data-nama': nama, 'data-harga': harga });
            $(trDT).on('click', function () { bukaModal(null, row); });

            // Reset input
            inputNama.value  = '';
            inputHarga.value = '';
            idCounter++;
            btn.disabled  = false;
            btn.innerHTML = 'Tambah';
        }, 500);
    }

    // (b) Buka modal saat row di-click
    function bukaModal(trEl, dtRow) {
        selectedRowEl = trEl;
        selectedDTRow = dtRow;

        let id, nama, harga;
        if (trEl) {
            id = trEl.dataset.id; nama = trEl.dataset.nama; harga = trEl.dataset.harga;
        } else {
            const data = dtRow.data();
            id   = data[0];
            nama = data[1];
            harga = $(dtRow.node()).attr('data-harga');
        }

        document.getElementById('modalId').value    = id;
        document.getElementById('modalNama').value  = nama;
        document.getElementById('modalHarga').value = harga;
        modalInstance.show();
    }

    // (v) Ubah: data row berubah sesuai input
    function ubahBarang() {
        const inputNama  = document.getElementById('modalNama');
        const inputHarga = document.getElementById('modalHarga');
        const btnUbah    = document.getElementById('btnUbah');

        inputNama.classList.remove('is-invalid');
        inputHarga.classList.remove('is-invalid');

        // (iii) Ketentuan nomor 1: checkValidity + reportValidity
        let valid = true;
        if (!inputNama.checkValidity() || inputNama.value.trim() === '') {
            inputNama.classList.add('is-invalid'); inputNama.reportValidity(); valid = false;
        }
        if (!inputHarga.checkValidity() || inputHarga.value === '') {
            inputHarga.classList.add('is-invalid'); inputHarga.reportValidity(); valid = false;
        }
        if (!valid) return;

        // Spinner
        btnUbah.disabled = true;
        btnUbah.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Menyimpan...';

        setTimeout(() => {
            const id       = document.getElementById('modalId').value;
            const nama     = inputNama.value.trim();
            const harga    = parseInt(inputHarga.value);
            const hargaStr = 'Rp ' + harga.toLocaleString('id-ID');

            if (selectedRowEl) {
                selectedRowEl.dataset.nama  = nama;
                selectedRowEl.dataset.harga = harga;
                selectedRowEl.cells[1].textContent = nama;
                selectedRowEl.cells[2].textContent = hargaStr;
            }
            if (selectedDTRow) {
                selectedDTRow.data([id, nama, hargaStr]).draw();
                $(selectedDTRow.node()).attr({ 'data-nama': nama, 'data-harga': harga });
                // Re-attach click event setelah draw
                $(selectedDTRow.node()).off('click').on('click', function () { bukaModal(null, selectedDTRow); });
            }

            btnUbah.disabled  = false;
            btnUbah.innerHTML = 'Simpan Perubahan';

            // (iv) Tutup modal setelah berhasil
            modalInstance.hide();
        }, 600);
    }

    // (v) Hapus: row terhapus
    function hapusBarang() {
        const btnHapus = document.getElementById('btnHapus');
        btnHapus.disabled = true;
        btnHapus.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

        setTimeout(() => {
            if (selectedRowEl) selectedRowEl.remove();
            if (selectedDTRow) selectedDTRow.remove().draw();

            btnHapus.disabled  = false;
            btnHapus.innerHTML = 'Hapus';

            // (iv) Tutup modal setelah berhasil
            modalInstance.hide();
        }, 600);
    }

    function gantTab(tipe, el) {
        event.preventDefault();
        document.querySelectorAll('#tabelTab .nav-link').forEach(a => a.classList.remove('active'));
        el.classList.add('active');
        if (tipe === 'biasa') {
            document.getElementById('panelBiasa').classList.remove('d-none');
            document.getElementById('panelDT').classList.add('d-none');
        } else {
            document.getElementById('panelBiasa').classList.add('d-none');
            document.getElementById('panelDT').classList.remove('d-none');
        }
    }
</script>
@endsection