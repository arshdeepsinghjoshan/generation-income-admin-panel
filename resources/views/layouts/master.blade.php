<!DOCTYPE html>

<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="../assets/" data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>{{ env('APP_NAME') }} - @yield('title')</title>


    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ url('/assets/img/favicon/favicon.ico') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="{{ url('/assets/vendor/fonts/boxicons.css') }}" />
    <link rel="stylesheet" href="{{ url('/assets/vendor/css/core.css') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ url('/assets/vendor/css/theme-default.css') }}" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ url('/assets/css/demo.css') }}" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ url('/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ url('/assets/vendor/libs/apex-charts/apex-charts.css') }}" />
    <script src="{{ url('/assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ url('/assets/js/config.js') }}"></script>
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet" type="text/css" />

</head>



<body>

    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            @include('layouts.sidebar')
            <div class="layout-page">
                @include('layouts.top_navbar')
                @if (session('success') || session('error'))
                <div class="container-xxl mt-2">
                    @include('include.message')

                </div>
                @endif
                @yield('content')



                <footer class="content-footer footer bg-footer-theme">
                    <div class="container-xxl d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
                        <div class="mb-2 mb-md-0">
                            ©
                            <script>
                                document.write(new Date().getFullYear());
                            </script> Copyright {{env('APP_NAME')}}. All Rights Reserved Powered By

                            <a href="http://arshresume.epizy.com/" target="_blank" class="footer-link fw-bolder">ASK</a>
                        </div>

                    </div>
                </footer>

                <div class="content-backdrop fade"></div>
            </div>
        </div>
    </div>

    <!-- Overlay -->
    <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <script src="{{ url('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ url('assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ url('assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ url('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ url('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
    <script src="{{ url('assets/vendor/js/menu.js') }}"></script>
    <script src="{{ url('assets/js/main.js') }}"></script>
    <script src="{{ url('assets/js/dashboards-analytics.js') }}"></script>

    <script>
        $(".toggle-password").click(function() {
            $(this).toggleClass("fa-eye fa-eye-slash ");
            $('#password').prop('type', $("#password").prop('type') == 'text' ? 'password' : 'text');

        });
        $(".toggle-password-confirm").click(function() {
            $(this).toggleClass("fa-eye fa-eye-slash ");
            $('#confirm_password').prop('type', $("#confirm_password").prop('type') == 'text' ? 'password' : 'text');
        });
    </script>
</body>

</html>