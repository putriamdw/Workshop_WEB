<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">

    {{-- Profile --}}
    <li class="nav-item nav-profile">
        <a href="#" class="nav-link">
            <div class="nav-profile-image">
                <img src="{{ asset('assets/images/faces/face1.jpg') }}" alt="profile" />
                <span class="login-status online"></span>
                <!--change to offline or busy as needed-->
            </div>
            <div class="nav-profile-text d-flex flex-column">
                <span class="font-weight-bold mb-2">{{ Auth::user()->name }}</span>
                <span class="text-secondary text-small">{{ Auth::user()->role }}</span>
            </div>
            <i class="mdi mdi-bookmark-check text-success nav-profile-badge"></i>
        </a>
    </li>

    {{-- Dashboard (semua user bisa lihat) --}}
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

    {{-- Menu Sertifikat --}}
<li class="nav-item {{ request()->routeIs('pdf.sertifikat') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('pdf.sertifikat') }}">
        <span class="menu-title">Sertifikat</span>
        <i class="mdi mdi-file-pdf-box menu-icon"></i>
    </a>
</li>

{{-- Menu Undangan --}}
<li class="nav-item {{ request()->routeIs('pdf.undangan') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('pdf.undangan') }}">
        <span class="menu-title">Undangan</span>
        <i class="mdi mdi-email menu-icon"></i>
    </a>
</li>

{{-- JS Tugas (Dropdown) --}}
<li class="nav-item {{ request()->routeIs('jstugas.*') ? 'active' : '' }}">
    <a class="nav-link" data-bs-toggle="collapse" href="#jsTugasMenu" 
       aria-expanded="{{ request()->routeIs('jstugas.*') ? 'true' : 'false' }}">
        <span class="menu-title">JS & jQuery</span>
        <i class="menu-arrow"></i>
        <i class="mdi mdi-language-javascript menu-icon"></i>
    </a>
    <div class="collapse {{ request()->routeIs('jstugas.*') ? 'show' : '' }}" id="jsTugasMenu">
        <ul class="nav flex-column sub-menu">
            <li class="nav-item {{ request()->routeIs('jstugas.tugas1') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('jstugas.tugas1') }}">
                    <span class="menu-title">JS 1 - Spinner</span>
                    <i class="mdi mdi-loading menu-icon"></i>
                </a>
            </li>
            <li class="nav-item {{ request()->routeIs('jstugas.tugas2_3') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('jstugas.tugas2_3') }}">
                    <span class="menu-title">JS 2 & 3 - Tabel & CRUD</span>
                    <i class="mdi mdi-table-edit menu-icon"></i>
                </a>
            </li>
            <li class="nav-item {{ request()->routeIs('jstugas.tugas4') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('jstugas.tugas4') }}">
                    <span class="menu-title">JS 4 - Select</span>
                    <i class="mdi mdi-form-select menu-icon"></i>
                </a>
            </li>
        </ul>
    </div>
</li>

    {{-- Hanya admin --}}
    @if(auth()->user()->role == 'admin')

        {{-- Menu Kategori --}}
        <li class="nav-item {{ request()->routeIs('kategori.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('kategori.index') }}">
                <span class="menu-title">Kategori</span>
                <i class="mdi mdi-format-list-bulleted menu-icon"></i>
            </a>
        </li>

        {{-- Menu Buku --}}
        <li class="nav-item {{ request()->routeIs('buku.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('buku.index') }}">
                <span class="menu-title">Buku</span>
                <i class="mdi mdi-book-open-page-variant menu-icon"></i>
            </a>
        </li>
    @endif
    </ul>
</nav>

   
