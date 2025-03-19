<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ตรวจสอบการมีอยู่ของตาราง
        if (!Schema::hasTable('companies')) {
            $this->command->error("Table 'companies' does not exist, skipping seeder.");
            return;
        }

        try {
            // ตรวจสอบคอลัมน์ที่จำเป็น
            $columns = Schema::getColumnListing('companies');
            $requiredColumns = ['company_name', 'address', 'phone', 'email'];
            $missingColumns = array_diff($requiredColumns, $columns);
            
            if (!empty($missingColumns)) {
                $this->command->error("Missing columns in companies table: " . implode(', ', $missingColumns));
                return;
            }

            // ข้อมูลบริษัทตัวอย่าง
            $companyData = [
                'company_name' => 'บริษัท ซีอีโอซอฟท์ จำกัด',
                'address' => '123 ถนนตัวอย่าง แขวงตัวอย่าง เขตตัวอย่าง กรุงเทพมหานคร 10110',
                'phone' => '02-123-4567',
                'mobile' => '081-234-5678',
                'fax' => '02-123-4568',
                'email' => 'info@ceosofts.com',
                'website' => 'https://www.ceosofts.com',
                'tax_id' => '1234567890123',
                'contact_person' => 'คุณผู้บริหาร',
                'branch' => 'สำนักงานใหญ่',
                'logo' => 'logo.png',
                'facebook' => 'https://www.facebook.com/ceosofts',
                'line' => '@ceosofts',
            ];

            // เพิ่มหรืออัพเดทข้อมูลบริษัท
            DB::table('companies')->updateOrInsert(
                ['company_name' => $companyData['company_name']], // คีย์หลักที่ใช้ในการตรวจสอบการซ้ำ
                array_merge($companyData, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );

            $this->command->info('Successfully seeded company information');
        } catch (\Exception $e) {
            $this->command->error("Error running " . get_class($this) . ": " . $e->getMessage());
        }
    }
}
