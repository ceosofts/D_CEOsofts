<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ItemStatus;

class ItemStatusSeeder extends Seeder
{
    public function run()
    {
        $statuses = ['กำลังส่งของ', 'ส่งแล้ว', 'เบิกผลิต', 'ยืม', 'รอการตรวจสอบ', 'พร้อมขาย', 'หมดสต็อก'];

        foreach ($statuses as $status) {
            ItemStatus::firstOrCreate(['name' => $status]);
        }
    }
}
