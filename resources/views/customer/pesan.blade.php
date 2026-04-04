@extends('layouts.main')
@section('title', 'Pesan — ' . $vendor->nama_kantin)
@section('content')
<div class="container py-4">
    <div class="row g-4">
        {{-- Daftar Menu --}}
        <div class="col-md-8">
            <h4>Menu — {{ $vendor->nama_kantin }}</h4>
            <div class="row g-3">
                @foreach($menus as $menu)
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        @if($menu->foto)
                            <img src="{{ Storage::url($menu->foto) }}"
                                 class="card-img-top" style="height:140px;object-fit:cover">
                        @endif
                        <div class="card-body d-flex flex-column">
                            <h6 class="fw-bold">{{ $menu->nama_menu }}</h6>
                            <p class="text-muted small flex-grow-1">{{ $menu->deskripsi }}</p>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <span class="text-success fw-bold">{{ $menu->harga_format }}</span>
                                <button class="btn btn-sm btn-primary btn-tambah"
                                    data-id="{{ $menu->id_menu }}"
                                    data-nama="{{ $menu->nama_menu }}"
                                    data-harga="{{ $menu->harga }}">+ Tambah</button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Keranjang --}}
        <div class="col-md-4">
            <div class="card shadow-sm border-0 sticky-top" style="top:80px">
                <div class="card-header fw-bold bg-primary text-white">🛒 Pesananmu</div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush" id="keranjang-list">
                        <li class="list-group-item text-center text-muted py-4" id="keranjang-empty">
                            Belum ada item
                        </li>
                    </ul>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between fw-bold mb-3">
                        <span>Total</span>
                        <span id="total-harga">Rp 0</span>
                    </div>
                    <form action="{{ route('customer.store') }}" method="POST" id="form-pesan">
                        @csrf
                        <input type="hidden" name="id_vendor" value="{{ $vendor->id_vendor }}">
                        <div id="items-input"></div>
                        <button type="submit" class="btn btn-success w-100"
                                id="btn-pesan" disabled>Lanjut ke Pembayaran</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const keranjang = {};

function formatRupiah(n) {
    return 'Rp ' + n.toLocaleString('id-ID');
}

function renderKeranjang() {
    const list   = document.getElementById('keranjang-list');
    const empty  = document.getElementById('keranjang-empty');
    const input  = document.getElementById('items-input');
    const btn    = document.getElementById('btn-pesan');
    const total  = document.getElementById('total-harga');
    const items  = Object.entries(keranjang).filter(([,v]) => v.jumlah > 0);

    list.innerHTML = '';
    input.innerHTML = '';

    if (items.length === 0) {
        list.appendChild(empty);
        btn.disabled = true;
        total.textContent = formatRupiah(0);
        return;
    }

    let sum = 0;
    items.forEach(([id, item], i) => {
        sum += item.harga * item.jumlah;
        const li = document.createElement('li');
        li.className = 'list-group-item';
        li.innerHTML = `
            <div class="d-flex justify-content-between align-items-center">
                <span>${item.nama}</span>
                <div class="d-flex align-items-center gap-1">
                    <button class="btn btn-sm btn-outline-secondary py-0 px-1"
                        onclick="kurang('${id}')">-</button>
                    <span>${item.jumlah}</span>
                    <button class="btn btn-sm btn-outline-secondary py-0 px-1"
                        onclick="tambah('${id}','${item.nama}',${item.harga})">+</button>
                </div>
            </div>
            <small class="text-muted">${formatRupiah(item.harga * item.jumlah)}</small>
        `;
        list.appendChild(li);
        input.innerHTML += `
            <input type="hidden" name="items[${i}][id_menu]" value="${id}">
            <input type="hidden" name="items[${i}][jumlah]" value="${item.jumlah}">
        `;
    });

    total.textContent = formatRupiah(sum);
    btn.disabled = false;
}

function tambah(id, nama, harga) {
    if (!keranjang[id]) keranjang[id] = { nama, harga, jumlah: 0 };
    keranjang[id].jumlah++;
    renderKeranjang();
}

function kurang(id) {
    if (!keranjang[id]) return;
    keranjang[id].jumlah = Math.max(0, keranjang[id].jumlah - 1);
    renderKeranjang();
}

document.querySelectorAll('.btn-tambah').forEach(btn => {
    btn.addEventListener('click', () => {
        tambah(btn.dataset.id, btn.dataset.nama, parseFloat(btn.dataset.harga));
    });
});
</script>
@endsection