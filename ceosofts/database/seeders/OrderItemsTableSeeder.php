<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OrderItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ตรวจสอบการมีอยู่ของตาราง
        if (!Schema::hasTable('order_items')) {
            $this->command->error("Table 'order_items' does not exist, skipping seeder.");
            return;
        }

        // ตรวจสอบว่ามีตาราง orders และ products หรือไม่
        if (!Schema::hasTable('orders')) {
            $this->command->error("Table 'orders' does not exist, skipping seeder.");
            return;
        }

        if (!Schema::hasTable('products')) {
            $this->command->error("Table 'products' does not exist, skipping seeder.");
            return;
        }

        // ตรวจสอบว่ามีข้อมูลใน orders และ products หรือไม่
        $orderCount = DB::table('orders')->count();
        if ($orderCount == 0) {
            $this->command->error("No orders found, please seed orders first.");
            return;
        }

        $productCount = DB::table('products')->count();
        if ($productCount == 0) {
            $this->command->error("No products found, please seed products first.");
            return;
        }

        try {
            // ตรวจสอบคอลัมน์ที่จำเป็น
            $columns = Schema::getColumnListing('order_items');
            $requiredColumns = ['order_id', 'product_id', 'quantity', 'unit_price', 'subtotal'];
            $missingColumns = array_diff($requiredColumns, $columns);
            
            if (!empty($missingColumns)) {
                $this->command->error("Missing columns in order_items table: " . implode(', ', $missingColumns));
                return;
            }
            
            // ดึงข้อมูล order และ product แรก
            $order = DB::table('orders')->first();
            $products = DB::table('products')->limit(2)->get();
            
            if (!$order || count($products) == 0) {
                $this->command->info("No orders or products available. Order items not created.");
                return;
            }

            // รายการสินค้าในคำสั่งซื้อตัวอย่าง
            $orderItems = [];
            foreach ($products as $index => $product) {
                $quantity = rand(1, 5);
                $unitPrice = $product->price;
                $subtotal = $quantity * $unitPrice;
                
                $orderItems[] = [
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }

            // เพิ่มข้อมูล และตรวจสอบก่อนเพิ่มเพื่อป้องกันการซ้ำ
            $count = 0;
            foreach ($orderItems as $item) {
                $exists = DB::table('order_items')
                    ->where('order_id', $item['order_id'])
                    ->where('product_id', $item['product_id'])
                    ->exists();
                    
                if (!$exists) {
                    DB::table('order_items')->insert($item);
                    $count++;
                }
            }

            $this->command->info("Successfully seeded {$count} order items");
        } catch (\Exception $e) {
            $this->command->error("Error running " . get_class($this) . ": " . $e->getMessage());
        }
    }
}
