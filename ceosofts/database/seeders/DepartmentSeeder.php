<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ตรวจสอบการมีอยู่ของตาราง
        if (!Schema::hasTable('departments')) {
            $this->command->error("Table 'departments' does not exist, skipping seeder.");
            return;
        }

        try {
            // ตรวจสอบคอลัมน์ที่จำเป็น
            $columns = Schema::getColumnListing('departments');
            $requiredColumns = ['name'];
            $missingColumns = array_diff($requiredColumns, $columns);
            
            if (!empty($missingColumns)) {
                $this->command->error("Missing columns in departments table: " . implode(', ', $missingColumns));
                return;
            }
            
            // รายชื่อแผนกตัวอย่าง
            $departments = [
                ['name' => 'ฝ่ายบริหาร'],
                ['name' => 'ฝ่ายการตลาด'],
                ['name' => 'ฝ่ายขาย'],
                ['name' => 'ฝ่ายบัญชี'],
                ['name' => 'ฝ่ายไอที'],
                ['name' => 'ฝ่ายผลิต'],
                ['name' => 'ฝ่ายจัดซื้อ'],
                ['name' => 'ฝ่ายคลังสินค้า'],
                ['name' => 'ฝ่ายบุคคล']
            ];

            $count = 0;
            foreach ($departments as $department) {
                DB::table('departments')->updateOrInsert(
                    ['name' => $department['name']],
                    ['created_at' => now(), 'updated_at' => now()]
                );
                $count++;
            }

            $this->command->info("Successfully seeded {$count} departments");
        } catch (\Exception $e) {
            $this->command->error("Error running " . get_class($this) . ": " . $e->getMessage());
        }
    }
}
