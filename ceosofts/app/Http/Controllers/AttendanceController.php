<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AttendanceController extends Controller
{
    /**
     * แสดงรายการการลงเวลาทำงาน
     */
    public function index()
    {
        $attendances = Attendance::with('employee')
            ->orderBy('date', 'desc')
            ->paginate(10);
        return \view('attendances.index', compact('attendances'));
    }

    /**
     * แสดงฟอร์มลงเวลาทำงานใหม่
     */
    public function create()
    {
        $employees = Employee::all();
        return \view('attendances.create', compact('employees'));
    }

    /**
     * บันทึกการลงเวลาทำงานใหม่ลงในฐานข้อมูล
     */
    public function store(Request $request)
    {
        $validatedData = $this->validateAttendance($request);

        try {
            // คำนวณชั่วโมงทำงานและ OT จากข้อมูลที่รับมา
            $this->calculateWorkHours($validatedData);

            Attendance::create($validatedData);

            return \redirect()->route('attendances.index')
                ->with('success', 'Attendance recorded successfully!');
        } catch (\Exception $e) {
            Log::error('Error storing attendance: ' . $e->getMessage(), $request->all());
            return \back()->withErrors(['error' => 'Error recording attendance: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * แสดงฟอร์มแก้ไขข้อมูลการลงเวลาทำงาน
     */
    public function edit($id)
    {
        $attendance = Attendance::findOrFail($id);
        $employees = Employee::all();
        return \view('attendances.edit', compact('attendance', 'employees'));
    }

    /**
     * อัปเดตการลงเวลาทำงาน
     */
    public function update(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);
        $validatedData = $this->validateAttendance($request);

        try {
            $this->calculateWorkHours($validatedData);

            $attendance->update($validatedData);

            return \redirect()->route('attendances.index')
                ->with('success', 'Attendance updated successfully!');
        } catch (\Exception $e) {
            Log::error('Error updating attendance: ' . $e->getMessage(), $request->all());
            return \back()->withErrors(['error' => 'Error updating attendance: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * ลบข้อมูลการลงเวลาทำงาน
     */
    public function destroy($id)
    {
        try {
            Attendance::findOrFail($id)->delete();
            return \redirect()->route('attendances.index')
                ->with('success', 'Attendance deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Error deleting attendance: ' . $e->getMessage());
            return \back()->withErrors(['error' => 'Error deleting attendance: ' . $e->getMessage()]);
        }
    }

    /**
     * Validate ข้อมูลการลงเวลาทำงาน
     */
    private function validateAttendance(Request $request)
    {
        return $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date'        => 'required|date',
            'check_in'    => 'nullable|date_format:Y-m-d\TH:i',
            'check_out'   => 'nullable|date_format:Y-m-d\TH:i',
        ]);
    }

    /**
     * คำนวณชั่วโมงทำงานและ OT จากข้อมูล check_in และ check_out
     *
     * @param array &$data ข้อมูลที่ได้รับจาก validate
     */
    private function calculateWorkHours(&$data)
    {
        if (!empty($data['check_in']) && !empty($data['check_out'])) {
            $checkIn = Carbon::parse($data['check_in']);
            $checkOut = Carbon::parse($data['check_out']);

            if ($checkIn->gt($checkOut)) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'check_out' => 'Check-out time must be after Check-in time.',
                ]);
            }

            // คำนวณชั่วโมงทำงาน (เป็นชั่วโมง)
            $workHours = max($checkIn->diffInMinutes($checkOut) / 60, 0);
            $data['work_hours'] = round($workHours, 2);
            // ตรวจสอบว่าทำงานครบ 8 ชั่วโมงหรือไม่
            $data['work_hours_completed'] = $workHours >= 8;
            // คำนวณ OT (เฉพาะชั่วโมงที่เกิน 8 ชั่วโมง)
            $data['overtime_hours'] = max($workHours - 8, 0);
        } else {
            $data['work_hours'] = null;
            $data['work_hours_completed'] = false;
            $data['overtime_hours'] = 0;
        }
    }
}
