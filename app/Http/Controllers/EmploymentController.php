<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employment;
use App\Models\PersonalInfo;
use App\Models\Department; // Import Department model


class EmploymentController extends Controller
{
    public function index()
    {
        $employmentData = PersonalInfo::leftJoin('employment', 'personal_info.id', '=', 'employment.personalID')
            ->select('personal_info.id', 'personal_info.first_name', 'personal_info.last_name', 'employment.department_id', 'employment.stepIncrement', 'employment.salaryGrade')
            ->get();

        $departments = Department::all(); // Fetch all departments

        return view('employmentStatus', compact('employmentData', 'departments'));
    }

    public function store(Request $request)
{
    $request->validate([
        'employeeId' => 'required|exists:personal_info,id',
        'department' => 'required|exists:departments,id',
        'salaryGrade' => 'required|integer|min:1|max:33',
        'stepIncrement' => 'required|integer|min:1|max:8',
    ]);

    try {
        // Check if employment record already exists for the employee
        $employment = Employment::where('personalID', $request->employeeId)->first();

        if ($employment) {
            // Update existing employment record
            $employment->update([
                'department_id' => $request->department,
                'salaryGrade' => $request->salaryGrade,
                'stepIncrement' => $request->stepIncrement,
                'updatedBy' => session('employeeName'),
            ]);
        } else {
            // Create new employment record
            $employment = Employment::create([
                'personalID' => $request->employeeId,
                'department_id' => $request->department,
                'salaryGrade' => $request->salaryGrade,
                'stepIncrement' => $request->stepIncrement,
                'updatedBy' => session('employeeName'),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Employment assignment saved successfully!',
            'data' => $employment
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to assign employment.',
            'error' => $e->getMessage()
        ]);
    }
}


}
