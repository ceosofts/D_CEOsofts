<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JobStatus;

class JobStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['name' => 'รอดำเนินการ', 'color' => '#FFA500', 'sort_order' => 1],
            ['name' => 'ระหว่างรออนุมัติจากลูกค้า', 'color' => '#0000FF', 'sort_order' => 2],
            ['name' => 'ลูกค้าอนุมัติใบเสนอราคา', 'color' => '#008000', 'sort_order' => 3],
            ['name' => 'ยกเลิก', 'color' => '#FF0000', 'sort_order' => 4],
            ['name' => 'กำลังวางบิล', 'color' => '#FFA500', 'sort_order' => 5],
            ['name' => 'ชำระเงินมัดจำแล้ว', 'color' => '#008000', 'sort_order' => 6],
            ['name' => 'ชำระเงินเต็มจำนวนแล้ว', 'color' => '#008000', 'sort_order' => 7],
            ['name' => 'ส่งสินค้าแล้ว', 'color' => '#008000', 'sort_order' => 8],
            ['name' => 'ยังไม่จ่าย', 'color' => '#FF0000', 'sort_order' => 9],
            ['name' => 'จ่ายยังไม่ครบ', 'color' => '#FF0000', 'sort_order' => 10],
            ['name' => 'เสร็จสิ้น', 'color' => '#008000', 'sort_order' => 11],

        ];

        foreach ($statuses as $status) {
            JobStatus::create($status);
        }
    }
}
