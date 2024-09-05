<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - {{ config('app.name', 'Laravel') }}</title>

    <link rel="stylesheet" href="{{ asset('') }}cms/assets/css/bootstrap.css">

    <link rel="stylesheet" href="{{ asset('') }}cms/assets/vendors/chartjs/Chart.min.css">

    <link rel="stylesheet" href="{{ asset('') }}cms/assets/vendors/perfect-scrollbar/perfect-scrollbar.css">
    <link rel="stylesheet" href="{{ asset('') }}cms/assets/css/app.css">
    <link rel="shortcut icon" href="{{ asset('') }}cms/assets/images/favicon.svg" type="image/x-icon">
</head>
<body>
    <div id="app">
        <div id="sidebar" class='active'>
            <div class="sidebar-wrapper active">
                <div class="sidebar-header">
                    <img src="{{ asset('') }}cms/assets/images/logo.svg" alt="" srcset="">
                </div>
                <div class="sidebar-menu">
                    <ul class="menu">
                        <li class='sidebar-title'>Main Menu</li>
                            <li class="sidebar-item ">
                                <a href="{{ route('dashboard') }}" class='sidebar-link'>
                                    <i data-feather="home" width="20"></i>
                                    <span>Dashboard</span>
                                </a>
                            </li>
                            <li class='sidebar-title'>Category</li>
                            <li class="sidebar-item  ">
                                <a href="#" class='sidebar-link'>
                                    <i data-feather="menu" width="20"></i>
                                    <span>Master Category</span>
                                </a>
                            </li>
                            <li class='sidebar-title'>Vendor</li>
                            <li class="sidebar-item  ">
                                <a href="#" class='sidebar-link'>
                                    <i data-feather="layers" width="20"></i>
                                    <span>Create Vendor</span>
                                </a>
                            </li>
                            <li class="sidebar-item  has-sub ">
                                <a href="#" class='sidebar-link'>
                                    <i data-feather="clipboard" width="20"></i>
                                    <span>List Vendor</span>
                                </a>
                                <ul class="submenu ">
                                    <li>
                                        <a href="#">Manufactur</a>
                                    </li>
                                    <li>
                                        <a href="#">General Supplier</a>
                                    </li>
                                    <li>
                                        <a href="#">Specialist Supplier</a>
                                    </li>
                                    <li>
                                        <a href="#">Contractor</a>
                                    </li>
                                    <li>
                                        <a href="#">Consultant</a>
                                    </li>
                                    <li>
                                        <a href="#">Service etc</a>
                                    </li>
                                </ul>
                            </li>
                            <li class='sidebar-title'>Jobs</li>
                            <li class="sidebar-item">
                                <a href="#" class='sidebar-link'>
                                    <i data-feather="link" width="20"></i>
                                    <span>Create Procurement</span>
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a href="#" class='sidebar-link'>
                                    <i data-feather="list" width="20"></i>
                                    <span>List Procurement</span>
                                </a>
                            </li>
                            <li class='sidebar-title'>Other</li>
                            <li class="sidebar-item">
                                <a href="#" class='sidebar-link'>
                                    <i data-feather="settings" width="20"></i>
                                    <span>Settings</span>
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" class='sidebar-link'>
                                        <i data-feather="log-out" width="20"></i>
                                        <span>Log Out</span>
                                    </a>
                                </form>
                            </li>
                            <li class="sidebar-item">
                                <a href="#" class='sidebar-link'>
                                    <i data-feather="help-circle" width="20"></i>
                                    <span>Help</span>
                                </a>
                            </li>
                    </ul>
                </div>
                <button class="sidebar-toggler btn x"><i data-feather="x"></i></button>
            </div>
        </div>
        <div id="main">
            <nav class="navbar navbar-header navbar-expand navbar-light">
                <a class="sidebar-toggler" href="#"><span class="navbar-toggler-icon"></span></a>
                <button class="btn navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                    aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav d-flex align-items-center navbar-light ms-auto">
                        <li class="dropdown nav-icon">
                            <a href="#" data-bs-toggle="dropdown" class="nav-link  dropdown-toggle nav-link-lg nav-link-user">
                                <div class="d-lg-inline-block">
                                    <i data-feather="bell"></i>
                                </div>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end dropdown-menu-large">
                                <h6 class='py-2 px-4'>Notifications</h6>
                                <ul class="list-group rounded-none">
                                    <li class="list-group-item border-0 align-items-start">
                                        <div class="avatar bg-success me-3">
                                            <span class="avatar-content"><i data-feather="shopping-cart"></i></span>
                                        </div>
                                        <div>
                                            <h6 class='text-bold'>New Order</h6>
                                            <p class='text-xs'>
                                                An order made by Ahmad Saugi for product Samsung Galaxy S69
                                            </p>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <li class="dropdown">
                            <a href="#" data-bs-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                                <div class="avatar me-1">
                                    <img src="{{ asset('') }}cms/assets/images/avatar/avatar-s-1.png" alt="" srcset="">
                                </div>
                                <div class="d-none d-md-block d-lg-inline-block">Hi, {{ auth()->user()->name }}</div>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="{{ route('profile.edit') }}"><i data-feather="user"></i> Account</a>
                                <div class="dropdown-divider"></div>
                                <!-- Authentication -->
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();">
                                        <i data-feather="log-out"></i> Logout
                                    </a>
                                </form>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
            <div class="main-content container-fluid">
                <div class="page-title">
                    <h3>Dashboard</h3>
                    <p class="text-subtitle text-muted">
                        Dunia Jasa Ads
                    </p>
                </div>
                <section class="section">
                    <div class="row mb-2">
                        <div class="col-12 col-md-3">
                            <div class="card card-statistic">
                                <div class="card-body p-0">
                                    <div class="d-flex flex-column">
                                        <div class='px-3 py-3 d-flex justify-content-between'>
                                            <h3 class='card-title'>Place for Ads</h3>
                                            <div class="card-right d-flex align-items-center">
                                                <p></p>
                                            </div>
                                        </div>
                                        <div class="chart-wrapper">
                                            <canvas id="canvas1" style="height:100px !important"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <div class="card card-statistic">
                                <div class="card-body p-0">
                                    <div class="d-flex flex-column">
                                        <div class='px-3 py-3 d-flex justify-content-between'>
                                            <h3 class='card-title'>Place for Ads</h3>
                                            <div class="card-right d-flex align-items-center">
                                                <p></p>
                                            </div>
                                        </div>
                                        <div class="chart-wrapper">
                                            <canvas id="canvas2" style="height:100px !important"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <div class="card card-statistic">
                                <div class="card-body p-0">
                                    <div class="d-flex flex-column">
                                        <div class='px-3 py-3 d-flex justify-content-between'>
                                            <h3 class='card-title'>Place for Ads</h3>
                                            <div class="card-right d-flex align-items-center">
                                                <p></p>
                                            </div>
                                        </div>
                                        <div class="chart-wrapper">
                                            <canvas id="canvas3" style="height:100px !important"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <div class="card card-statistic">
                                <div class="card-body p-0">
                                    <div class="d-flex flex-column">
                                        <div class='px-3 py-3 d-flex justify-content-between'>
                                            <h3 class='card-title'>Place for Ads</h3>
                                            <div class="card-right d-flex align-items-center">
                                                <p></p>
                                            </div>
                                        </div>
                                        <div class="chart-wrapper">
                                            <canvas id="canvas4" style="height:100px !important"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            <footer>
                <div class="footer clearfix mb-0 text-muted">
                    <div class="float-start">
                        <p>2024 &copy; Dunia Jasa</p>
                    </div>
                    <div class="float-end">
                        <p>Crafted with <span class='text-danger'><i data-feather="gift"></i></span> by <a href="#">Dunia Jasa</a></p>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script src="{{ asset('') }}cms/assets/js/feather-icons/feather.min.js"></script>
    <script src="{{ asset('') }}cms/assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="{{ asset('') }}cms/assets/js/app.js"></script>

    <script src="{{ asset('') }}cms/assets/vendors/chartjs/Chart.min.js"></script>
    <script src="{{ asset('') }}cms/assets/vendors/apexcharts/apexcharts.min.js"></script>
    {{-- <script src="{{ asset('') }}cms/assets/js/pages/dashboard.js"></script> --}}

    <script src="{{ asset('') }}cms/assets/js/main.js"></script>
</body>
</html>
