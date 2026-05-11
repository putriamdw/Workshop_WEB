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

        /* Lampiran 3 breakdown box */
        .breakdown-box {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 1rem 1.2rem;
            font-family: 'Courier New', monospace;
            font-size: 0.82rem;
            text-align: left;
            margin-top: 1rem;
        }
        .breakdown-box .line { margin-bottom: 2px; }
        .breakdown-box .divider { border-top: 1px dashed #adb5bd; margin: 8px 0; }
        .breakdown-box .highlight { font-weight: bold; color: #0d6efd; }
        .breakdown-box .result-diterima { font-weight: bold; color: #198754; font-size: 0.9rem; }
        .breakdown-box .result-ditolak  { font-weight: bold; color: #dc3545; font-size: 0.9rem; }

        .diagram-arrow {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 0.78rem;
            color: #6c757d;
            margin-bottom: 8px;
            font-family: 'Courier New', monospace;
        }
        .diagram-node {
            background: #e9ecef;
            border: 1px solid #ced4da;
            border-radius: 6px;
            padding: 3px 10px;
            font-weight: bold;
            color: #495057;
        }
        .diagram-line {
            flex: 1;
            height: 1px;
            background: repeating-linear-gradient(90deg, #adb5bd 0, #adb5bd 4px, transparent 4px, transparent 8px);
        }
    </style>
</head>
<body>
<div class="container py-5 d-flex justify-content-center align-items-center" style="min-height:100vh;">
    <div class="card main-card" style="max-width:460px; width:100%;">
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

                {{-- Diterima --}}
                <div id="hasilDiterima" style="display:none;">
                    <div class="text-center">
                        <div style="font-size:4rem;">✅</div>
                        <h4 class="fw-bold text-success">Kunjungan Diterima!</h4>
                        <p id="pesanDiterima" class="text-muted"></p>
                        <div class="badge bg-success fs-6 px-3 py-2 mb-2" id="namaTokoDiterima"></div>
                    </div>
                    <div class="breakdown-box" id="breakdownDiterima"></div>
                </div>

                {{-- Ditolak --}}
                <div id="hasilDitolak" style="display:none;">
                    <div class="text-center">
                        <div style="font-size:4rem;">❌</div>
                        <h4 class="fw-bold text-danger">Kunjungan Ditolak!</h4>
                        <p id="pesanDitolak" class="text-muted"></p>
                    </div>
                    <div class="breakdown-box" id="breakdownDitolak"></div>
                </div>

                {{-- Error --}}
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

/**
 * Buat HTML breakdown Lampiran 3
 */
function buatBreakdown(data) {
    const simbol    = data.status === 'diterima' ? '≤' : '>';
    const kelasResult = data.status === 'diterima' ? 'result-diterima' : 'result-ditolak';
    const ikonResult  = data.status === 'diterima' ? '✓ DITERIMA' : '✗ DITOLAK';

    return `
        <div class="diagram-arrow">
            <div class="diagram-node">📍 TOKO</div>
            <div class="diagram-line"></div>
            <span style="font-size:1rem;">📏</span>
            <div class="diagram-line"></div>
            <div class="diagram-node">🧑 SALES</div>
        </div>
        <div class="divider"></div>
        <div class="line">jarak_aktual &nbsp;&nbsp;&nbsp;= <span class="highlight">${data.jarak} meter</span></div>
        <div class="divider"></div>
        <div class="line">threshold &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;= <span class="highlight">${data.threshold_admin} m</span> <span class="text-muted">(ditentukan admin)</span></div>
        <div class="line">acc GPS toko &nbsp;&nbsp;= <span class="highlight">${data.acc_toko} m</span></div>
        <div class="line">acc GPS sales &nbsp;= <span class="highlight">${data.acc_sales} m</span></div>
        <div class="divider"></div>
        <div class="line">
            threshold_efektif = ${data.threshold_admin} + ${data.acc_toko} + ${data.acc_sales}
            = <span class="highlight">${data.threshold} m</span>
        </div>
        <div class="divider"></div>
        <div class="line ${kelasResult}">
            ${data.jarak} m &nbsp;${simbol}&nbsp; ${data.threshold} m &nbsp;→&nbsp; ${ikonResult}
        </div>
    `;
}

function tampilHasil(status, data) {
    stepGps.style.display        = 'none';
    hasilKunjungan.style.display = 'block';

    if (status === 'diterima') {
        hasilDiterima.style.display = 'block';
        document.getElementById('pesanDiterima').textContent    = data.message;
        document.getElementById('namaTokoDiterima').textContent = data.nama_toko;
        document.getElementById('breakdownDiterima').innerHTML  = buatBreakdown(data);

    } else if (status === 'ditolak') {
        hasilDitolak.style.display = 'block';
        document.getElementById('pesanDitolak').textContent  = data.message;
        document.getElementById('breakdownDitolak').innerHTML = buatBreakdown(data);

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
            tampilHasil('error', {
                message: 'Gagal ambil GPS: ' + err.message + '. Pastikan izin lokasi diaktifkan.'
            });
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

            stepScan.style.display   = 'none';
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