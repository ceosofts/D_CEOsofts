<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TaxSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ตรวจสอบว่าตารางมีอยู่จริงหรือไม่
        if (!Schema::hasTable('tax_settings')) {
            echo "Table 'tax_settings' does not exist!\n";
            return;
        }

        try {
            // ข้อมูลอัตราภาษีต่าง ๆ
            $taxSettings = [
                [
                    'name' => 'ภาษีมูลค่าเพิ่ม',
                    'rate' => 7.00,
                    'is_active' => true
                ],
                [
                    'name' => 'ภาษีค่าจ้างทำ',
                    'rate' => 3.00,
                    'is_active' => true
                ],
                [
                    'name' => 'ภาษีหัก ณ ที่จ่าย',
                    'rate' => 1.00,
                    'is_active' => true
                ]
            ];

            foreach ($taxSettings as $taxSetting) {
                DB::table('tax_settings')->updateOrInsert(
                    ['name' => $taxSetting['name'], 'rate' => $taxSetting['rate']],
                    [
                        'is_active' => $taxSetting['is_active'],
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );
            }
        } catch (\Exception $e) {
            echo "Error seeding tax settings: " . $e->getMessage() . "\n";
        }
    }
}
