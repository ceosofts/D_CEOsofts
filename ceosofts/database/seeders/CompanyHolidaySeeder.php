<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CompanyHoliday;

class CompanyHolidaySeeder extends Seeder
{
    public function run(): void
    {
        $holidays = [
            ['date' => '2025-01-01', 'name' => 'วันขึ้นปีใหม่'],
            ['date' => '2025-04-13', 'name' => 'วันสงกรานต์'],
            ['date' => '2025-12-05', 'name' => 'วันพ่อแห่งชาติ'],
            ['date' => '2025-12-10', 'name' => 'วันรัฐธรรมนูญ'],
            ['date' => '2025-12-25', 'name' => 'วันคริสต์มาส'],
            

            ['date' => '2026-01-01', 'name' => 'วันขึ้นปีใหม่'],
            ['date' => '2026-04-13', 'name' => 'วันสงกรานต์'],
            ['date' => '2026-12-05', 'name' => 'วันพ่อแห่งชาติ'],
            ['date' => '2026-12-10', 'name' => 'วันรัฐธรรมนูญ'],
            ['date' => '2026-12-25', 'name' => 'วันคริสต์มาส'],
            ['date' => '2026-12-01', 'name' => 'วันขึ้นปีใหม่']

        ];

        foreach ($holidays as $holiday) {
            CompanyHoliday::create($holiday);
        }
    }
}
