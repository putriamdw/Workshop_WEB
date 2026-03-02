<!DOCTYPE html>
<html lang="en">
<head>

    <!-- DataTables CSS -->
    <link rel="stylesheet"
            href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

    {{-- Header --}}
    <meta charset="UTF-8">
    @include('layouts.header')

    {{-- Style Global --}}
    <link rel="stylesheet" href="{{ asset('template/assets/css/style.css') }}">

    {{-- Style Page --}}
    @yield('style-page')
</head>

<body>
<div class="container-scroller">

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

@include('layouts.script')

@yield('script-page')

</div>

        </div>
    </div>
</div>
</body>
</html>
