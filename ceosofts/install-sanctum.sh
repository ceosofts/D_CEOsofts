#!/bin/bash

# Install Laravel Sanctum
composer require laravel/sanctum

# Publish configuration
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

# Run migrations
php artisan migrate

# Clear cache
php artisan config:clear
php artisan cache:clear

echo "Laravel Sanctum installed successfully!"
