@extends('layouts.app')

@section('title', 'Home')

@section('content')
<div class="container py-5">
    <!-- Hero Section -->
    <section class="text-center mb-5">
        <h1 class="display-4">Welcome to Our Company</h1>
        <p class="lead">Providing the best services and products for you.</p>
    </section>

    <!-- About Section -->
    <section class="mb-5">
        <h2>About Us</h2>
        <p>
            We are a leading company providing ERP and CRM solutions for businesses. 
            Our mission is to deliver the best tools to help businesses thrive in a competitive market.
        </p>
    </section>

    <!-- Contact Section -->
    <section class="mb-5">
        <h2>Contact Us</h2>
        <p><strong>Address:</strong> 1234 Main Street, Big City, Country</p>
        <p><strong>Phone:</strong> 081-234-5678</p>
        <p><strong>Email:</strong> <a href="mailto:contact@ourcompany.com">contact@ourcompany.com</a></p>
        <p><strong>Follow us:</strong></p>
        <ul class="list-unstyled">
            <li>
                <a href="https://facebook.com/ourcompany" target="_blank">
                    <i class="bi bi-facebook"></i> Facebook
                </a>
            </li>
            <li>
                <a href="#" target="_blank">
                    <i class="bi bi-chat-dots"></i> Line QR Code
                </a>
            </li>
            <li>
                <a href="https://ourcompany.com" target="_blank">
                    <i class="bi bi-globe"></i> Website
                </a>
            </li>
        </ul>
    </section>

    <!-- Footer Section -->
    <footer class="bg-light py-3">
        <div class="container text-center">
            <p class="mb-0">&copy; 2025 Our Company. All rights reserved.</p>
        </div>
    </footer>
</div>
@endsection