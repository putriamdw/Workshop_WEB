@extends('layouts.main')
@section('title', 'Tambah Customer 1 - Blob')
@section('content')
<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Tambah Customer — Foto sebagai Blob</h4>
            </div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">{{ $errors->first() }}</div>
                @endif

                <form action="{{ route('customer-data.store-blob') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" name="nama" class="form-control" value="{{ old('nama') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <input type="text" name="alamat" class="form-control" value="{{ old('alamat') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Provinsi</label>
                        <input type="text" name="provinsi" class="form-control" value="{{ old('provinsi') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kota</label>
                        <input type="text" name="kota" class="form-control" value="{{ old('kota') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kecamatan</label>
                        <input type="text" name="kecamatan" class="form-control" value="{{ old('kecamatan') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kodepos - Kelurahan</label>
                        <div class="input-group">
                            <input type="text" name="kodepos" class="form-control" placeholder="Kodepos" value="{{ old('kodepos') }}" style="max-width:120px">
                            <input type="text" name="kelurahan" class="form-control" placeholder="Kelurahan" value="{{ old('kelurahan') }}">
                        </div>
                    </div>

                    {{-- Preview foto --}}
                    <div class="mb-3">
                        <label class="form-label">Foto</label>
                        <div class="border rounded p-3 text-center bg-light" style="min-height:120px">
                            <img id="previewFoto" src="" alt="Preview Foto"
                                 style="display:none; max-height:150px; border-radius:8px; object-fit:cover;">
                            <p id="noFoto" class="text-muted mt-3">Belum ada foto</p>
                        </div>
                        <input type="hidden" name="foto_base64" id="foto_base64">
                        <button type="button" class="btn btn-secondary mt-2"
                                data-bs-toggle="modal" data-bs-target="#modalKamera">
                            <i class="mdi mdi-camera"></i> Ambil Foto
                        </button>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary" id="btnSimpan" disabled>
                            Simpan Data
                        </button>
                        <a href="{{ route('customer-data.index') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal Kamera --}}
<div class="modal fade" id="modalKamera" tabindex="-1" aria-labelledby="modalKameraLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalKameraLabel">Modal Ambil Foto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                {{-- Pilihan kamera --}}
                <div class="mb-2">
                    <label class="form-label small">Pilihan Kamera</label>
                    <select id="selectKamera" class="form-select form-select-sm"></select>
                </div>

                {{-- Video & Snapshot side by side --}}
                <div class="row g-3">
                    <div class="col-6 text-center">
                        <p class="small text-muted mb-1">Video</p>
                        <video id="video" autoplay playsinline
                               style="width:100%; border-radius:8px; background:#000; max-height:260px; object-fit:cover;"></video>
                    </div>
                    <div class="col-6 text-center">
                        <p class="small text-muted mb-1">Snapshot</p>
                        <canvas id="canvas"
                                style="width:100%; border-radius:8px; background:#eee; max-height:260px;"></canvas>
                    </div>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <button type="button" class="btn btn-secondary" id="btnAmbilFoto">
                    <i class="mdi mdi-camera"></i> Ambil Foto
                </button>
                <button type="button" class="btn btn-success" id="btnSimpanFoto" disabled
                        data-bs-dismiss="modal">
                    Simpan Foto
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script-page')
<script>
const video        = document.getElementById('video');
const canvas       = document.getElementById('canvas');
const selectKamera = document.getElementById('selectKamera');
const btnAmbil     = document.getElementById('btnAmbilFoto');
const btnSimpan    = document.getElementById('btnSimpanFoto');
const inputBase64  = document.getElementById('foto_base64');
const previewFoto  = document.getElementById('previewFoto');
const noFoto       = document.getElementById('noFoto');
const btnSimpanForm= document.getElementById('btnSimpan');

let stream = null;
let snapshotData = null;

// Ambil daftar kamera yang tersedia
async function loadKameras() {
    const devices = await navigator.mediaDevices.enumerateDevices();
    const kameras = devices.filter(d => d.kind === 'videoinput');
    selectKamera.innerHTML = '';
    kameras.forEach((k, i) => {
        const opt = document.createElement('option');
        opt.value = k.deviceId;
        opt.text  = k.label || `Kamera ${i + 1}`;
        selectKamera.appendChild(opt);
    });
}

// Nyalakan kamera
async function startKamera(deviceId = null) {
    if (stream) stream.getTracks().forEach(t => t.stop());
    const constraints = { video: deviceId ? { deviceId: { exact: deviceId } } : true };
    stream = await navigator.mediaDevices.getUserMedia(constraints);
    video.srcObject = stream;
    await loadKameras();
}

// Ganti kamera saat select berubah
selectKamera.addEventListener('change', () => startKamera(selectKamera.value));

// Buka modal → nyalakan kamera
document.getElementById('modalKamera').addEventListener('show.bs.modal', () => {
    startKamera();
    btnSimpan.disabled = true;
    snapshotData = null;
    // Reset canvas
    const ctx = canvas.getContext('2d');
    ctx.clearRect(0, 0, canvas.width, canvas.height);
});

// Tutup modal → matikan kamera
document.getElementById('modalKamera').addEventListener('hide.bs.modal', () => {
    if (stream) stream.getTracks().forEach(t => t.stop());
});

// Ambil foto → tampilkan di canvas (snapshot)
btnAmbil.addEventListener('click', () => {
    canvas.width  = video.videoWidth  || 400;
    canvas.height = video.videoHeight || 300;
    const ctx = canvas.getContext('2d');
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
    snapshotData   = canvas.toDataURL('image/jpeg');
    btnSimpan.disabled = false;
});

// Simpan foto → kirim ke form utama
btnSimpan.addEventListener('click', () => {
    if (!snapshotData) return;
    inputBase64.value      = snapshotData;
    previewFoto.src        = snapshotData;
    previewFoto.style.display = 'block';
    noFoto.style.display   = 'none';
    btnSimpanForm.disabled = false;
});
</script>
@endsection