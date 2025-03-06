<?php

namespace App\Http\Controllers;

use App\Models\WorkShift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WorkShiftController extends Controller
{
    /**
     * แสดงรายการเวรทำงานทั้งหมด
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $shifts = WorkShift::all();
            return response()->json($shifts, 200);
        } catch (\Exception $e) {
            Log::error('Error fetching work shifts: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch work shifts'], 500);
        }
    }

    /**
     * บันทึกเวรทำงานใหม่
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // ตัวอย่าง validation (ปรับตามฟิลด์จริงของคุณ)
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time'   => 'required|date_format:H:i|after:start_time',
        ]);

        try {
            $shift = WorkShift::create($validated);
            return response()->json($shift, 201);
        } catch (\Exception $e) {
            Log::error('Error creating work shift: ' . $e->getMessage(), $request->all());
            return response()->json(['error' => 'Failed to create work shift'], 500);
        }
    }
}
