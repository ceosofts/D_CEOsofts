<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Unit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // ตรวจสอบการมีอยู่ของตาราง
        if (!Schema::hasTable('units')) {
            $this->command->error("Table 'units' does not exist, skipping seeder.");
            return;
        }

        try {
            // ตรวจสอบโครงสร้างตารางก่อนที่จะทำการ seed
            $columns = Schema::getColumnListing('units');
            
            // ดูว่ามีคอลัมน์อะไรบ้างในตาราง
            $this->command->info("Columns found in units table: " . implode(", ", $columns));
            
            // ตรวจสอบคอลัมน์ที่ต้องใช้
            $hasUnitNameTh = in_array('unit_name_th', $columns);
            $hasName = in_array('name', $columns);
            $hasDescription = in_array('description', $columns);
            $hasIsActive = in_array('is_active', $columns);

            // หากไม่มีคอลัมน์ที่จำเป็นให้แจ้งเตือนและหยุดการทำงาน
            if (!$hasName && !$hasUnitNameTh) {
                $this->command->error("Missing required columns: need either 'name' or 'unit_name_th' column");
                return;
            }

            $this->command->info("Seeding units with proper column structure...");

            // ข้อมูลที่จะ seed
            $units = [
                ['thai_name' => 'ชิ้น', 'eng_name' => 'Piece', 'description' => 'หน่วยวัดสำหรับสินค้าที่นับเป็นชิ้น'],
                ['thai_name' => 'อัน', 'eng_name' => 'Item', 'description' => 'หน่วยวัดสำหรับสินค้าที่นับเป็นอัน'],
                ['thai_name' => 'กล่อง', 'eng_name' => 'Box', 'description' => 'หน่วยวัดสำหรับสินค้าที่นับเป็นกล่อง'],
                ['thai_name' => 'แพ็ค', 'eng_name' => 'Pack', 'description' => 'หน่วยวัดสำหรับสินค้าที่นับเป็นแพ็ค'],
                ['thai_name' => 'เมตร', 'eng_name' => 'Meter', 'description' => 'หน่วยวัดสำหรับสินค้าที่วัดเป็นเมตร'],
                ['thai_name' => 'กิโลกรัม', 'eng_name' => 'Kilogram', 'description' => 'หน่วยวัดสำหรับสินค้าที่ชั่งน้ำหนักเป็นกิโลกรัม'],
                ['thai_name' => 'ลิตร', 'eng_name' => 'Liter', 'description' => 'หน่วยวัดสำหรับสินค้าประเภทของเหลว'],
            ];

            // ทำการ seed ข้อมูล
            $seededCount = 0;
            foreach ($units as $unit) {
                $data = [];
                
                if ($hasName) {
                    $data['name'] = $unit['thai_name'];
                }
                
                if ($hasUnitNameTh) {
                    $data['unit_name_th'] = $unit['thai_name'];
                }
                
                if (isset($unit['eng_name']) && $hasUnitNameTh) {
                    $data['unit_name_en'] = $unit['eng_name'];
                }
                
                if ($hasDescription && isset($unit['description'])) {
                    $data['description'] = $unit['description'];
                }
                
                if ($hasIsActive) {
                    $data['is_active'] = true;
                }
                
                // เพิ่ม unit_code หากมีคอลัมน์นี้
                if (in_array('unit_code', $columns)) {
                    $data['unit_code'] = strtoupper(substr($unit['eng_name'], 0, 3));
                }
                
                // ไม่ seed ถ้าข้อมูลที่จะใส่ว่างเปล่า
                if (!empty($data)) {
                    // สร้างเงื่อนไขการค้นหาตามโครงสร้างข้อมูลที่มี
                    $condition = $hasUnitNameTh ? ['unit_name_th' => $unit['thai_name']] : ['name' => $unit['thai_name']];
                    
                    // เพิ่มข้อมูลการสร้างและแก้ไข
                    $data['created_at'] = now();
                    $data['updated_at'] = now();
                    
                    // ใช้ updateOrCreate เพื่อป้องกันการซ้ำ
                    DB::table('units')->updateOrInsert($condition, $data);
                    $seededCount++;
                }
            }

            $this->command->info("Successfully seeded {$seededCount} new units");
            
        } catch (\Exception $e) {
            $this->command->error("Error running " . get_class($this) . ": " . $e->getMessage());
        }
    }
}
