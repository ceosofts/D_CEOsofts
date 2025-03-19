<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class PrefixSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ตรวจสอบการมีอยู่ของตาราง
        if (!Schema::hasTable('prefixes')) {
            $this->command->error("Table 'prefixes' does not exist, skipping seeder.");
            return;
        }

        try {
            // ตรวจสอบคอลัมน์ที่จำเป็น
            $columns = Schema::getColumnListing('prefixes');
            $hasPrefixTh = in_array('prefix_th', $columns);
            
            $this->command->info("Table structure check: prefix_th column exists: " . ($hasPrefixTh ? 'YES' : 'NO'));
            
            if (!$hasPrefixTh) {
                $this->command->error("Required column 'prefix_th' not found in prefixes table.");
                return;
            }

            // คำนำหน้าชื่อภาษาไทยพื้นฐาน
            $prefixes = [
                [
                    'prefix_th' => 'นาย',
                    'prefix_en' => 'Mr.',
                    'description' => 'คำนำหน้าชื่อสำหรับผู้ชาย',
                    'is_active' => true
                ],
                [
                    'prefix_th' => 'นาง',
                    'prefix_en' => 'Mrs.',
                    'description' => 'คำนำหน้าชื่อสำหรับผู้หญิงที่แต่งงานแล้ว',
                    'is_active' => true
                ],
                [
                    'prefix_th' => 'นางสาว',
                    'prefix_en' => 'Miss',
                    'description' => 'คำนำหน้าชื่อสำหรับผู้หญิงที่ยังไม่แต่งงาน',
                    'is_active' => true
                ],
                [
                    'prefix_th' => 'ดร.',
                    'prefix_en' => 'Dr.',
                    'description' => 'คำนำหน้าชื่อสำหรับผู้มีตำแหน่งด็อกเตอร์',
                    'is_active' => true
                ],
                [
                    'prefix_th' => 'อาจารย์',
                    'prefix_en' => 'Prof.',
                    'description' => 'คำนำหน้าชื่อสำหรับอาจารย์',
                    'is_active' => true
                ]
            ];

            // ลบฟิลด์ name ออกจากข้อมูลก่อนเพิ่มลงฐานข้อมูล
            $count = 0;
            foreach ($prefixes as $prefix) {
                try {
                    DB::table('prefixes')->updateOrInsert(
                        ['prefix_th' => $prefix['prefix_th']],
                        [
                            'prefix_en' => $prefix['prefix_en'],
                            'description' => $prefix['description'],
                            'is_active' => $prefix['is_active'],
                            'created_at' => now(),
                            'updated_at' => now()
                        ]
                    );
                    $count++;
                    $this->command->info("Seeded prefix: " . $prefix['prefix_th']);
                } catch (\Exception $e) {
                    $this->command->error("Error seeding prefix " . $prefix['prefix_th'] . ": " . $e->getMessage());
                }
            }

            $this->command->info("Successfully seeded {$count} prefixes");
        } catch (\Exception $e) {
            $this->command->error("Error running " . get_class($this) . ": " . $e->getMessage());
        }
    }
}
