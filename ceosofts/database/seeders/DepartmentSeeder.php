<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    public function run()
    {
        // $departments = ['ฝ่ายขาย', 'ฝ่ายการตลาด', 'ฝ่ายบัญชี', 'ฝ่ายไอที', 'ฝ่ายบุคคล'];

        $departments = ['ฝ่ายขาย', 'ฝ่ายการตลาด', 'ฝ่ายบัญชี', 'ฝ่ายไอที', 'ฝ่ายบุคคล',
        'ฝ่ายบริหาร', 'ฝ่ายวิจัยและพัฒนา', 'ฝ่ายการผลิต',
         'ฝ่ายฝึกอบรม', 'ฝ่ายความปลอดภัย', 'ฝ่ายบริการลูกค้า', 'ฝ่ายธุรการ', 'ฝ่ายขนส่ง',
          'ฝ่ายสื่อสาร', 'ฝ่ายศูนย์ข้อมูล'];

        foreach ($departments as $department) {
            Department::firstOrCreate(['name' => $department]);
        }
    }
}
