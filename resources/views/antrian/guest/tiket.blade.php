<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tiket Antrian #{{ $antrian->nomor }} — RS Digital</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            min-height: 100vh;
            background: #f0f9f6;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            font-family: 'Segoe UI', sans-serif;
        }
        .tiket-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 8px 40px rgba(10,110,92,0.12);
            max-width: 400px;
            width: 100%;
            overflow: hidden;
        }
        .hdr {
            background: #0a6e5c;
            padding: 1.75rem 2rem 2.25rem;
            text-align: center;
            position: relative;
        }
        .wave {
            position: absolute;
            bottom: 0; left: 0; right: 0;
            height: 28px;
            background: #fff;
            border-radius: 28px 28px 0 0;
        }
        .label-kecil {
            font-size: .7rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: rgba(255,255,255,0.6);
            margin-bottom: .4rem;
        }
        .nomor {
            font-size: 5rem;
            font-weight: 700;
            color: #fff;
            line-height: 1;
            letter-spacing: -2px;
        }
        .poli-badge {
            display: inline-block;
            background: rgba(255,255,255,0.15);
            color: rgba(255,255,255,0.9);
            border: 1px solid rgba(255,255,255,0.25);
            border-radius: 20px;
            padding: 4px 14px;
            font-size: .8rem;
            margin-top: .75rem;
        }
        .body { padding: 1.25rem 1.75rem .75rem; }
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f3f4f6;
        }
        .info-row:last-child { border-bottom: none; }
        .info-label { font-size: .83rem; color: #6b7280; }
        .info-value { font-size: .9rem; font-weight: 600; color: #111827; }
        .status-badge {
            background: #e8f7f4;
            color: #0a6e5c;
            border: 1px solid #9fd9cc;
            border-radius: 20px;
            padding: 4px 14px;
            font-size: .8rem;
            font-weight: 600;
        }
        .divider {
            height: 1px;
            background: repeating-linear-gradient(90deg, #e5e7eb 0, #e5e7eb 6px, transparent 6px, transparent 12px);
            margin: 0 1.75rem;
        }
        .posisi-box {
            margin: 1rem 1.75rem;
            background: #e8f7f4;
            border: 1px solid #9fd9cc;
            border-radius: 12px;
            padding: 1.1rem;
            text-align: center;
        }
        .posisi-angka { font-size: 2.2rem; font-weight: 700; color: #0a6e5c; line-height: 1; }
        .posisi-label { font-size: .78rem; color: #084f3f; margin-top: .2rem; }
        .estimasi { font-size: .78rem; color: #b45309; font-weight: 600; margin-top: .4rem; }
        .footer {
            background: #f9fafb;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            padding: 1rem 1.75rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: .6rem;
        }
        .btn-back {
            background: #0a6e5c;
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 9px 24px;
            font-size: .85rem;
            font-weight: 600;
            cursor: pointer;
            transition: background .2s;
        }
        .btn-back:hover { background: #085c4d; }
        .footer-text { font-size: .74rem; color: #9ca3af; }
    </style>
</head>
<body>
<div class="tiket-card">
    <div class="hdr">
        <div class="label-kecil">Nomor Antrian Anda</div>
        <div class="nomor">{{ $antrian->nomor }}</div>
        <div class="poli-badge">🏥 Poli {{ $antrian->poli }}</div>
        <div class="wave"></div>
    </div>
    <div class="body">
        <div class="info-row">
            <span class="info-label">Nama</span>
            <span class="info-value">{{ $antrian->nama }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Layanan</span>
            <span class="info-value">Poli {{ $antrian->poli }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Jam Daftar</span>
            <span class="info-value">{{ $antrian->jam_daftar->format('H:i') }} WIB</span>
        </div>
        <div class="info-row">
            <span class="info-label">Status</span>
            <span id="statusBox" class="status-badge">{{ ucfirst($antrian->status) }}</span>
        </div>
    </div>
    <div class="divider"></div>
    <div class="posisi-box" id="posisiBox">
        <div class="posisi-label">Memuat posisi antrian...</div>
    </div>
    <div class="footer">
        <button class="btn-back" onclick="window.location.href='{{ route('antrian.guest') }}'">
            ← Daftar Antrian Lain
        </button>
        <span class="footer-text">RS Digital © {{ date('Y') }}</span>
    </div>
</div>
<script>
    const antrianId = {{ $antrian->id }};

    setTimeout(function() {
        const source = new EventSource('/sse/antrian');

        source.addEventListener('queue-update', function(event) {
            const data = JSON.parse(event.data);
            const list = data.list || [];
            const me   = list.find(a => a.id == antrianId);
            if (!me) return;

            const statusBox = document.getElementById('statusBox');
            const statusMap = {
                menunggu:  { label: 'Menunggu',  bg: '#e8f7f4', color: '#0a6e5c', border: '#9fd9cc' },
                dipanggil: { label: 'Dipanggil', bg: '#fef9c3', color: '#92400e', border: '#fde68a' },
                selesai:   { label: 'Selesai',   bg: '#f3f4f6', color: '#374151', border: '#d1d5db' },
                terlambat: { label: 'Terlambat', bg: '#fef2f2', color: '#dc2626', border: '#fecaca' },
            };
            const s = statusMap[me.status] || { label: me.status, bg: '#f3f4f6', color: '#374151', border: '#d1d5db' };
            statusBox.textContent = s.label;
            statusBox.style.background  = s.bg;
            statusBox.style.color       = s.color;
            statusBox.style.borderColor = s.border;

            const menunggu  = list.filter(a => a.status === 'menunggu');
            const posisku   = menunggu.findIndex(a => a.id == antrianId) + 1;
            const posisiBox = document.getElementById('posisiBox');

            if (me.status === 'dipanggil') {
                posisiBox.style.background  = '#f0fdf4';
                posisiBox.style.borderColor = '#86efac';
                posisiBox.innerHTML = `
                    <div style="font-size:1.6rem;margin-bottom:.4rem;">🔔</div>
                    <div style="font-weight:700;color:#15803d;font-size:.95rem;">Nomor Anda Sedang Dipanggil!</div>
                    <div style="font-size:.8rem;color:#166534;margin-top:.3rem;">
                        Silakan menuju Poli ${me.poli}${me.ruangan ? ' — ' + me.ruangan : ''}
                    </div>`;
            } else if (me.status === 'selesai') {
                posisiBox.style.background  = '#f9fafb';
                posisiBox.style.borderColor = '#e5e7eb';
                posisiBox.innerHTML = `
                    <div style="font-size:1.6rem;margin-bottom:.4rem;">✅</div>
                    <div style="color:#374151;font-size:.88rem;">Antrian Anda telah selesai.</div>`;
            } else if (me.status === 'terlambat') {
                posisiBox.style.background  = '#fef2f2';
                posisiBox.style.borderColor = '#fecaca';
                posisiBox.innerHTML = `
                    <div style="font-size:1.6rem;margin-bottom:.4rem;">⏰</div>
                    <div style="font-weight:700;color:#dc2626;font-size:.88rem;">Waktu habis / terlewat</div>
                    <div style="font-size:.78rem;color:#991b1b;margin-top:.2rem;">Silakan hubungi petugas.</div>`;
            } else if (posisku > 0) {
                const estimasiMenit = (posisku - 1) * 5;
                const estimasiText  = estimasiMenit === 0 ? '🎯 Anda berikutnya!' : `⏱ Estimasi tunggu: ± ${estimasiMenit} menit`;
                posisiBox.style.background  = '#e8f7f4';
                posisiBox.style.borderColor = '#9fd9cc';
                posisiBox.innerHTML = `
                    <div class="posisi-label">Posisi antrian Anda</div>
                    <div class="posisi-angka">${posisku}</div>
                    <div class="posisi-label">dari ${menunggu.length} yang menunggu</div>
                    <div class="estimasi">${estimasiText}</div>`;
            }
        });

        source.onerror = function(error) { console.error('SSE error:', error); };
    }, 3000);
</script>
</body>
</html>