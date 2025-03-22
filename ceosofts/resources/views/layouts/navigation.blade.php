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
                @auth
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a>
                    </li>
                    
                    <!-- Customers Menu -->
                    <li class="nav-item dropdown">
                        <a id="customerDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            {{ __('Customers') }}
                        </a>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="customerDropdown">
                            <a class="dropdown-item" href="{{ route('customers.index') }}">{{ __('All Customers') }}</a>
                            <a class="dropdown-item" href="{{ route('customers.create') }}">{{ __('Add New Customer') }}</a>
                        </div>
                    </li>
                    
                    <!-- Products Menu -->
                    <li class="nav-item dropdown">
                        <a id="productDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            {{ __('Products') }}
                        </a>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="productDropdown">
                            <a class="dropdown-item" href="{{ route('products.index') }}">{{ __('All Products') }}</a>
                            <a class="dropdown-item" href="{{ route('products.create') }}">{{ __('Add New Product') }}</a>
                        </div>
                    </li>
                    
                    <!-- Orders Menu -->
                    <li class="nav-item dropdown">
                        <a id="orderDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            {{ __('Orders') }}
                        </a>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="orderDropdown">
                            <a class="dropdown-item" href="{{ route('orders.index') }}">{{ __('All Orders') }}</a>
                            <a class="dropdown-item" href="{{ route('orders.create') }}">{{ __('Create Order') }}</a>
                        </div>
                    </li>
                    
                    <!-- Admin Menu -->
                    @if(auth()->user()->hasRole('admin'))
                    <li class="nav-item dropdown">
                        <a id="adminDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            {{ __('Admin') }}
                        </a>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="adminDropdown">
                            <a class="dropdown-item" href="{{ route('admin.users.index') }}">{{ __('Users') }}</a>
                            <a class="dropdown-item" href="{{ route('admin.departments.index') }}">{{ __('Departments') }}</a>
                            <a class="dropdown-item" href="{{ route('admin.positions.index') }}">{{ __('Positions') }}</a>
                            <a class="dropdown-item" href="{{ route('admin.units.index') }}">{{ __('Units') }}</a>
                            <a class="dropdown-item" href="{{ route('admin.prefixes.index') }}">{{ __('Prefixes') }}</a>
                        </div>
                    </li>
                    @endif
                @endauth
            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ms-auto">
                <!-- Authentication Links -->
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
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            {{ Auth::user()->name }}
                        </a>

                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                {{ __('Profile') }}
                            </a>
                            
                            <a class="dropdown-item" href="{{ route('logout') }}"
                               onclick="event.preventDefault();
                                             document.getElementById('logout-form').submit();">
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
