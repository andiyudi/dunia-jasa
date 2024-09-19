<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - {{ config('app.name', 'Laravel') }}</title>

    @include('includes.style')
</head>
<body>
    @routes()
    <div id="app">
        @include('includes.sidebar')
        <div id="main">
            @include('includes.navbar')

            <div class="main-content container-fluid">
                <div class="page-title">
                    <h3>{{ $title ?? ''}}</h3>
                    <p class="text-subtitle text-muted">
                        {{ $pretitle ?? ''}}
                    </p>
                </div>
                <section class="section">

                    @yield('content')
                </section>
            </div>


            @include('includes.footer')
        </div>
    </div>
    <script src="{{ asset('') }}cms/assets/js/feather-icons/feather.min.js"></script>
    <script src="{{ asset('') }}cms/assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="{{ asset('') }}cms/assets/js/app.js"></script>

    <script src="{{ asset('') }}cms/assets/vendors/chartjs/Chart.min.js"></script>
    <script src="{{ asset('') }}cms/assets/vendors/apexcharts/apexcharts.min.js"></script>
    {{-- <script src="{{ asset('') }}cms/assets/js/pages/dashboard.js"></script> --}}

    <script src="{{ asset('') }}cms/assets/js/main.js"></script>

    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>
</html>
