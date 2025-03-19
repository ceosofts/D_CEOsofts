<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Product;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ตรวจสอบการมีอยู่ของตาราง
        if (!Schema::hasTable('products')) {
            $this->command->error("Table 'products' does not exist, skipping seeder.");
            return;
        }

        try {
            // ตรวจสอบคอลัมน์ที่จำเป็น
            $columns = Schema::getColumnListing('products');
            $requiredColumns = ['name', 'code', 'price', 'sku', 'stock_quantity'];
            $missingColumns = array_diff($requiredColumns, $columns);
            
            if (!empty($missingColumns)) {
                $this->command->error("Missing columns in products table: " . implode(', ', $missingColumns));
                return;
            }
            
            // สินค้าตัวอย่าง
            $products = [
                [
                    'code' => 'P001',
                    'name' => 'สินค้าตัวอย่าง 1',
                    'description' => 'คำอธิบายสินค้าตัวอย่าง 1',
                    'price' => 1000.00,
                    'stock_quantity' => 50,
                    'sku' => 'SKU001',
                    'barcode' => 'BAR001',
                    'is_active' => true
                ],
                [
                    'code' => 'P002',
                    'name' => 'สินค้าตัวอย่าง 2',
                    'description' => 'คำอธิบายสินค้าตัวอย่าง 2',
                    'price' => 2000.00,
                    'stock_quantity' => 30,
                    'sku' => 'SKU002',
                    'barcode' => 'BAR002',
                    'is_active' => true
                ],
                [
                    'code' => 'P003',
                    'name' => 'สินค้าตัวอย่าง 3',
                    'description' => 'คำอธิบายสินค้าตัวอย่าง 3',
                    'price' => 3000.00,
                    'stock_quantity' => 20,
                    'sku' => 'SKU003',
                    'barcode' => 'BAR003',
                    'is_active' => true
                ],
            ];

            $count = 0;
            foreach ($products as $product) {
                DB::table('products')->updateOrInsert(
                    ['code' => $product['code']],
                    [
                        'name' => $product['name'],
                        'description' => $product['description'],
                        'price' => $product['price'],
                        'stock_quantity' => $product['stock_quantity'],
                        'sku' => $product['sku'],
                        'barcode' => $product['barcode'],
                        'is_active' => $product['is_active'],
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );
                $count++;
            }

            $this->command->info("Successfully seeded {$count} products");
        } catch (\Exception $e) {
            $this->command->error("Error running " . get_class($this) . ": " . $e->getMessage());
        }
    }
}
