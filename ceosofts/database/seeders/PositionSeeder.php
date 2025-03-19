<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ตรวจสอบการมีอยู่ของตาราง
        if (!Schema::hasTable('positions')) {
            $this->command->error("Table 'positions' does not exist, skipping seeder.");
            return;
        }

        try {
            // ตรวจสอบคอลัมน์ที่จำเป็น
            $columns = Schema::getColumnListing('positions');
            $requiredColumns = ['name', 'code', 'description'];
            $missingColumns = array_diff($requiredColumns, $columns);
            
            if (!empty($missingColumns)) {
                $this->command->error("Missing columns in positions table: " . implode(', ', $missingColumns));
                return;
            }
            
            // รายชื่อตำแหน่งตัวอย่าง
            $positions = [
                ['name' => 'ผู้จัดการทั่วไป', 'code' => 'GM', 'description' => 'General Manager'],
                ['name' => 'ผู้จัดการฝ่าย', 'code' => 'M', 'description' => 'Manager'],
                ['name' => 'หัวหน้างาน', 'code' => 'S', 'description' => 'Supervisor'],
                ['name' => 'พนักงาน', 'code' => 'E', 'description' => 'Employee']
            ];

            $count = 0;
            foreach ($positions as $position) {
                DB::table('positions')->updateOrInsert(
                    ['name' => $position['name']],
                    [
                        'code' => $position['code'],
                        'description' => $position['description'],
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );
                $count++;
            }

            $this->command->info("Successfully seeded {$count} positions");
        } catch (\Exception $e) {
            $this->command->error("Error running " . get_class($this) . ": " . $e->getMessage());
        }
    }
}
