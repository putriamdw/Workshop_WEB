@extends('layouts.main')
@section('title', 'Scan QR Code — Admin')
@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-dark text-white text-center">
                <h5 class="mb-0">Scanner Pesanan Kantin</h5>
            </div>
            <div class="card-body">
                <!-- Area Kamera -->
                <div id="reader" style="width: 100%;"></div>

                <div id="hasilScan" class="mt-4" style="display:none;">
                    <div class="alert alert-success">
                        <h6>✅ Pesanan Berhasil Dibaca</h6>
                        <hr>
                        <p class="mb-1"><strong>ID:</strong> <span id="resId"></span></p>
                        <p class="mb-1"><strong>Pembeli:</strong> <span id="resPembeli"></span></p>
                        <div id="resStatus" class="my-2"></div>
                        
                        <table class="table table-sm mt-2 bg-white rounded">
                            <thead><tr><th>Menu</th><th>Qty</th><th class="text-end">Total</th></tr></thead>
                            <tbody id="resMenu"></tbody>
                        </table>
                        <button onclick="location.reload()" class="btn btn-success w-100 mt-2">Scan QR Lain</button>
                    </div>
                </div>

                <audio id="beep" src="{{ asset('assets/sounds/beep.mp3') }}" preload="auto"></audio>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    const beep = document.getElementById('beep');

    function onScanSuccess(decodedText, decodedResult) {
        beep.play().catch(() => {});
        
        // Ekstrak ID Pesanan
        let idPesanan = decodedText;
        const match = decodedText.match(/\/pesan\/sukses\/([^\/\?#]+)/);
        if (match) idPesanan = match[1];

        html5QrcodeScanner.clear().then(() => {
            fetch(`{{ url('/admin/scan-qr/cari') }}/${idPesanan}`)
                .then(res => res.json())
                .then(data => {
                    if (data.found) {
                        document.getElementById('resId').textContent = data.id_pesanan;
                        document.getElementById('resPembeli').textContent = data.nama_pembeli;
                        document.getElementById('resStatus').innerHTML = data.status_bayar === 'lunas' 
                            ? '<span class="badge bg-success">LUNAS</span>' 
                            : '<span class="badge bg-warning text-dark">BELUM BAYAR</span>';

                        let menuHtml = '';
                        data.details.forEach(d => {
                            menuHtml += `<tr><td>${d.nama_menu}</td><td>${d.jumlah}</td><td class="text-end">${d.subtotal}</td></tr>`;
                        });
                        document.getElementById('resMenu').innerHTML = menuHtml;
                        document.getElementById('hasilScan').style.display = 'block';
                    } else {
                        alert("Data tidak ditemukan!");
                        location.reload();
                    }
                });
        });
    }

    let html5QrcodeScanner = new Html5QrcodeScanner(
        "reader", 
        { fps: 10, qrbox: { width: 250, height: 250 } }, 
        false
    );
    html5QrcodeScanner.render(onScanSuccess);
</script>
@endsection