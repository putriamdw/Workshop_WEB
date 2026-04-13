<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">

    {{-- Profile --}}
    <li class="nav-item nav-profile">
        <a href="#" class="nav-link">
            <div class="nav-profile-image">
                <img src="{{ asset('assets/images/faces/face1.jpg') }}" alt="profile" />
                <span class="login-status online"></span>
            </div>
            <div class="nav-profile-text d-flex flex-column">
                @auth
                    <span class="font-weight-bold mb-2">{{ Auth::user()->name }}</span>
                    <span class="text-secondary text-small">{{ Auth::user()->role }}</span>
                @else
                    <span class="font-weight-bold mb-2">Guest</span>
                    <span class="text-secondary text-small">customer</span>
                @endauth
            </div>
            <i class="mdi mdi-bookmark-check text-success nav-profile-badge"></i>
        </a>
    </li>

    @auth

    {{-- ADMIN --}}
    @if(Auth::user()->role == 'admin')

        <li class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.dashboard') }}">
                <span class="menu-title">Dashboard Admin</span>
                <i class="mdi mdi-view-dashboard menu-icon"></i>
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('admin.vendor.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.vendor.index') }}">
                <span class="menu-title">Data Vendor</span>
                <i class="mdi mdi-store menu-icon"></i>
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('admin.pesanan.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.pesanan.index') }}">
                <span class="menu-title">Semua Pesanan</span>
                <i class="mdi mdi-receipt menu-icon"></i>
            </a>
        </li>

        <!-- <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('dashboard') }}">
                <span class="menu-title">Dashboard Toko</span>
                <i class="mdi mdi-home menu-icon"></i>
            </a>
        </li> -->

        <li class="nav-item {{ request()->routeIs('kategori.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('kategori.index') }}">
                <span class="menu-title">Kategori</span>
                <i class="mdi mdi-format-list-bulleted menu-icon"></i>
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('buku.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('buku.index') }}">
                <span class="menu-title">Buku</span>
                <i class="mdi mdi-book-open-page-variant menu-icon"></i>
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('barang.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('barang.index') }}">
                <span class="menu-title">Barang</span>
                <i class="mdi mdi-tag menu-icon"></i>
            </a>
        </li>

        {{-- Customer (SC3) — hanya admin --}}
        <li class="nav-item {{ request()->routeIs('customer-data.*') ? 'active' : '' }}">
            <a class="nav-link" data-bs-toggle="collapse" href="#customerMenu"
               aria-expanded="{{ request()->routeIs('customer-data.*') ? 'true' : 'false' }}">
                <span class="menu-title">Customer</span>
                <i class="menu-arrow"></i>
                <i class="mdi mdi-account-group menu-icon"></i>
            </a>
            <div class="collapse {{ request()->routeIs('customer-data.*') ? 'show' : '' }}" id="customerMenu">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('customer-data.index') }}">Data Customer</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('customer-data.tambah1') }}">Tambah Customer 1</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('customer-data.tambah2') }}">Tambah Customer 2</a>
                    </li>
                </ul>
            </div>
        </li>

        <li class="nav-item {{ request()->routeIs('pdf.*') ? 'active' : '' }}">
            <a class="nav-link" data-bs-toggle="collapse" href="#pdfMenu"
               aria-expanded="{{ request()->routeIs('pdf.*') ? 'true' : 'false' }}">
                <span class="menu-title">PDF Generator</span>
                <i class="menu-arrow"></i>
                <i class="mdi mdi-file-pdf-box menu-icon"></i>
            </a>
            <div class="collapse {{ request()->routeIs('pdf.*') ? 'show' : '' }}" id="pdfMenu">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('pdf.sertifikat') }}">Sertifikat</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('pdf.undangan') }}">Undangan</a>
                    </li>
                </ul>
            </div>
        </li>

        <li class="nav-item {{ request()->routeIs('jstugas.*') ? 'active' : '' }}">
            <a class="nav-link" data-bs-toggle="collapse" href="#jsTugasMenu"
               aria-expanded="{{ request()->routeIs('jstugas.*') ? 'true' : 'false' }}">
                <span class="menu-title">JS & jQuery</span>
                <i class="menu-arrow"></i>
                <i class="mdi mdi-language-javascript menu-icon"></i>
            </a>
            <div class="collapse {{ request()->routeIs('jstugas.*') ? 'show' : '' }}" id="jsTugasMenu">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('jstugas.tugas1') }}">JS 1 - Spinner</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('jstugas.tugas2_3') }}">JS 2 & 3 - Tabel & CRUD</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('jstugas.tugas4') }}">JS 4 - Select</a>
                    </li>
                </ul>
            </div>
        </li>

        <li class="nav-item {{ request()->routeIs('wilayah.*') || request()->routeIs('pos.*') ? 'active' : '' }}">
            <a class="nav-link" data-bs-toggle="collapse" href="#ajaxMenu"
               aria-expanded="{{ request()->routeIs('wilayah.*') || request()->routeIs('pos.*') ? 'true' : 'false' }}">
                <span class="menu-title">AJAX & Axios</span>
                <i class="menu-arrow"></i>
                <i class="mdi mdi-web menu-icon"></i>
            </a>
            <div class="collapse {{ request()->routeIs('wilayah.*') || request()->routeIs('pos.*') ? 'show' : '' }}" id="ajaxMenu">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('wilayah.index') }}">Wilayah – jQuery</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('wilayah.axios') }}">Wilayah – Axios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('pos.index') }}">POS – jQuery</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('pos.axios') }}">POS – Axios</a>
                    </li>
                </ul>
            </div>
        </li>

    {{-- VENDOR --}}
    @elseif(Auth::user()->role == 'vendor')

        <li class="nav-item {{ request()->routeIs('vendor.dashboard') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('vendor.dashboard') }}">
                <span class="menu-title">Dashboard</span>
                <i class="mdi mdi-view-dashboard menu-icon"></i>
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('vendor.menu.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('vendor.menu.index') }}">
                <span class="menu-title">Kelola Menu</span>
                <i class="mdi mdi-food menu-icon"></i>
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('vendor.pesanan.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('vendor.pesanan.index') }}">
                <span class="menu-title">Pesanan Lunas</span>
                <i class="mdi mdi-receipt menu-icon"></i>
            </a>
        </li>

    {{-- USER BIASA --}}
    @else

        <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('dashboard') }}">
                <span class="menu-title">Dashboard</span>
                <i class="mdi mdi-home menu-icon"></i>
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('barang.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('barang.index') }}">
                <span class="menu-title">Barang</span>
                <i class="mdi mdi-tag menu-icon"></i>
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('pdf.*') ? 'active' : '' }}">
            <a class="nav-link" data-bs-toggle="collapse" href="#pdfMenu"
               aria-expanded="{{ request()->routeIs('pdf.*') ? 'true' : 'false' }}">
                <span class="menu-title">PDF Generator</span>
                <i class="menu-arrow"></i>
                <i class="mdi mdi-file-pdf-box menu-icon"></i>
            </a>
            <div class="collapse {{ request()->routeIs('pdf.*') ? 'show' : '' }}" id="pdfMenu">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('pdf.sertifikat') }}">Sertifikat</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('pdf.undangan') }}">Undangan</a>
                    </li>
                </ul>
            </div>
        </li>

        <li class="nav-item {{ request()->routeIs('jstugas.*') ? 'active' : '' }}">
            <a class="nav-link" data-bs-toggle="collapse" href="#jsTugasMenu"
               aria-expanded="{{ request()->routeIs('jstugas.*') ? 'true' : 'false' }}">
                <span class="menu-title">JS & jQuery</span>
                <i class="menu-arrow"></i>
                <i class="mdi mdi-language-javascript menu-icon"></i>
            </a>
            <div class="collapse {{ request()->routeIs('jstugas.*') ? 'show' : '' }}" id="jsTugasMenu">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('jstugas.tugas1') }}">JS 1 - Spinner</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('jstugas.tugas2_3') }}">JS 2 & 3 - Tabel & CRUD</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('jstugas.tugas4') }}">JS 4 - Select</a>
                    </li>
                </ul>
            </div>
        </li>

        <li class="nav-item {{ request()->routeIs('wilayah.*') || request()->routeIs('pos.*') ? 'active' : '' }}">
            <a class="nav-link" data-bs-toggle="collapse" href="#ajaxMenu"
               aria-expanded="{{ request()->routeIs('wilayah.*') || request()->routeIs('pos.*') ? 'true' : 'false' }}">
                <span class="menu-title">AJAX & Axios</span>
                <i class="menu-arrow"></i>
                <i class="mdi mdi-web menu-icon"></i>
            </a>
            <div class="collapse {{ request()->routeIs('wilayah.*') || request()->routeIs('pos.*') ? 'show' : '' }}" id="ajaxMenu">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('wilayah.index') }}">Wilayah – jQuery</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('wilayah.axios') }}">Wilayah – Axios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('pos.index') }}">POS – jQuery</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('pos.axios') }}">POS – Axios</a>
                    </li>
                </ul>
            </div>
        </li>

    @endif

    @endauth

    </ul>
</nav>