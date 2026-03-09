@extends('layouts.main')

@section('style-page')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
@endsection

@section('content')
<div class="row g-4">

    {{-- Card 1: Select Biasa --}}
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0">Select</h5>
            </div>
            <div class="card-body">

                {{-- (b) Opsi input select ditambahkan melalui form input text Kota --}}
                <div class="mb-3">
                    <label class="form-label">Tambah Kota</label>
                    <div class="input-group">
                        <input type="text" id="inputKota1" class="form-control" placeholder="Nama kota...">
                        <button class="btn btn-primary" type="button" onclick="tambahKota('biasa')">+ Tambah</button>
                    </div>
                </div>

                {{-- (a) Form select kota adalah element input select --}}
                <div class="mb-3">
                    <label class="form-label">Pilih Kota</label>
                    {{-- (b) Nama kota sebagai value dan nilai tampil --}}
                    <select id="selectKota1" class="form-select" onchange="updateTerpilih('biasa')">
                        <option value="">-- Pilih kota --</option>
                        <option value="Jakarta">Jakarta</option>
                        <option value="Surabaya">Surabaya</option>
                        <option value="Bandung">Bandung</option>
                    </select>
                </div>

                {{-- (c) Kota terpilih ditampilkan --}}
                <div class="mb-1">
                    <label class="form-label">Kota Terpilih</label>
                    <div id="terpilih1" class="form-control bg-light" style="min-height:38px;">
                        <span class="text-muted" id="placeholder1">Belum ada kota dipilih</span>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Card 2: Select2 --}}
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0">Select 2</h5>
            </div>
            <div class="card-body">

                {{-- (b) Opsi input select ditambahkan melalui form input text Kota --}}
                <div class="mb-3">
                    <label class="form-label">Tambah Kota</label>
                    <div class="input-group">
                        <input type="text" id="inputKota2" class="form-control" placeholder="Nama kota...">
                        <button class="btn btn-primary" type="button" onclick="tambahKota('select2')">+ Tambah</button>
                    </div>
                </div>

                {{-- (a) Form select kota menggunakan Select2 --}}
                <div class="mb-3">
                    <label class="form-label">Pilih Kota</label>
                    {{-- (b) Nama kota sebagai value dan nilai tampil --}}
                    <select id="selectKota2" class="form-select" data-bs-theme="bootstrap-5">
                        <option value="">-- Pilih atau cari kota --</option>
                        <option value="Jakarta">Jakarta</option>
                        <option value="Surabaya">Surabaya</option>
                        <option value="Bandung">Bandung</option>
                    </select>
                </div>

                {{-- (c) Kota terpilih ditampilkan --}}
                <div class="mb-1">
                    <label class="form-label">Kota Terpilih</label>
                    <div id="terpilih2" class="form-control bg-light" style="min-height:38px;">
                        <span class="text-muted" id="placeholder2">Belum ada kota dipilih</span>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>
@endsection

@section('script-page')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function () {
        // Inisialisasi Select2
        $('#selectKota2').select2({
            theme: 'bootstrap-5',
            placeholder: '-- Pilih atau cari kota --',
            allowClear: true
        });

        // $(this) merujuk pada dirinya sendiri yaitu #selectKota2
        // Diletakkan di dalam .ready() untuk memastikan element sudah terload
        $('#selectKota2').on('change', function () {
            updateTerpilih('select2');
        });
    });

    // (b) Tambah opsi kota ke element select
    function tambahKota(tipe) {
        const inputId  = tipe === 'biasa' ? 'inputKota1' : 'inputKota2';
        const selectId = tipe === 'biasa' ? 'selectKota1' : 'selectKota2';
        const input    = document.getElementById(inputId);
        const select   = document.getElementById(selectId);
        const namaKota = input.value.trim();

        if (!namaKota) {
            input.classList.add('is-invalid');
            setTimeout(() => input.classList.remove('is-invalid'), 1500);
            return;
        }

        // Cek duplikat
        const existing = Array.from(select.options).map(o => o.value.toLowerCase());
        if (existing.includes(namaKota.toLowerCase())) {
            alert('Kota sudah ada!');
            return;
        }

        // (b) Nama kota sebagai value dan nilai tampil pada element select
        const option = new Option(namaKota, namaKota);
        select.appendChild(option);

        // Refresh Select2
        if (tipe === 'select2') {
            $('#selectKota2').trigger('change.select2');
        }

        input.value = '';
    }

    // (c) Kota yang terpilih ditampilkan pada Kota Terpilih
    function updateTerpilih(tipe) {
        const selectId     = tipe === 'biasa' ? 'selectKota1' : 'selectKota2';
        const terpilihId   = tipe === 'biasa' ? 'terpilih1'   : 'terpilih2';
        const placeholderId = tipe === 'biasa' ? 'placeholder1' : 'placeholder2';

        const nilai     = document.getElementById(selectId).value;
        const container = document.getElementById(terpilihId);

        if (!nilai) {
            container.innerHTML = `<span class="text-muted" id="${placeholderId}">Belum ada kota dipilih</span>`;
        } else {
            container.innerHTML = `<strong>${nilai}</strong>`;
        }
    }
</script>
@endsection