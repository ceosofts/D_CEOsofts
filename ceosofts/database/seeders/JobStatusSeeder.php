<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class JobStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!Schema::hasTable('job_statuses')) {
            $this->command->error("Table 'job_statuses' does not exist, skipping seeder.");
            return;
        }
        
        try {
            // ตรวจสอบว่ามีคอลัมน์ sort_order หรือไม่
            if (!Schema::hasColumn('job_statuses', 'sort_order')) {
                $this->command->error("Column 'sort_order' not found in job_statuses table.");
                return;
            }

            $statuses = [
                [
                    'name' => 'รอดำเนินการ',
                    'color' => '#FFA500', // สีส้ม
                    'sort_order' => 1,
                ],
                [
                    'name' => 'กำลังดำเนินการ',
                    'color' => '#4169E1', // สีน้ำเงิน
                    'sort_order' => 2,
                ],
                [
                    'name' => 'เสร็จสิ้น',
                    'color' => '#008000', // สีเขียว
                    'sort_order' => 3,
                ],
                [
                    'name' => 'ยกเลิก',
                    'color' => '#FF0000', // สีแดง
                    'sort_order' => 4,
                ]
            ];

            foreach ($statuses as $status) {
                DB::table('job_statuses')->updateOrInsert(
                    ['name' => $status['name']],
                    [
                        'color' => $status['color'],
                        'sort_order' => $status['sort_order'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
            
            $this->command->info("Successfully seeded job statuses");
        } catch (\Exception $e) {
            $this->command->error("Error running " . get_class($this) . ": " . $e->getMessage());
        }
    }
}
