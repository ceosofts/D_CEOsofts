<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'CEOsofts') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    <!-- Scripts and Styles -->
    @assets
</head>
<body>
    <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                {{ config('app.name', 'CEOsofts') }}
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Left Side Of Navbar -->
                <ul class="navbar-nav me-auto">
                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="navbar-nav ms-auto">
                    <!-- Authentication Links -->
                    @if (Route::has('login'))
                        @auth
                            <li class="nav-item">
                                <a href="{{ url('/dashboard') }}" class="nav-link">Dashboard</a>
                            </li>
                        @else
                            <li class="nav-item">
                                <a href="{{ route('login') }}" class="nav-link">Log in</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a href="{{ route('register') }}" class="nav-link">Register</a>
                                </li>
                            @endif
                        @endauth
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    <section class="hero-section text-center">
        <div class="container">
            <h1 class="display-4 mb-4">Welcome to CEOsofts</h1>
            <p class="lead mb-5">A powerful business management solution for modern businesses.</p>
            <div>
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn btn-light btn-lg mx-2">Go to Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-light btn-lg mx-2">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg mx-2">Register</a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Our Features</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="feature-box text-center">
                        <div class="feature-icon">💼</div>
                        <h3>Customer Management</h3>
                        <p>Efficiently manage all your customer information and interactions.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box text-center">
                        <div class="feature-icon">📦</div>
                        <h3>Product Management</h3>
                        <p>Keep track of your products, inventory, and pricing information.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box text-center">
                        <div class="feature-icon">📊</div>
                        <h3>Order Processing</h3>
                        <p>Streamline your order workflow from creation to fulfillment.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box text-center">
                        <div class="feature-icon">💰</div>
                        <h3>Invoicing</h3>
                        <p>Create professional invoices and manage payments efficiently.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box text-center">
                        <div class="feature-icon">👥</div>
                        <h3>HR Management</h3>
                        <p>Manage employees, attendance, and payroll all in one place.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box text-center">
                        <div class="feature-icon">📈</div>
                        <h3>Reporting</h3>
                        <p>Generate insights with comprehensive business reports.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="py-4 bg-light">
        <div class="container text-center">
            <p>© {{ date('Y') }} CEOsofts. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
