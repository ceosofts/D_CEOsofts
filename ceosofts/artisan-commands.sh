#!/bin/bash

# Update composer packages
composer update

# Clear the Laravel cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Dump autoload
composer dump-autoload

# Publish Sanctum configuration (if not already done)
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

# Run migrations for Sanctum
php artisan migrate

echo "Laravel Sanctum setup complete!"
