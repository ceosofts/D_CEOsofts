<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - @yield('title', 'Home')</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    {{-- @vite(['resources/sass/app.scss', 'resources/js/app.js']) --}}
    {{-- <link rel="stylesheet" href="{{ asset('css/app.css') }}"> --}}

    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}


    <!-- Styles (เพิ่มเติมสำหรับ CSS พิเศษในแต่ละหน้า) -->
    @stack('styles')

    <!-- ✅ เพิ่ม CSS สำหรับ Search Box -->
    {{-- @push('styles')
    <style>
        .input-group input {
            height: 38px !important; /* หรือกำหนดค่าให้เหมือนกัน */
        }
    </style>
    @endpush --}}

    @push('styles')
    <style>
        .input-group .form-control {
            height: 38px !important; /* บังคับให้เปลี่ยนขนาด */
        }
    </style>
    @endpush

</head>
<body>
    <div id="app">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('home') }}">{{ __('Home') }}</a>
                        </li>


        @auth
            <li class="nav-item">
                <a class="nav-link" href="{{ route('dashboard') }}">
                    <i class="bi bi-speedometer2"></i> {{ __('Dashboard') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('customers.index') }}">
                    <i class="bi bi-person"></i> {{ __('Customers') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('products.index') }}">
                    <i class="bi bi-box-seam"></i> {{ __('Products') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('orders.index') }}">
                    <i class="bi bi-basket"></i> {{ __('Orders') }}
                </a>
            </li>

                    <!-- Dropdown HR Menu -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarHRDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            HR
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarHRDropdown">
                            <a class="dropdown-item" href="{{ route('employees.index') }}">Employees</a>
                            @can('view company holidays')
                                <a class="dropdown-item" href="{{ route('company-holidays.index') }}">📅 ปฏิทินวันหยุด</a>
                            @endcan
                            <a class="dropdown-item" href="{{ url('/attendances') }}">
                                <i class="bi bi-calendar-check"></i> บันทึกเวลางาน
                            </a>
                            <a class="dropdown-item" href="{{ route('wages.summary') }}">
                                <i class="fas fa-hand-holding-usd"></i> สรุปค่าแรง
                            </a>
                        </div>
                    </li>




            <!-- ✅ รวม Admin Setting ไว้ที่เดียว -->
            @canany(['manage departments', 'manage users', 'manage companies', 'manage units'])
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="bi bi-gear"></i> Admin Setting
                    </a>
                    <div class="dropdown-menu" aria-labelledby="adminDropdown">
                        @can('manage departments')
                            <a class="dropdown-item" href="{{ route('admin.departments.index') }}">
                                <i class="bi bi-building"></i> แผนก
                            </a>
                        @endcan
                        @can('manage users')
                            <a class="dropdown-item" href="{{ route('admin.users.index') }}">
                                <i class="bi bi-people"></i> ผู้ใช้
                            </a>
                        @endcan
                        @can('manage companies')
                            <a class="dropdown-item" href="{{ route('admin.companies.index') }}">
                                <i class="bi bi-buildings"></i> Companies
                            </a>
                        @endcan
                        @can('manage units')
                            <a class="dropdown-item" href="{{ route('admin.units.index') }}">
                                <i class="bi bi-rulers"></i> Units
                            </a>
                        @endcan

                        @can('manage positions')
                            <a class="dropdown-item" href="{{ route('admin.positions.index') }}">
                                <i class="bi bi-person-badge"></i> จัดการตำแหน่งพนักงาน
                            </a>
                        @endcan

                        @can('manage prefixes')
                            <a class="dropdown-item" href="{{ route('admin.prefixes.index') }}">
                                <i class="bi bi-list"></i> จัดการคำนำหน้าชื่อ
                            </a>
                        @endcan

                        @can('manage item statuses')
                            <a class="dropdown-item" href="{{ route('admin.item_statuses.index') }}">
                                <i class="bi bi-list"></i> จัดการสถานะสินค้า
                            </a>
                        @endcan
                        
                        @can('manage payment statuses')
                            <a class="dropdown-item" href="{{ route('admin.payment_statuses.index') }}">
                                <i class="bi bi-list"></i> จัดการสถานะการจ่ายเงิน
                            </a>
                        @endcan

                        @can('manage tax settings')
                            <a class="dropdown-item" href="{{ route('admin.tax.index') }}">
                                <i class="bi bi-list"></i> จัดการการตั้งค่าภาษี
                            </a>
                        @endcan


                    </div>
                </li>
            @endcanany
        @endauth

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    {{ Auth::user()->name }}
                                </a>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a href="{{ route('dashboard') }}" class="dropdown-item">Dashboard</a>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="py-4">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-light text-center py-3 mt-4">
            <div class="container">
                <p class="mb-0">&copy; 2025 {{ config('app.name', 'Laravel') }}. All rights reserved.</p>
            </div>
        </footer>
    </div>

    <!-- Scripts -->
    @stack('scripts')
</body>
</html>