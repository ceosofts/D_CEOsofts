<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CustomersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ตรวจสอบการมีอยู่ของตาราง
        if (!Schema::hasTable('customers')) {
            $this->command->error("Table 'customers' does not exist, skipping seeder.");
            return;
        }

        try {
            // ตรวจสอบคอลัมน์ที่จำเป็น
            $columns = Schema::getColumnListing('customers');
            $requiredColumns = ['companyname', 'email', 'phone', 'address'];
            $missingColumns = array_diff($requiredColumns, $columns);
            
            if (!empty($missingColumns)) {
                $this->command->error("Missing columns in customers table: " . implode(', ', $missingColumns));
                return;
            }
            
            // ลูกค้าตัวอย่าง
            $customers = [
                [
                    'companyname' => 'บริษัท ตัวอย่าง จำกัด',
                    'email' => 'contact@example.com',
                    'phone' => '02-123-4567',
                    'address' => 'กรุงเทพมหานคร',
                    'taxid' => '1234567890123',
                    'branch' => 'สำนักงานใหญ่',
                    'code' => 'C001',
                    'contact_name' => 'คุณสมชาย'
                ],
                [
                    'companyname' => 'ห้างหุ้นส่วนจำกัด ตัวอย่าง',
                    'email' => 'info@example.co.th',
                    'phone' => '02-765-4321',
                    'address' => 'เชียงใหม่',
                    'taxid' => '9876543210123',
                    'branch' => 'สาขาเชียงใหม่',
                    'code' => 'C002',
                    'contact_name' => 'คุณสมหญิง'
                ]
            ];

            $count = 0;
            foreach ($customers as $customer) {
                DB::table('customers')->updateOrInsert(
                    ['email' => $customer['email']],
                    [
                        'companyname' => $customer['companyname'],
                        'phone' => $customer['phone'],
                        'address' => $customer['address'],
                        'taxid' => $customer['taxid'],
                        'branch' => $customer['branch'],
                        'code' => $customer['code'],
                        'contact_name' => $customer['contact_name'],
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );
                $count++;
            }

            $this->command->info("Successfully seeded {$count} customers");
        } catch (\Exception $e) {
            $this->command->error("Error running " . get_class($this) . ": " . $e->getMessage());
        }
    }
}
