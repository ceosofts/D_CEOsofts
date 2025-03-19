<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!Schema::hasTable('attendances')) {
            $this->command->error("Table 'attendances' does not exist, skipping seeder.");
            return;
        }

        if (!Schema::hasTable('employees') || DB::table('employees')->count() == 0) {
            $this->command->error("No employees found. Please seed employees first.");
            return;
        }

        try {
            // ข้อมูลการเข้างานตัวอย่างสำหรับพนักงาน ID 1
            // สร้างข้อมูลการเข้างาน 10 วันย้อนหลัง
            $attendanceData = [];
            $employeeId = 1; // เลือกพนักงานคนแรก
            
            for ($i = 0; $i < 10; $i++) {
                $date = now()->subDays($i + 1)->format('Y-m-d');
                $checkInHour = rand(8, 9);
                $checkInMinute = rand(0, 59);
                $checkIn = now()->subDays($i + 1)->setTime($checkInHour, $checkInMinute, 0);
                
                // เวลาเลิกงานปกติคือ 17:00-19:00
                $checkOutHour = rand(17, 18);
                $checkOutMinute = rand(0, 59);
                $checkOut = now()->subDays($i + 1)->setTime($checkOutHour, $checkOutMinute, 0);
                
                // คำนวณชั่วโมงการทำงานและ overtime
                $workHours = $checkOut->diffInMinutes($checkIn) / 60;
                $workHours = round($workHours, 2);
                $overtimeHours = max(0, $workHours - 8);
                
                $attendanceData[] = [
                    'employee_id' => $employeeId,
                    'date' => $date,
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                    'status' => 'normal',
                    'work_hours' => $workHours,
                    'overtime_hours' => $overtimeHours,
                    'work_hours_completed' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
            
            DB::table('attendances')->insert($attendanceData);
            
            $this->command->info("Successfully seeded attendance records");
        } catch (\Exception $e) {
            $this->command->error("Error running " . get_class($this) . ": " . $e->getMessage());
        }
    }
}
