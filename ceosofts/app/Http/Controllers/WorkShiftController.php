<?php
namespace App\Http\Controllers;

use App\Models\WorkShift;
use Illuminate\Http\Request;

class WorkShiftController extends Controller
{
    public function index()
    {
        $shifts = WorkShift::all();
        return response()->json($shifts);
    }

    public function store(Request $request)
    {
        $shift = WorkShift::create($request->all());
        return response()->json($shift, 201);
    }
}
