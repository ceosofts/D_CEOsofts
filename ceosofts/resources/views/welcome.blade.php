<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome to {{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,600,700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f9f9f9;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        header {
            text-align: center;
            padding: 50px 20px;
            background-color: #333;
            color: #fff;
        }
        header h1 {
            font-size: 2.5rem;
            margin: 0;
        }
        header p {
            margin: 10px 0 0;
            font-size: 1.2rem;
        }
        .nav-links {
            margin-top: 20px;
        }
        .nav-links a {
            margin: 0 10px;
            color: #fff;
            text-decoration: none;
            font-weight: bold;
            padding: 10px 15px;
            border: 2px solid transparent;
            border-radius: 5px;
            transition: background-color 0.3s, border-color 0.3s;
        }
        .nav-links a:hover {
            background-color: #fff;
            color: #ff2d20;
            border-color: #fff;
        }
        section {
            margin: 40px 0;
        }
        section h2 {
            font-size: 1.8rem;
            margin-bottom: 20px;
        }
        ul {
            list-style: none;
            padding: 0;
        }
        ul li {
            margin-bottom: 10px;
            font-size: 1.1rem;
        }
        footer {
            background-color: #333;
            color: #fff;
            text-align: center;
            padding: 20px 0;
        }
        footer a {
            color: #ff2d20;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <header>
        <h1>Welcome to {{ config('app.name', 'Our Company') }}</h1>
        <p>Your one-stop solution for ERP and CRM systems.</p>

        <!-- Navigation Links -->
        <div class="nav-links">
            @auth
                <a href="{{ url('/home') }}">Home</a>
            @else
                <a href="{{ route('login') }}">Log in</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}">Register</a>
                @endif
            @endauth
        </div>
    </header>

    <div class="container">
        <!-- About Us Section -->
        <section>
            <h2>About Us</h2>
            <p>
                Our company specializes in delivering top-notch ERP and CRM solutions that help businesses streamline their operations and enhance customer relationships. 
                With years of experience and a dedicated team, we ensure that our clients achieve their goals efficiently.
            </p>
        </section>

        <!-- Products and Services Section -->
        <section>
            <h2>Our Products and Services</h2>
            <ul>
                <li>💼 <strong>ERP Systems</strong>: Streamline your business processes with advanced ERP solutions.</li>
                <li>🤝 <strong>CRM Tools</strong>: Build and maintain strong customer relationships with our CRM tools.</li>
                <li>📊 <strong>Business Consulting</strong>: Get expert advice to optimize your business strategies.</li>
                <li>🛠️ <strong>Custom Software Development</strong>: Tailored solutions to meet your unique business needs.</li>
            </ul>
        </section>

        <!-- Contact Section -->
        <section>
            <h2>Contact Us</h2>
            <p><strong>Address:</strong> 1234 Main Street, Big City, Country</p>
            <p><strong>Phone:</strong> 081-234-5678</p>
            <p><strong>Email:</strong> <a href="mailto:contact@ourcompany.com">contact@ourcompany.com</a></p>
            <p><strong>Follow us:</strong></p>
            <ul>
                <li><a href="https://facebook.com/ourcompany" target="_blank">Facebook</a></li>
                <li><a href="#" target="_blank">Line</a></li>
                <li><a href="https://ourcompany.com" target="_blank">Website</a></li>
            </ul>
        </section>
    </div>

    <footer>
        <p>&copy; {{ date('Y') }} {{ config('app.name', 'Our Company') }}. All rights reserved.</p>
        <p>Powered by Laravel</p>
    </footer>
</body>
</html>
