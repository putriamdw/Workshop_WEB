@extends('layouts.main')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Wilayah Indonesia — Axios</h4>
                    <a href="{{ route('wilayah.index') }}" class="btn btn-sm btn-outline-primary">
                        Lihat versi jQuery →
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

                <div id="box-hasil" class="alert alert-success d-none mt-1 small"></div>

            </div>
        </div>
    </div>
</div>
@endsection

@section('script-page')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    function isiSelect(selector, data, valKey, textKey, placeholder) {
        const el = document.querySelector(selector);
        el.innerHTML = '<option value="">' + placeholder + '</option>';
        data.forEach(function(item) {
            const opt = document.createElement('option');
            opt.value       = item[valKey];
            opt.textContent = item[textKey];
            el.appendChild(opt);
        });
        el.disabled = false;
    }

    function kosongkanSelect(selector, placeholder) {
        const el = document.querySelector(selector);
        el.innerHTML = '<option value="">' + placeholder + '</option>';
        el.disabled  = true;
    }

    function loadProvinsi() {
        axios.get("{{ route('wilayah.provinsi') }}")
            .then(function(res) {
                console.log('Provinsi response:', res.data);
                isiSelect('#sel-provinsi', res.data.data, 'id', 'name', '-- Pilih Provinsi --');
            })
            .catch(function(err) { console.log('Error:', err); });
    }

    function loadKota(id) {
        axios.get("{{ url('/wilayah/kota') }}/" + id)
            .then(function(res) {
                isiSelect('#sel-kota', res.data.data, 'id', 'name', '-- Pilih Kota --');
            })
            .catch(function(err) { console.log('Error:', err); });
    }

    function loadKecamatan(id) {
        axios.get("{{ url('/wilayah/kecamatan') }}/" + id)
            .then(function(res) {
                isiSelect('#sel-kecamatan', res.data.data, 'id', 'name', '-- Pilih Kecamatan --');
            })
            .catch(function(err) { console.log('Error:', err); });
    }

    function loadKelurahan(id) {
        axios.get("{{ url('/wilayah/kelurahan') }}/" + id)
            .then(function(res) {
                isiSelect('#sel-kelurahan', res.data.data, 'id', 'name', '-- Pilih Kelurahan --');
            })
            .catch(function(err) { console.log('Error:', err); });
    }

    document.addEventListener('DOMContentLoaded', function() {
        loadProvinsi();

        document.querySelector('#sel-provinsi').addEventListener('change', function() {
            const id = this.value;
            kosongkanSelect('#sel-kota',      '-- Pilih Kota --');
            kosongkanSelect('#sel-kecamatan', '-- Pilih Kecamatan --');
            kosongkanSelect('#sel-kelurahan', '-- Pilih Kelurahan --');
            document.getElementById('box-hasil').classList.add('d-none');
            if (!id) return;
            loadKota(id);
        });

        document.querySelector('#sel-kota').addEventListener('change', function() {
            const id = this.value;
            kosongkanSelect('#sel-kecamatan', '-- Pilih Kecamatan --');
            kosongkanSelect('#sel-kelurahan', '-- Pilih Kelurahan --');
            document.getElementById('box-hasil').classList.add('d-none');
            if (!id) return;
            loadKecamatan(id);
        });

        document.querySelector('#sel-kecamatan').addEventListener('change', function() {
            const id = this.value;
            kosongkanSelect('#sel-kelurahan', '-- Pilih Kelurahan --');
            document.getElementById('box-hasil').classList.add('d-none');
            if (!id) return;
            loadKelurahan(id);
        });

        document.querySelector('#sel-kelurahan').addEventListener('change', function() {
            const box = document.getElementById('box-hasil');
            if (!this.value) { box.classList.add('d-none'); return; }
            const kelurahan = this.options[this.selectedIndex].text;
            const kecamatan = document.querySelector('#sel-kecamatan').options[document.querySelector('#sel-kecamatan').selectedIndex].text;
            const kota      = document.querySelector('#sel-kota').options[document.querySelector('#sel-kota').selectedIndex].text;
            const provinsi  = document.querySelector('#sel-provinsi').options[document.querySelector('#sel-provinsi').selectedIndex].text;
            box.classList.remove('d-none');
            box.innerHTML = '📍 <strong>' + kelurahan + '</strong>, '
                          + kecamatan + ', ' + kota + ', ' + provinsi;
        });
    });
</script>
@endsection