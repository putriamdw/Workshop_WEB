@extends('layouts.main')
@section('title', 'Barcode Scanner')
@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Scan Barcode Barang</h5>
            </div>
            <div class="card-body">
                <!-- Area Kamera -->
                <div id="reader" style="width: 100%;"></div>
                
                <!-- Detail Barang (Muncul setelah scan) -->
                <div id="hasilScan" class="mt-4 p-3 border rounded bg-light" style="display:none;">
                    <h6>📦 Detail Barang:</h6>
                    <table class="table table-sm mb-0">
                        <tr><td>ID Barang</td><td id="resIdBarang" class="fw-bold"></td></tr>
                        <tr><td>Nama</td><td id="resNama"></td></tr>
                        <tr><td>Harga</td><td id="resHarga" class="text-success fw-bold"></td></tr>
                    </table>
                    <button onclick="lokasiReload()" class="btn btn-outline-primary btn-sm mt-3 w-100">Scan Lagi</button>
                </div>

                <div id="tidakDitemukan" class="alert alert-warning mt-4" style="display:none;">
                    Data barcode <strong id="kodeTidakDitemukan"></strong> tidak ada.
                    <button onclick="lokasiReload()" class="btn btn-sm btn-link">Coba lagi</button>
                </div>
                
                <audio id="beep" src="{{ asset('assets/sounds/beep.mp3') }}" preload="auto"></audio>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    const beep = document.getElementById('beep');

    function lokasiReload() {
        location.reload();
    }

    function onScanSuccess(decodedText, decodedResult) {
        // Bunyi beep
        beep.play().catch(() => {});
        
        // Matikan scanner agar tidak scan terus menerus
        html5QrcodeScanner.clear().then(() => {
            // Proses cari data ke server
            fetch(`{{ url('/barcode/cari') }}/${decodedText}`)
                .then(res => res.json())
                .then(data => {
                    if (data.found) {
                        document.getElementById('resIdBarang').textContent = data.id_barang;
                        document.getElementById('resNama').textContent = data.nama;
                        document.getElementById('resHarga').textContent = data.harga_format;
                        document.getElementById('hasilScan').style.display = 'block';
                    } else {
                        document.getElementById('kodeTidakDitemukan').textContent = decodedText;
                        document.getElementById('tidakDitemukan').style.display = 'block';
                    }
                })
                .catch(err => alert("Gagal ambil data: " + err));
        });
    }

    // Inisialisasi scanner otomatis
    let html5QrcodeScanner = new Html5QrcodeScanner(
        "reader", 
        { 
            fps: 10, 
            qrbox: { width: 400, height: 200 }, // Box panjang untuk barcode
            rememberLastUsedCamera: true,
            showTorchButtonIfSupported: true
        },
        false
    );
    html5QrcodeScanner.render(onScanSuccess);
</script>
@endsection