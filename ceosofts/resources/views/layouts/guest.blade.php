<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'CEOsofts') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @assets
    
    <style>
        body {
            font-family: 'Figtree', sans-serif;
            background-color: #f8f9fa;
        }
        .guest-card {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="font-sans text-gray-900 antialiased">
    <div class="min-vh-100 d-flex flex-column justify-content-center align-items-center pt-5 pt-sm-0">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card guest-card">
                        <div class="card-header bg-white text-center py-3">
                            <a href="/" class="d-block">
                                <h4 class="mb-0">{{ config('app.name', 'CEOsofts') }}</h4>
                            </a>
                        </div>

                        <div class="card-body py-4 px-4">
                            {{ $slot ?? '' }}
                            @yield('content')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
