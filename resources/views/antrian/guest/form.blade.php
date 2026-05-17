<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Antrian — RS Digital</title>
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
        .card-antrian {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 8px 40px rgba(10,110,92,0.12);
            max-width: 440px;
            width: 100%;
            overflow: hidden;
        }
        .hdr {
            background: #0a6e5c;
            padding: 2rem 2rem 2.5rem;
            text-align: center;
            position: relative;
        }
        .hdr-icon {
            width: 56px;
            height: 56px;
            background: rgba(255,255,255,0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.6rem;
        }
        .hdr h2 { color: #fff; font-size: 1.3rem; font-weight: 600; margin-bottom: .25rem; }
        .hdr p  { color: rgba(255,255,255,0.7); font-size: .85rem; }
        .wave {
            position: absolute;
            bottom: 0; left: 0; right: 0;
            height: 28px;
            background: #fff;
            border-radius: 28px 28px 0 0;
        }
        .body { padding: 1.5rem 1.75rem 1.25rem; }
        .info-box {
            background: #e8f7f4;
            border-left: 3px solid #0a6e5c;
            border-radius: 0 8px 8px 0;
            padding: 10px 14px;
            font-size: .83rem;
            color: #084f3f;
            margin-bottom: 1.5rem;
        }
        .field { margin-bottom: 1.1rem; }
        .field label {
            display: block;
            font-size: .83rem;
            font-weight: 600;
            color: #4b5563;
            margin-bottom: .4rem;
        }
        .field input, .field select {
            width: 100%;
            padding: 11px 14px;
            border: 1.5px solid #d1fae5;
            border-radius: 10px;
            background: #fff;
            color: #111827;
            font-size: .92rem;
            transition: border-color .2s;
            outline: none;
        }
        .field input:focus, .field select:focus { border-color: #0a6e5c; }
        .field input::placeholder { color: #9ca3af; }
        .is-invalid { border-color: #ef4444 !important; }
        .invalid-feedback { font-size: .8rem; color: #ef4444; margin-top: .3rem; }
        .btn-daftar {
            width: 100%;
            padding: 12px;
            background: #0a6e5c;
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: .95rem;
            font-weight: 600;
            cursor: pointer;
            margin-top: .5rem;
            transition: background .2s;
        }
        .btn-daftar:hover { background: #085c4d; }
        .alert-danger {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 10px;
            padding: 10px 14px;
            font-size: .83rem;
            color: #dc2626;
            margin-bottom: 1rem;
        }
        .footer {
            background: #f9fafb;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            padding: .9rem;
            font-size: .75rem;
            color: #9ca3af;
        }
    </style>
</head>
<body>
<div class="card-antrian">
    <div class="hdr">
        <div class="hdr-icon">🏥</div>
        <h2>RS Digital</h2>
        <p>Pendaftaran Antrian Online</p>
        <div class="wave"></div>
    </div>
    <div class="body">
        @if ($errors->any())
            <div class="alert-danger">
                <ul style="margin:0;padding-left:1.2rem;">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="info-box">
            ℹ️ Isi formulir berikut untuk mendapatkan nomor antrian. Nomor antrian akan tampil di halaman baru setelah mendaftar.
        </div>
        <form action="{{ route('antrian.guest.store') }}" method="POST" id="formDaftar">
            @csrf
            <div class="field">
                <label>Nama Lengkap</label>
                <input type="text" name="nama"
                       class="{{ $errors->has('nama') ? 'is-invalid' : '' }}"
                       placeholder="Masukkan nama lengkap Anda"
                       value="{{ old('nama') }}" autofocus>
                @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="field">
                <label>Poli / Layanan yang Dituju</label>
                <select name="poli" class="{{ $errors->has('poli') ? 'is-invalid' : '' }}">
                    <option value="" disabled {{ old('poli') ? '' : 'selected' }}>— Pilih Poli / Layanan —</option>
                    @foreach ($poliList as $key => $label)
                        <option value="{{ $key }}" {{ old('poli') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('poli')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <button type="submit" class="btn-daftar">🎫 Ambil Nomor Antrian</button>
        </form>
    </div>
    <div class="footer">RS Digital — Antrian Digital Terpadu © {{ date('Y') }}</div>
</div>
<script>
    document.getElementById('formDaftar').addEventListener('submit', function () {
        this.target = '_blank';
    });
    window.addEventListener('pageshow', function(e) {
        if (e.persisted) document.getElementById('formDaftar').reset();
    });
</script>
</body>
</html>