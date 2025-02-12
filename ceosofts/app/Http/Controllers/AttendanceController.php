<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        $attendances = Attendance::with('employee')->orderBy('date', 'desc')->paginate(10);
        return view('attendances.index', compact('attendances'));
    }

    public function create()
    {
        $employees = Employee::all();
        return view('attendances.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $validatedData = $this->validateAttendance($request);

        // ✅ คำนวณเวลาทำงาน
        $this->calculateWorkHours($validatedData);

        Attendance::create($validatedData);

        return redirect()->route('attendances.index')->with('success', 'Attendance recorded successfully!');
    }

    public function edit($id)
    {
        $attendance = Attendance::findOrFail($id);
        $employees = Employee::all();
        return view('attendances.edit', compact('attendance', 'employees'));
    }

    public function update(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);
        $validatedData = $this->validateAttendance($request);

        // ✅ คำนวณเวลาทำงาน
        $this->calculateWorkHours($validatedData);

        $attendance->update($validatedData);

        return redirect()->route('attendances.index')->with('success', 'Attendance updated successfully!');
    }

    public function destroy($id)
    {
        Attendance::findOrFail($id)->delete();
        return redirect()->route('attendances.index')->with('success', 'Attendance deleted successfully!');
    }

    /**
     * ✅ ฟังก์ชันสำหรับ Validate ข้อมูล Attendance
     */
    private function validateAttendance(Request $request)
    {
        return $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'check_in' => 'nullable|date_format:Y-m-d\TH:i',
            'check_out' => 'nullable|date_format:Y-m-d\TH:i',
        ]);
    }

    /**
     * ✅ ฟังก์ชันคำนวณชั่วโมงทำงานและ OT
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

            // ✅ คำนวณชั่วโมงการทำงาน
            $workHours = max($checkIn->diffInMinutes($checkOut) / 60, 0);
            $data['work_hours'] = round($workHours, 2);
            $data['work_hours_completed'] = $workHours >= 8;
            $data['overtime_hours'] = max($workHours - 8, 0);
        } else {
            $data['work_hours'] = null;
            $data['work_hours_completed'] = false;
            $data['overtime_hours'] = 0;
        }
    }
}
