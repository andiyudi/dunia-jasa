<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-3202491369358787"
    crossorigin="anonymous"></script>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Dunia Jasa</title>
    <meta name="description" content="">
    <meta name="keywords" content="">

    <!-- Favicons -->
    <link href="{{ asset ('') }}welcome/assets/img/favicon.png" rel="icon">
    <link href="{{ asset ('') }}welcome/assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800&family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="{{ asset ('') }}welcome/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset ('') }}welcome/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset ('') }}welcome/assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="{{ asset ('') }}welcome/assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="{{ asset ('') }}welcome/assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

    <!-- Main CSS File -->
    <link href="{{ asset ('') }}welcome/assets/css/main.css" rel="stylesheet">

    <!-- =======================================================
    * Template Name: BizLand
    * Template URL: https://bootstrapmade.com/bizland-bootstrap-business-template/
    * Updated: Aug 07 2024 with Bootstrap v5.3.3
    * Author: BootstrapMade.com
    * License: https://bootstrapmade.com/license/
    ======================================================== -->
    </head>

    <body class="index-page">

    <header id="header" class="header sticky-top">

    <div class="branding d-flex align-items-cente">

        <div class="container position-relative d-flex align-items-center justify-content-between">
        <a href="#" class="logo d-flex align-items-center">
            <!-- Uncomment the line below if you also wish to use an image logo -->
            <img src="{{ asset ('') }}welcome/assets/img/logo.png" alt="">
            <h1 class="sitename">Dunia Jasa</h1>
        </a>

        <nav id="navmenu" class="navmenu">
            <ul>
            <li><a href="#hero" class="active">Home</a></li>
            @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}">
                            Log in
                        </a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}">
                                Register
                            </a>
                        @endif
                    @endauth
            @endif
            </ul>
            <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
        </nav>

        </div>

    </div>

    </header>

    <main class="main">

    <!-- Hero Section -->
    <section id="hero" class="hero section light-background">

        <div class="container">
        <div class="row gy-4">
            <div class="col-lg-6 order-2 order-lg-1 d-flex flex-column justify-content-center" data-aos="zoom-out">
            <h1>Welcome to<br><span>Dunia Jasa</span></h1>
            <p>Portal Online Procurement And Tender</p>
            <div class="d-flex">
            </div>
            </div>
        </div>
        </div>

    </section><!-- /Hero Section -->


    </main>

    <footer id="footer" class="footer">


    <div class="container copyright text-center mt-4">
        <p>© <span>Copyright</span> <strong class="px-1 sitename">Dunia Jasa</strong> <span>All Rights Reserved</span></p>
        <div class="credits">
        <!-- All the links in the footer should remain intact. -->
        <!-- You can delete the links only if you've purchased the pro version. -->
        <!-- Licensing information: https://bootstrapmade.com/license/ -->
        <!-- Purchase the pro version with working PHP/AJAX contact form: [buy-url] -->
        Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a>
        </div>
    </div>

    </footer>

    <!-- Scroll Top -->
    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <!-- Preloader -->
    <div id="preloader">
    <div></div>
    <div></div>
    <div></div>
    <div></div>
    </div>

    <!-- Vendor JS Files -->
    <script src="{{ asset ('') }}welcome/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset ('') }}welcome/assets/vendor/php-email-form/validate.js"></script>
    <script src="{{ asset ('') }}welcome/assets/vendor/aos/aos.js"></script>
    <script src="{{ asset ('') }}welcome/assets/vendor/glightbox/js/glightbox.min.js"></script>
    <script src="{{ asset ('') }}welcome/assets/vendor/waypoints/noframework.waypoints.js"></script>
    <script src="{{ asset ('') }}welcome/assets/vendor/purecounter/purecounter_vanilla.js"></script>
    <script src="{{ asset ('') }}welcome/assets/vendor/swiper/swiper-bundle.min.js"></script>
    <script src="{{ asset ('') }}welcome/assets/vendor/imagesloaded/imagesloaded.pkgd.min.js"></script>
    <script src="{{ asset ('') }}welcome/assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>

    <!-- Main JS File -->
    <script src="{{ asset ('') }}welcome/assets/js/main.js"></script>

    </body>

</html>
