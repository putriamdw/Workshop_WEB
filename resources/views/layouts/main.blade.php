<!DOCTYPE html>
<html lang="en">
<head>
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

    <meta charset="UTF-8">
    @include('layouts.header')

    {{-- Style Page --}}
    @yield('style-page')

    <style>
    @media (max-width: 991px) {
        .sidebar-offcanvas {
            position: fixed !important;
            top: 63px !important;
            left: -260px !important;
            width: 255px !important;
            height: calc(100vh - 63px) !important;
            z-index: 9999 !important;
            overflow-y: auto !important;
            transition: left 0.3s ease !important;
            background: #1c1c2e;
        }
        .sidebar-offcanvas.active {
            left: 0 !important;
        }
        /* Overlay gelap di belakang sidebar */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 63px;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 9998;
        }
        .sidebar-overlay.active {
            display: block;
        }
    }
    </style>
</head>

<body>
<div class="container-scroller">

    {{-- Overlay untuk menutup sidebar saat klik di luar --}}
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    {{-- Navbar --}}
    @include('layouts.navbar')

    <div class="container-fluid page-body-wrapper">

        {{-- Sidebar --}}
        @include('layouts.sidebar')

        {{-- Content --}}
        <div class="main-panel">
            <div class="content-wrapper">
                @yield('content')
            </div>

            {{-- Footer --}}
            @include('layouts.footer')

            {{-- Scripts --}}
            @include('layouts.script')

            <script>
            (function($) {
                $(function() {

                    // ── Fix sidebar active (override misc.js) ──────────────
                    var sidebar = $('.sidebar');
                    sidebar.find('.nav-item').removeClass('active');
                    sidebar.find('.nav-link').removeClass('active');
                    sidebar.find('.collapse').removeClass('show');

                    var fullPath = location.pathname;
                    $('.nav li a', sidebar).each(function() {
                        var $this = $(this);
                        var href  = $this.attr('href');
                        if (href && href !== '#') {
                            try {
                                var linkPath = new URL(href, location.origin).pathname;
                                if (fullPath === linkPath) {
                                    $this.parents('.nav-item').last().addClass('active');
                                    if ($this.parents('.sub-menu').length) {
                                        $this.closest('.collapse').addClass('show');
                                        $this.addClass('active');
                                    }
                                }
                            } catch(e) {}
                        }
                    });

                    // ── Toggle sidebar mobile ──────────────────────────────
                    var $sidebar  = $('.sidebar-offcanvas');
                    var $overlay  = $('#sidebarOverlay');
                    var $toggleBtn = $('.navbar-toggler-right');

                    $toggleBtn.on('click', function(e) {
                        e.stopPropagation();
                        $sidebar.toggleClass('active');
                        $overlay.toggleClass('active');
                    });

                    // Tutup sidebar kalau klik overlay
                    $overlay.on('click', function() {
                        $sidebar.removeClass('active');
                        $overlay.removeClass('active');
                    });

                    // Tutup sidebar kalau klik link di dalam sidebar (UX mobile)
                    $sidebar.find('a:not([data-bs-toggle])').on('click', function() {
                        if ($(window).width() < 992) {
                            $sidebar.removeClass('active');
                            $overlay.removeClass('active');
                        }
                    });

                });
            })(jQuery);
            </script>

            @yield('script-page')

        </div>
    </div>
</div>
</body>
</html>