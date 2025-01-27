@extends('layouts.app')

@section('title', 'Home')

@section('content')
<div class="container">
    <div class="text-center my-5">
        <h1>Welcome to Our Company</h1>
        <p>Providing the best services and products for you.</p>
    </div>

    <!-- About Section -->
    <div class="my-5">
        <h2>About Us</h2>
        <p>
            We are a leading company providing ERP and CRM solutions for businesses. 
            Our mission is to deliver the best tools to help businesses thrive in a competitive market.
        </p>
    </div>

    <!-- Contact Section -->
    <div class="my-5">
        <h2>Contact Us</h2>
        <p><strong>Address:</strong> 1234 Main Street, Big City, Country</p>
        <p><strong>Phone:</strong> 081-234-5678</p>
        <p><strong>Email:</strong> contact@ourcompany.com</p>
        <p><strong>Follow us:</strong></p>
        <ul>
            <li><a href="https://facebook.com/ourcompany" target="_blank">Facebook</a></li>
            <li><a href="#" target="_blank">Line QR Code</a></li>
            <li><a href="https://ourcompany.com" target="_blank">Website</a></li>
        </ul>
    </div>

    <!-- Footer Section -->
    <footer class="bg-light py-3">
        <div class="container text-center">
            <p>&copy; 2025 Our Company. All rights reserved.</p>
        </div>
    </footer>
</div>
@endsection
