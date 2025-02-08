<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        // ✅ เพิ่มข้อมูลบริษัท ถ้ายังไม่มี
        DB::table('companies')->updateOrInsert(
            ['branch' => '00000'], // เช็คว่ามีข้อมูลนี้หรือยัง
            [
                'company_name'       => 'บริษัท ABC จำกัด',
                'address'            => '123 ถนนตัวอย่าง แขวงตัวอย่าง เขตตัวอย่าง กรุงเทพฯ 10110',
                'phone'              => '02-123-4567',
                'mobile'             => '081-234-5678',
                'fax'                => '02-987-6543',
                'email'              => 'contact@abc.com',
                'website'            => 'https://www.abc.com',
                'logo'               => 'default-logo.png',
                'twitter'            => 'https://twitter.com/abc_company',
                'instagram'          => 'https://instagram.com/abc_company',
                'linkedin'           => 'https://linkedin.com/company/abc',
                'youtube'            => 'https://youtube.com/abc_company',
                'tiktok'             => 'https://tiktok.com/@abc_company',
                'facebook'           => 'https://facebook.com/abc_company',
                'line'               => '@abc_company',
                'tax_id'             => '1234567890123',
                'contact_person'     => 'คุณสมชาย แซ่ลี้',
                'branch'             => '00000', // ✅ รหัสสาขา
                'branch_description' => 'สำนักงานใหญ่', // ✅ คำอธิบายสาขา
                'created_at'         => now(),
                'updated_at'         => now(),
            ]
        );
    }
}
