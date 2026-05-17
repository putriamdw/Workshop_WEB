<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Papan Antrian — RS Digital</title>
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            background: #0a1628;
            color: #fff;
            font-family: 'Segoe UI', sans-serif;
            min-height: 100vh;
            overflow: hidden;
        }
        .header-bar {
            background: linear-gradient(90deg, #0d2e6e, #1565c0);
            padding: 14px 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 20px rgba(0,0,0,0.4);
            height: 60px;
        }
        .brand { font-size: 1.3rem; font-weight: 700; }
        .jam-digital {
            font-size: 1.5rem;
            font-weight: 700;
            color: #90caf9;
            font-variant-numeric: tabular-nums;
        }
        .main-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            height: calc(100vh - 60px - 34px);
        }
        .panel-kiri {
            background: linear-gradient(160deg, #0d2e6e 0%, #0a1628 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            border-right: 1px solid rgba(255,255,255,0.07);
            position: relative;
        }
        .label-panggil {
            text-transform: uppercase;
            letter-spacing: 3px;
            font-size: .8rem;
            color: #90caf9;
            margin-bottom: 1rem;
        }
        .nomor-panggil {
            font-size: clamp(6rem, 14vw, 11rem);
            font-weight: 900;
            line-height: 1;
            color: #ffd54f;
            text-shadow: 0 0 60px rgba(255,213,79,0.35);
            transition: all .4s ease;
        }
        .nama-panggil {
            font-size: clamp(1.2rem, 2.8vw, 2rem);
            font-weight: 700;
            margin-top: .5rem;
            text-align: center;
        }
        .poli-panggil {
            font-size: clamp(.85rem, 1.8vw, 1.2rem);
            color: #90caf9;
            margin-top: .25rem;
            text-align: center;
        }
        .btn-menuju {
            margin-top: 1.5rem;
            background: rgba(255,213,79,0.12);
            border: 2px solid #ffd54f;
            color: #ffd54f;
            border-radius: 30px;
            padding: 10px 28px;
            font-size: .95rem;
            font-weight: 600;
            cursor: default;
        }
        .idle-text {
            color: rgba(255,255,255,0.2);
            font-size: 1rem;
            margin-top: 1rem;
        }
        @keyframes flashGlow {
            0%,100% { box-shadow: none; }
            50%      { box-shadow: 0 0 80px rgba(255,213,79,0.45); }
        }
        .panel-kiri.flashing { animation: flashGlow .7s ease 4; }
        .panel-kanan {
            background: #0a1e3d;
            display: flex;
            flex-direction: column;
            padding: 1.5rem;
            overflow: hidden;
        }
        .panel-kanan-title {
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: .75rem;
            color: #90caf9;
            padding-bottom: .75rem;
            border-bottom: 1px solid rgba(255,255,255,0.07);
            margin-bottom: 1rem;
        }
        .antrian-scroll {
            flex: 1;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .antrian-item {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 12px;
            padding: 14px 18px;
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .item-nomor { font-size: 1.6rem; font-weight: 900; color: #ffd54f; min-width: 3.2rem; }
        .item-nama  { font-weight: 600; font-size: 1rem; }
        .item-poli  { font-size: .78rem; color: #90caf9; margin-top: 2px; }
        .kosong-msg { text-align: center; color: rgba(255,255,255,0.2); padding: 3rem 0; }
        .footer-bar {
            background: #071424;
            text-align: center;
            padding: 8px;
            font-size: .72rem;
            color: rgba(255,255,255,0.22);
            border-top: 1px solid rgba(255,255,255,0.05);
            height: 34px;
        }
        #overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.88);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            backdrop-filter: blur(8px);
        }
        #overlay p {
            color: rgba(255,255,255,0.8);
            font-size: 1.05rem;
            margin-bottom: 1.5rem;
            text-align: center;
            max-width: 340px;
            line-height: 1.6;
        }
        #btnAktivasi {
            background: linear-gradient(135deg, #1565c0, #0d6efd);
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 14px 44px;
            font-size: 1.2rem;
            font-weight: 700;
            cursor: pointer;
        }
        #btnAktivasi:hover { opacity: .88; }
        .antrian-scroll::-webkit-scrollbar { width: 4px; }
        .antrian-scroll::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.14);
            border-radius: 4px;
        }
    </style>
</head>
<body>

{{-- Overlay aktivasi suara --}}
<div id="overlay">
    <div style="font-size:3.5rem; margin-bottom:1rem;">🏥</div>
    <p>
        <strong>RS Digital — Papan Antrian</strong><br>
        Klik tombol di bawah untuk mengaktifkan layar dan suara panggilan otomatis.
    </p>
    <button id="btnAktivasi">▶&nbsp; Aktifkan Papan Antrian</button>
</div>

<div class="header-bar">
    <div class="brand">🏥 RS Digital &mdash; Papan Antrian</div>
    <div class="jam-digital" id="jamTampil">00:00:00</div>
</div>

<div class="main-grid">
    <div class="panel-kiri" id="panelKiri">
        <div class="label-panggil">Nomor Dipanggil</div>
        <div class="nomor-panggil" id="nomorPanggil">—</div>
        <div class="nama-panggil"  id="namaPanggil">&nbsp;</div>
        <div class="poli-panggil"  id="poliPanggil">&nbsp;</div>
        <div id="btnMenuju" class="btn-menuju d-none">🔔 Silakan Menuju Poli</div>
        <div class="idle-text" id="idleText">Menunggu panggilan...</div>
    </div>

    <div class="panel-kanan">
        <div class="panel-kanan-title">Antrian Menunggu</div>
        <div class="antrian-scroll" id="antrianList">
            <div class="kosong-msg">Memuat data...</div>
        </div>
    </div>
</div>

<div class="footer-bar">
    RS Digital — Antrian Digital Terpadu &copy; {{ date('Y') }}
</div>

<audio src="{{ asset('assets/sounds/dingdong.mp3') }}" id="audio"></audio>

<script>
// ── Jam digital ───────────────────────────────────────────────────────────
(function () {
    const pad = n => String(n).padStart(2, '0');
    const el  = document.getElementById('jamTampil');
    function update() {
        const now = new Date();
        el.textContent =
            `${pad(now.getHours())}:${pad(now.getMinutes())}:${pad(now.getSeconds())}`;
    }
    update();
    setInterval(update, 1000);
})();

// ── State ─────────────────────────────────────────────────────────────────
let lastNomor  = sessionStorage.getItem('papan_lastNomor') || null;
let audioReady = false;

// ── Overlay aktivasi suara ────────────────────────────────────────────────
document.getElementById('btnAktivasi').addEventListener('click', function () {
    audioReady = true;
    document.getElementById('overlay').style.display = 'none';

    if ('speechSynthesis' in window) {
        const warm = new SpeechSynthesisUtterance(' ');
        warm.volume = 0;
        window.speechSynthesis.speak(warm);
    }
});

// ── Fungsi suara ───────────────────────
function playSound(nomor, nama, poli, ruangan = '', loket = '') {
    if (!('speechSynthesis' in window)) {
        console.warn('Browser tidak mendukung Web Speech API');
        return;
    }

    window.speechSynthesis.cancel();

    const audio = document.getElementById('audio');

    const pesan = new SpeechSynthesisUtterance(
        `Nomor antrian ${nomor}. ${nama}, silakan menuju ke Poli ${poli}`
        + (ruangan ? `, ${ruangan}` : '')
        + (loket   ? `, ${loket}`   : '')
        + '.'
    );
    pesan.lang   = 'id-ID';
    pesan.rate   = 0.85;
    pesan.pitch  = 1.0;
    pesan.volume = 1.0;

    audio.currentTime = 0;
    audio.play();

    audio.onended = function() {
        window.speechSynthesis.speak(pesan);
    };
}

// ── SSE ───────────────────────────────────────────────────────────────────
const source = new EventSource('/sse/antrian');

source.addEventListener('queue-update', function(event) {
    const data = JSON.parse(event.data);
    updatePanelKiri(data.dipanggil);
    updatePanelKanan(data.list || []);
});

source.onerror = function(error) {
    console.error('SSE error:', error);
};

// ── Update panel kiri ─────────────────────────────────────────────────────
function updatePanelKiri(item) {
    const elNomor  = document.getElementById('nomorPanggil');
    const elNama   = document.getElementById('namaPanggil');
    const elPoli   = document.getElementById('poliPanggil');
    const elMenuju = document.getElementById('btnMenuju');
    const elIdle   = document.getElementById('idleText');
    const panel    = document.getElementById('panelKiri');

    if (!item) {
        elNomor.textContent  = '—';
        elNama.innerHTML     = '&nbsp;';
        elPoli.innerHTML     = '&nbsp;';
        elMenuju.classList.add('d-none');
        elIdle.style.display = 'block';
        return;
    }

    elNomor.textContent = item.nomor;
    elNama.textContent  = item.nama;

    // Tampilkan poli + ruangan + loket jika ada
    elPoli.textContent  = `Poli ${item.poli}`
        + (item.ruangan ? ` — ${item.ruangan}` : '')
        + (item.loket   ? ` | ${item.loket}`   : '');

    elMenuju.textContent = `🔔 Silakan Menuju Poli ${item.poli}`
        + (item.ruangan ? ` — ${item.ruangan}` : '');

    elMenuju.classList.remove('d-none');
    elIdle.style.display = 'none';

    // Suara & flash hanya jika nomor berubah
    if (item.nomor !== lastNomor) {
        lastNomor = item.nomor;
        sessionStorage.setItem('papan_lastNomor', lastNomor);

        if (audioReady) {
            playSound(item.nomor, item.nama, item.poli, item.ruangan || '', item.loket || '');
        }

        panel.classList.add('flashing');
        setTimeout(() => panel.classList.remove('flashing'), 3200);
    }
}

// ── Update panel kanan ────────────────────────────────────────────────────
function updatePanelKanan(list) {
    const container = document.getElementById('antrianList');
    const menunggu  = list.filter(a => a.status === 'menunggu');

    if (!menunggu.length) {
        container.innerHTML = '<div class="kosong-msg">Tidak ada antrian yang menunggu.</div>';
        return;
    }

    container.innerHTML = menunggu.map(item => `
        <div class="antrian-item">
            <div class="item-nomor">${esc(item.nomor)}</div>
            <div>
                <div class="item-nama">${esc(item.nama)}</div>
                <div class="item-poli">Poli ${esc(item.poli)}</div>
            </div>
        </div>
    `).join('');
}

// ── XSS escape ────────────────────────────────────────────────────────────
function esc(s) {
    return String(s)
        .replace(/&/g,'&amp;').replace(/</g,'&lt;')
        .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
</script>
</body>
</html>