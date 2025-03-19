<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ItemStatus;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ItemStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Seeding item statuses...');
        
        // ข้อมูลตัวอย่างสถานะสินค้า
        $statuses = [
            [
                'name' => 'พร้อมจำหน่าย',
                'code' => 'AVAIL',
                'description' => 'สินค้าพร้อมจำหน่าย มีของในสต็อก',
                'color' => '#28a745',
                'is_active' => true
            ],
            [
                'name' => 'สินค้าหมด',
                'code' => 'OUT',
                'description' => 'สินค้าหมด ไม่มีของในสต็อก',
                'color' => '#dc3545',
                'is_active' => true
            ],
            [
                'name' => 'กำลังนำเข้า',
                'code' => 'IMP',
                'description' => 'อยู่ระหว่างการนำเข้าสินค้าเพิ่มเติม',
                'color' => '#fd7e14',
                'is_active' => true
            ],
            [
                'name' => 'สั่งจองล่วงหน้า',
                'code' => 'PRE',
                'description' => 'สินค้าสามารถสั่งจองล่วงหน้าได้',
                'color' => '#17a2b8',
                'is_active' => true
            ],
            [
                'name' => 'หยุดจำหน่าย',
                'code' => 'DISC',
                'description' => 'สินค้าหยุดจำหน่ายหรือถูกยกเลิก',
                'color' => '#6c757d',
                'is_active' => false
            ],
            [
                'name' => 'ใกล้หมด',
                'code' => 'LOW',
                'description' => 'สินค้าเหลือน้อย ใกล้หมด',
                'color' => '#ffc107',
                'is_active' => true
            ]
        ];
        
        DB::beginTransaction();
        try {
            foreach ($statuses as $status) {
                // ตรวจสอบว่ามีข้อมูลนี้แล้วหรือยัง
                $existingStatus = ItemStatus::where('name', $status['name'])->first();
                
                if ($existingStatus) {
                    // อัปเดตข้อมูลที่มีอยู่
                    $existingStatus->update($status);
                    $this->command->info('Updated status: ' . $status['name']);
                } else {
                    // เพิ่มข้อมูลใหม่
                    ItemStatus::create($status);
                    $this->command->info('Created status: ' . $status['name']);
                }
            }
            
            DB::commit();
            $this->command->info('Item statuses seeded successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error seeding item statuses: ' . $e->getMessage());
            $this->command->error('Error seeding item statuses: ' . $e->getMessage());
        }
    }
}
