<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TaxSetting;

class TaxSettingSeeder extends Seeder
{
    public function run(): void
    {
        $taxes = [
            ['name' => 'ภาษีค่าจ้างทำ', 'rate' => 3.00],
            ['name' => 'ภาษีค่าขนส่ง (บุคคลธรรมดา)', 'rate' => 1.00],
            ['name' => 'ค่าเช่า (บุคคลธรรมดา)', 'rate' => 5.00],
            ['name' => 'ค่าเช่า (นิติบุคคล)', 'rate' => 3.00],
            ['name' => 'ภาษีโฆษณา', 'rate' => 2.00],
            ['name' => 'ภาษีดอกเบี้ยเงินปันผล', 'rate' => 10.00],
            ['name' => 'ภาษีมูลค่าเพิ่ม', 'rate' => 7.00],
        ];

        foreach ($taxes as $tax) {
            TaxSetting::firstOrCreate($tax);
        }
    }
}
