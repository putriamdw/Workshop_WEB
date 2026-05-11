<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Titik Kunjungan Toko</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #2E4057 0%, #4472C4 100%); min-height: 100vh; }
        .main-card { border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
    </style>
</head>
<body>
<div class="container py-5 d-flex justify-content-center align-items-center" style="min-height:100vh;">
    <div class="card main-card" style="max-width:420px; width:100%;">
        <div class="card-body p-4">

            <div class="text-center mb-4">
                <div style="font-size:2.5rem;">📍</div>
                <h4 class="fw-bold mb-1">Titik Kunjungan Toko</h4>
                <p class="text-muted small">Scan QR Code yang ada di toko untuk mencatat kunjungan</p>
            </div>

            {{-- Step 1: Scan QR --}}
            <div id="stepScan">
                <div id="reader" style="width:100%; border-radius:12px; overflow:hidden;"></div>
                <div class="text-center mt-3">
                    <button id="btnMulaiScan" class="btn btn-primary px-4">
                        <i>📷</i> Mulai Scan QR Code
                    </button>
                    <button id="btnStopScan" class="btn btn-secondary px-4" style="display:none;">
                        ⏹ Stop
                    </button>
                </div>
            </div>

            {{-- Step 2: Ambil GPS --}}
            <div id="stepGps" style="display:none;">
                <div class="alert alert-success text-center">
                    <strong>✅ QR terbaca!</strong><br>
                    Kode Toko: <code id="kodeTokoBaca"></code>
                </div>
                <div class="alert alert-warning text-center">
                    <div id="spinnerGps">
                        ⏳ Mengambil koordinat GPS kamu...
                    </div>
                </div>
            </div>

            {{-- Hasil --}}
            <div id="hasilKunjungan" style="display:none;">
                <div id="hasilDiterima" class="text-center" style="display:none;">
                    <div style="font-size:4rem;">✅</div>
                    <h4 class="fw-bold text-success">Kunjungan Diterima!</h4>
                    <p id="pesanDiterima" class="text-muted"></p>
                    <div class="badge bg-success fs-6 px-3 py-2" id="namaTokoDiterima"></div>
                </div>
                <div id="hasilDitolak" class="text-center" style="display:none;">
                    <div style="font-size:4rem;">❌</div>
                    <h4 class="fw-bold text-danger">Kunjungan Ditolak!</h4>
                    <p id="pesanDitolak" class="text-muted"></p>
                </div>
                <div id="hasilError" class="text-center" style="display:none;">
                    <div style="font-size:4rem;">⚠️</div>
                    <h4 class="fw-bold text-warning">Terjadi Masalah</h4>
                    <p id="pesanError" class="text-muted"></p>
                </div>
                <div class="text-center mt-3">
                    <button onclick="location.reload()" class="btn btn-outline-primary w-100">
                        🔄 Kunjungi Toko Lain
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

<audio id="beep" src="{{ asset('assets/sounds/beep.mp3') }}" preload="auto"></audio>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
let html5QrCode = null;
let sudahScan   = false;

const stepScan       = document.getElementById('stepScan');
const stepGps        = document.getElementById('stepGps');
const hasilKunjungan = document.getElementById('hasilKunjungan');
const hasilDiterima  = document.getElementById('hasilDiterima');
const hasilDitolak   = document.getElementById('hasilDitolak');
const hasilError     = document.getElementById('hasilError');
const kodeTokoBaca   = document.getElementById('kodeTokoBaca');
const btnMulai       = document.getElementById('btnMulaiScan');
const btnStop        = document.getElementById('btnStopScan');
const beep           = document.getElementById('beep');

function tampilHasil(status, data) {
    stepGps.style.display          = 'none';
    hasilKunjungan.style.display   = 'block';

    if (status === 'diterima') {
        hasilDiterima.style.display = 'block';
        document.getElementById('pesanDiterima').textContent    = data.message;
        document.getElementById('namaTokoDiterima').textContent = data.nama_toko;
    } else if (status === 'ditolak') {
        hasilDitolak.style.display = 'block';
        document.getElementById('pesanDitolak').textContent = data.message;
    } else {
        hasilError.style.display = 'block';
        document.getElementById('pesanError').textContent = data.message;
    }
}

function ambilGpsDanKirim(kodeToko) {
    if (!navigator.geolocation) {
        tampilHasil('error', { message: 'Browser tidak mendukung Geolocation.' });
        return;
    }

    navigator.geolocation.getCurrentPosition(
        async (pos) => {
            try {
                const res = await fetch('{{ route("toko.simpan-kunjungan") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        kode_toko:           kodeToko,
                        latitude_kunjungan:  pos.coords.latitude,
                        longitude_kunjungan: pos.coords.longitude,
                        accuracy_kunjungan:  pos.coords.accuracy,
                    })
                });

                const data = await res.json();
                tampilHasil(data.status, data);

            } catch (e) {
                tampilHasil('error', { message: 'Gagal menghubungi server: ' + e.message });
            }
        },
        (err) => {
            tampilHasil('error', { message: 'Gagal ambil GPS: ' + err.message + '. Pastikan izin lokasi diaktifkan.' });
        },
        { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
    );
}

function mulaiScan() {
    sudahScan   = false;
    html5QrCode = new Html5Qrcode("reader");
    btnMulai.style.display = 'none';
    btnStop.style.display  = 'inline-block';

    html5QrCode.start(
        { facingMode: "environment" },
        { fps: 10, qrbox: { width: 250, height: 250 } },
        async (decodedText) => {
            if (sudahScan) return;
            sudahScan = true;

            beep.play().catch(() => {});
            html5QrCode.stop().catch(() => {});

            stepScan.style.display = 'none';
            kodeTokoBaca.textContent = decodedText;
            stepGps.style.display    = 'block';

            ambilGpsDanKirim(decodedText);
        },
        (error) => {}
    ).catch(err => alert('Kamera tidak bisa diakses: ' + err));
}

btnMulai.addEventListener('click', mulaiScan);
btnStop.addEventListener('click', () => {
    html5QrCode.stop().catch(() => {});
    btnMulai.style.display = 'inline-block';
    btnStop.style.display  = 'none';
});
</script>
</body>
</html>