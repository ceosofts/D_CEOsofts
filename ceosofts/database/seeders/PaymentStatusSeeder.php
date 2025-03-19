<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class PaymentStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ตรวจสอบว่าตารางมีอยู่จริงหรือไม่
        if (!Schema::hasTable('payment_statuses')) {
            echo "Table 'payment_statuses' does not exist!\n";
            return;
        }

        $now = Carbon::now();
        
        // ตรวจสอบว่าคอลัมน์ที่จะใช้มีอยู่หรือไม่
        $hasNewColumns = Schema::hasColumns('payment_statuses', ['code', 'description', 'color', 'is_active']);
        
        if ($hasNewColumns) {
            // กรณีมีคอลัมน์ครบ
            $statuses = [
                [
                    'name' => 'จ่ายเงินสด',
                    'code' => 'CASH',
                    'description' => 'การชำระเงินด้วยเงินสด',
                    'color' => '#28a745',
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                [
                    'name' => 'โอนเงิน',
                    'code' => 'TRANSFER',
                    'description' => 'การชำระเงินผ่านการโอนเงินธนาคาร',
                    'color' => '#17a2b8',
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                [
                    'name' => 'จ่ายเช็ค',
                    'code' => 'CHEQUE',
                    'description' => 'การชำระเงินด้วยเช็ค',
                    'color' => '#ffc107',
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                [
                    'name' => 'เช็ครอเคลียร์ริ่ง',
                    'code' => 'PENDING',
                    'description' => 'เช็คที่อยู่ระหว่างรอการเคลียร์ริ่ง',
                    'color' => '#6c757d',
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                [
                    'name' => 'เช็คเด้ง',
                    'code' => 'BOUNCE',
                    'description' => 'เช็คที่ไม่สามารถขึ้นเงินได้',
                    'color' => '#dc3545',
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now
                ]
            ];
        } else {
            // กรณีมีเฉพาะคอลัมน์พื้นฐาน
            $statuses = [
                [
                    'name' => 'จ่ายเงินสด',
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                [
                    'name' => 'โอนเงิน',
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                [
                    'name' => 'จ่ายเช็ค',
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                [
                    'name' => 'เช็ครอเคลียร์ริ่ง',
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                [
                    'name' => 'เช็คเด้ง',
                    'created_at' => $now,
                    'updated_at' => $now
                ]
            ];
        }

        try {
            // ใช้ insertOrIgnore() เพื่อป้องกันข้อมูลซ้ำ
            DB::table('payment_statuses')->insertOrIgnore($statuses);
        } catch (\Exception $e) {
            echo "Error seeding payment statuses: " . $e->getMessage() . "\n";
        }
    }
}
