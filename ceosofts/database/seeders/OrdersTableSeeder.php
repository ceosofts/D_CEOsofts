<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OrdersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ตรวจสอบการมีอยู่ของตาราง
        if (!Schema::hasTable('orders')) {
            $this->command->error("Table 'orders' does not exist, skipping seeder.");
            return;
        }

        // ตรวจสอบว่ามีตาราง customers หรือไม่
        if (!Schema::hasTable('customers')) {
            $this->command->error("Table 'customers' does not exist, skipping seeder.");
            return;
        }

        // ตรวจสอบว่ามีข้อมูลลูกค้า
        $customerCount = DB::table('customers')->count();
        if ($customerCount == 0) {
            $this->command->error("No customers found, please seed customers first.");
            return;
        }

        try {
            // ตรวจสอบคอลัมน์ที่จำเป็น
            $columns = Schema::getColumnListing('orders');
            $requiredColumns = ['customer_id', 'order_number', 'order_date', 'total_amount'];
            $missingColumns = array_diff($requiredColumns, $columns);
            
            if (!empty($missingColumns)) {
                $this->command->error("Missing columns in orders table: " . implode(', ', $missingColumns));
                return;
            }
            
            // ดึง ID ลูกค้าตัวแรก
            $customer = DB::table('customers')->first();
            if (!$customer) {
                $this->command->info("No customers available. Orders not created.");
                return;
            }

            // คำสั่งซื้อตัวอย่าง
            $orders = [
                [
                    'customer_id' => $customer->id,
                    'order_number' => 'ORD-' . date('Ymd') . '-001',
                    'order_date' => now()->format('Y-m-d'),
                    'total_amount' => 5000.00,
                    'status' => 'pending',
                    'notes' => 'คำสั่งซื้อตัวอย่าง 1'
                ],
                [
                    'customer_id' => $customer->id,
                    'order_number' => 'ORD-' . date('Ymd') . '-002',
                    'order_date' => now()->subDays(1)->format('Y-m-d'),
                    'total_amount' => 8000.00,
                    'status' => 'completed',
                    'notes' => 'คำสั่งซื้อตัวอย่าง 2'
                ]
            ];

            $count = 0;
            foreach ($orders as $order) {
                DB::table('orders')->updateOrInsert(
                    ['order_number' => $order['order_number']],
                    [
                        'customer_id' => $order['customer_id'],
                        'order_date' => $order['order_date'],
                        'total_amount' => $order['total_amount'],
                        'status' => $order['status'],
                        'notes' => $order['notes'],
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );
                $count++;
            }

            $this->command->info("Successfully seeded {$count} orders");
        } catch (\Exception $e) {
            $this->command->error("Error running " . get_class($this) . ": " . $e->getMessage());
        }
    }
}
