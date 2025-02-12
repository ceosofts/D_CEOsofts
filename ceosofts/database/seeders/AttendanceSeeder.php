<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceSeeder extends Seeder
{
    public function run()
    {
        Employee::chunk(50, function ($employees) {
            foreach ($employees as $employee) {
                $attendances = [];

                for ($i = 1; $i <= 10; $i++) {
                    $date = Carbon::now()->subDays($i)->format('Y-m-d');

                    // ✅ กำหนดเวลาเข้างานและออกงานแบบสุ่ม
                    $checkIn = Carbon::parse($date . ' ' . $this->randomMinute(8, 9));
                    $checkOut = Carbon::parse($date . ' ' . $this->randomMinute(17, 18));

                    // ✅ คำนวณชั่วโมงการทำงาน
                    $workHours = max(round(($checkOut->timestamp - $checkIn->timestamp) / 3600, 2), 0);
                    $workHoursCompleted = $workHours >= 8;
                    $overtimeHours = max($workHours - 8, 0);

                    $attendances[] = [
                        'employee_id' => $employee->id,
                        'date' => $date,
                        'check_in' => $checkIn,
                        'check_out' => $checkOut,
                        'work_hours' => $workHours,
                        'work_hours_completed' => $workHoursCompleted,
                        'overtime_hours' => $overtimeHours,
                        'status' => 'normal',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                Attendance::insert($attendances); // ✅ ใช้ insert ทีเดียวเร็วกว่า create ทีละรายการ
            }
        });

        // echo "✅ Attendance records seeded successfully!\n";
    }

    /**
     * ฟังก์ชันช่วยสุ่มเวลาแบบ HH:mm:ss
     */
    private function randomMinute($hourStart, $hourEnd)
    {
        return sprintf('%02d:%02d:%02d', rand($hourStart, $hourEnd), rand(0, 59), 0);
    }
}
