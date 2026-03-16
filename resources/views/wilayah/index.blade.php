@extends('layouts.main')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Wilayah Indonesia — jQuery AJAX</h4>
                    <a href="{{ route('wilayah.axios') }}" class="btn btn-sm btn-outline-success">
                        Lihat versi Axios →
                    </a>
                </div>
            </div>
            <div class="card-body">

                <div class="mb-3">
                    <label class="form-label fw-semibold">Provinsi</label>
                    <select class="form-select" id="sel-provinsi">
                        <option value="">-- Pilih Provinsi --</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Kota / Kabupaten</label>
                    <select class="form-select" id="sel-kota" disabled>
                        <option value="">-- Pilih Kota --</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Kecamatan</label>
                    <select class="form-select" id="sel-kecamatan" disabled>
                        <option value="">-- Pilih Kecamatan --</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Kelurahan / Desa</label>
                    <select class="form-select" id="sel-kelurahan" disabled>
                        <option value="">-- Pilih Kelurahan --</option>
                    </select>
                </div>

                <!-- Hasil -->
                <div id="box-hasil" class="alert alert-success d-none mt-1 small"></div>

            </div>
        </div>
    </div>
</div>
@endsection

@section('script-page')
<script>
    function isiSelect(selector, data, valKey, textKey, placeholder) {
        const el = $(selector);
        el.html('<option value="">' + placeholder + '</option>');
        $.each(data, function(i, item) {
            el.append('<option value="' + item[valKey] + '">' + item[textKey] + '</option>');
        });
        el.prop('disabled', false);
    }

    function kosongkanSelect(selector, placeholder) {
        $(selector)
            .html('<option value="">' + placeholder + '</option>')
            .prop('disabled', true);
    }

    function loadProvinsi() {
        $.ajax({
            method : 'GET',
            url    : "{{ route('wilayah.provinsi') }}",
            success: function(res) {
                console.log('Provinsi response:', res);
                isiSelect('#sel-provinsi', res.data, 'id', 'name', '-- Pilih Provinsi --');
            },
            error: function(xhr) { console.log('Error:', xhr); }
        });
    }

    function loadKota(idProvinsi) {
        $.ajax({
            method : 'GET',
            url    : "{{ url('/wilayah/kota') }}/" + idProvinsi,
            success: function(res) {
                console.log('Kota response:', res);
                isiSelect('#sel-kota', res.data, 'id', 'name', '-- Pilih Kota --');
            },
            error: function(xhr) { console.log('Error:', xhr); }
        });
    }

    function loadKecamatan(idKota) {
        $.ajax({
            method : 'GET',
            url    : "{{ url('/wilayah/kecamatan') }}/" + idKota,
            success: function(res) {
                console.log('Kecamatan response:', res);
                isiSelect('#sel-kecamatan', res.data, 'id', 'name', '-- Pilih Kecamatan --');
            },
            error: function(xhr) { console.log('Error:', xhr); }
        });
    }

    function loadKelurahan(idKecamatan) {
        $.ajax({
            method : 'GET',
            url    : "{{ url('/wilayah/kelurahan') }}/" + idKecamatan,
            success: function(res) {
                console.log('Kelurahan response:', res);
                isiSelect('#sel-kelurahan', res.data, 'id', 'name', '-- Pilih Kelurahan --');
            },
            error: function(xhr) { console.log('Error:', xhr); }
        });
    }

    $(document).ready(function() {
        loadProvinsi();

        $('#sel-provinsi').on('change', function() {
            const id = $(this).val();
            kosongkanSelect('#sel-kota',      '-- Pilih Kota --');
            kosongkanSelect('#sel-kecamatan', '-- Pilih Kecamatan --');
            kosongkanSelect('#sel-kelurahan', '-- Pilih Kelurahan --');
            $('#box-hasil').addClass('d-none');
            if (!id) return;
            loadKota(id);
        });

        $('#sel-kota').on('change', function() {
            const id = $(this).val();
            kosongkanSelect('#sel-kecamatan', '-- Pilih Kecamatan --');
            kosongkanSelect('#sel-kelurahan', '-- Pilih Kelurahan --');
            $('#box-hasil').addClass('d-none');
            if (!id) return;
            loadKecamatan(id);
        });

        $('#sel-kecamatan').on('change', function() {
            const id = $(this).val();
            kosongkanSelect('#sel-kelurahan', '-- Pilih Kelurahan --');
            $('#box-hasil').addClass('d-none');
            if (!id) return;
            loadKelurahan(id);
        });

        $('#sel-kelurahan').on('change', function() {
            if (!$(this).val()) { $('#box-hasil').addClass('d-none'); return; }
            const kelurahan = $(this).find('option:selected').text();
            const kecamatan = $('#sel-kecamatan').find('option:selected').text();
            const kota      = $('#sel-kota').find('option:selected').text();
            const provinsi  = $('#sel-provinsi').find('option:selected').text();
            $('#box-hasil').removeClass('d-none')
                .html('📍 <strong>' + kelurahan + '</strong>, '
                    + kecamatan + ', ' + kota + ', ' + provinsi);
        });
    });
</script>
@endsection