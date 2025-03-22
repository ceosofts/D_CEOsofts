<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear any existing attendance records to avoid constraint violations
        DB::table('attendances')->delete();
        
        $employees = Employee::all();
        if ($employees->isEmpty()) {
            $this->command->info('No employees found. Skipping attendance seeding.');
            return;
        }

        $employee = $employees->first(); // Use the first employee for sample data
        
        // Create attendance records for the last 10 days
        $startDate = Carbon::now()->subDays(10);
        
        for ($i = 0; $i < 10; $i++) {
            $date = $startDate->copy()->addDays($i);
            
            // Skip weekends
            if ($date->isWeekend()) {
                continue;
            }
            
            // Random check in time between 8:00 AM and 9:30 AM
            $checkInHour = rand(8, 9);
            $checkInMinute = $checkInHour == 9 ? rand(0, 30) : rand(0, 59);
            $checkIn = $date->copy()->setTime($checkInHour, $checkInMinute);
            
            // Random check out time between 5:00 PM and 6:30 PM
            $checkOutHour = rand(17, 18);
            $checkOutMinute = $checkOutHour == 18 ? rand(0, 30) : rand(0, 59);
            $checkOut = $date->copy()->setTime($checkOutHour, $checkOutMinute);
            
            // Calculate work hours
            $workHours = $checkIn->diffInHours($checkOut);
            
            // Create the attendance record
            Attendance::create([
                'employee_id' => $employee->id,
                'date' => $date->toDateString(),
                'check_in' => $checkIn->toDateTimeString(),
                'check_out' => $checkOut->toDateTimeString(),
                'status' => 'normal',
                'work_hours' => $workHours,
                'overtime_hours' => rand(0, 2),
                'work_hours_completed' => true,
            ]);
        }

        $this->command->info('Successfully seeded attendance records');
    }
}
