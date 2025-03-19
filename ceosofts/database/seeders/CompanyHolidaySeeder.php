<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class CompanyHolidaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!Schema::hasTable('company_holidays')) {
            $this->command->error("Table company_holidays does not exist, skipping seeder.");
            return;
        }

        try {
            $currentYear = Carbon::now()->year;
            
            // วันหยุดประจำปีของไทย
            $holidays = [
                // วันหยุดราชการและวันนักขัตฤกษ์ประจำปี 2023-2024 (ตัวอย่าง)
                [
                    'holiday_name' => 'วันปีใหม่',
                    'holiday_date' => $currentYear . '-01-01',
                    'description' => 'วันขึ้นปีใหม่สากล',
                    'is_annual' => true,
                    'holiday_year' => null
                ],
                [
                    'holiday_name' => 'วันแรงงานแห่งชาติ',
                    'holiday_date' => $currentYear . '-05-01',
                    'description' => 'วันแรงงานแห่งชาติ',
                    'is_annual' => true,
                    'holiday_year' => null
                ],
                [
                    'holiday_name' => 'วันสงกรานต์',
                    'holiday_date' => $currentYear . '-04-13',
                    'description' => 'วันสงกรานต์ (วันครอบครัว)',
                    'is_annual' => true,
                    'holiday_year' => null
                ],
                [
                    'holiday_name' => 'วันสงกรานต์',
                    'holiday_date' => $currentYear . '-04-14',
                    'description' => 'วันสงกรานต์',
                    'is_annual' => true,
                    'holiday_year' => null
                ],
                [
                    'holiday_name' => 'วันสงกรานต์',
                    'holiday_date' => $currentYear . '-04-15',
                    'description' => 'วันสงกรานต์',
                    'is_annual' => true,
                    'holiday_year' => null
                ],
                [
                    'holiday_name' => 'วันเฉลิมพระชนมพรรษาพระบาทสมเด็จพระปรเมนทรรามาธิบดีศรีสินทรมหาวชิราลงกรณฯ พระวชิรเกล้าเจ้าอยู่หัว',
                    'holiday_date' => $currentYear . '-07-28',
                    'description' => 'วันเฉลิมพระชนมพรรษา ร.10',
                    'is_annual' => true,
                    'holiday_year' => null
                ],
                [
                    'holiday_name' => 'วันเฉลิมพระชนมพรรษาสมเด็จพระนางเจ้าสิริกิติ์ พระบรมราชินีนาถ พระบรมราชชนนีพันปีหลวง',
                    'holiday_date' => $currentYear . '-08-12',
                    'description' => 'วันแม่แห่งชาติ',
                    'is_annual' => true,
                    'holiday_year' => null
                ],
                [
                    'holiday_name' => 'วันคล้ายวันสวรรคตของพระบาทสมเด็จพระบรมชนกาธิเบศร มหาภูมิพลอดุลยเดชมหาราชฯ',
                    'holiday_date' => $currentYear . '-10-13',
                    'description' => 'วันคล้ายวันสวรรคต ร.9',
                    'is_annual' => true,
                    'holiday_year' => null
                ],
                [
                    'holiday_name' => 'วันปิยมหาราช',
                    'holiday_date' => $currentYear . '-10-23',
                    'description' => 'วันคล้ายวันสวรรคตของพระบาทสมเด็จพระจุลจอมเกล้าเจ้าอยู่หัว',
                    'is_annual' => true,
                    'holiday_year' => null
                ],
                [
                    'holiday_name' => 'วันคล้ายวันเฉลิมพระชนมพรรษาของพระบาทสมเด็จพระบรมชนกาธิเบศรฯ',
                    'holiday_date' => $currentYear . '-12-05',
                    'description' => 'วันพ่อแห่งชาติ',
                    'is_annual' => true,
                    'holiday_year' => null
                ],
                [
                    'holiday_name' => 'วันรัฐธรรมนูญ',
                    'holiday_date' => $currentYear . '-12-10',
                    'description' => 'วันรัฐธรรมนูญ',
                    'is_annual' => true,
                    'holiday_year' => null
                ],
                [
                    'holiday_name' => 'วันสิ้นปี',
                    'holiday_date' => $currentYear . '-12-31',
                    'description' => 'วันสิ้นปี',
                    'is_annual' => true,
                    'holiday_year' => null
                ],
                
                // วันหยุดพิเศษ บริษัท (ตัวอย่าง)
                [
                    'holiday_name' => 'วันหยุดพิเศษบริษัท',
                    'holiday_date' => $currentYear . '-09-15',
                    'description' => 'วันครบรอบก่อตั้งบริษัท',
                    'is_annual' => false,
                    'holiday_year' => $currentYear
                ],
            ];
            
            foreach ($holidays as $holiday) {
                DB::table('company_holidays')->updateOrInsert(
                    [
                        'holiday_date' => $holiday['holiday_date'],
                        'holiday_year' => $holiday['holiday_year']
                    ],
                    [
                        'holiday_name' => $holiday['holiday_name'],
                        'description' => $holiday['description'],
                        'is_annual' => $holiday['is_annual'],
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
            
            $this->command->info('Successfully seeded company holidays.');
        } catch (\Exception $e) {
            $this->command->error("Error running " . get_class($this) . ": " . $e->getMessage());
        }
    }
}
