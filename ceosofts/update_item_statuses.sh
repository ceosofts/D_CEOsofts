#!/bin/bash
# คำสั่งเพื่อรัน migration และ seed ข้อมูลสถานะสินค้า

echo "===== อัปเดตตาราง Item Statuses ====="

echo "1. รัน Migration..."
php artisan migrate --path=database/migrations/2025_04_07_000000_update_item_statuses_table.php

echo "2. เคลียร์แคช..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo "3. รัน Seeder..."
php artisan db:seed --class=ItemStatusSeeder

echo "✓ การอัปเดตเสร็จสมบูรณ์!"
