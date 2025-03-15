<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobStatus;
use Illuminate\Http\Request;

class JobStatusController extends Controller
{
    public function index()
    {
        $statuses = JobStatus::orderBy('sort_order')->get();
        return view('jobstatus.jobstatus-index', compact('statuses'));
    }

    public function create()
    {
        return view('jobstatus.jobstatus-create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:7',
            'sort_order' => 'required|integer|min:0',
            'is_active' => 'boolean'
        ]);

        JobStatus::create($validated);

        return redirect()->route('admin.job-statuses.index')
            ->with('success', 'Job status created successfully');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(JobStatus $jobStatus)
    {
        return view('jobstatus.jobstatus-edit', compact('jobStatus'));
    }

    public function update(Request $request, JobStatus $jobStatus)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:7',
            'sort_order' => 'required|integer|min:0',
            'is_active' => 'boolean'
        ]);

        $jobStatus->update($validated);

        return redirect()->route('admin.job-statuses.index')
            ->with('success', 'Job status updated successfully');
    }

    public function destroy(JobStatus $jobStatus)
    {
        $jobStatus->delete();
        return redirect()->route('admin.job-statuses.index')
            ->with('success', 'Job status deleted successfully');
    }
}
