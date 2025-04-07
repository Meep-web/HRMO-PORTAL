<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\Employment;
use App\Models\PersonalInfo;
use App\Models\Department;
use Illuminate\Support\Facades\File;
use App\Models\Designation;
use App\Models\SalaryGrade;



class NoticeOfSalaryAdjustmentController extends Controller
{
    // Show the form
    public function show()
    {
        $employees = PersonalInfo::select('id', 'first_name', 'middle_name', 'last_name')
            ->get()
            ->map(function ($employee) {
                $employment = Employment::where('personalID', $employee->id)->first();

                return [
                    'id' => $employee->id,
                    'name' => trim("{$employee->first_name} {$employee->middle_name} {$employee->last_name}"),
                    'department' => $employment?->department_id
                        ? Department::find($employment->department_id)?->department_name ?? 'N/A'
                        : 'N/A',
                    'updatedBy' => $employment?->updatedBy ?? 'N/A',
                    'updated_at' => $employment?->updated_at ?? 'N/A',
                ];
            });

        // Log or pass to view
        logger($employees);

        return view('noticeOfSalaryAdjustment', ['employees' => $employees]);
    }

    public function showSalaryChanges(Request $request)
    {
        // Get employee ID from request
        $employeeId = $request->input('employeeId');
    
        // Path to the JSON file
        $jsonFilePath = storage_path('app/employment_changes.json');
    
        // Check if the file exists
        if (!File::exists($jsonFilePath)) {
            return response()->json(['error' => 'JSON file not found.'], 404);
        }
    
        // Get the content of the JSON file
        $jsonContent = File::get($jsonFilePath);
    
        // Decode the JSON content into an array
        $changes = json_decode($jsonContent, true);
    
        // Filter the data based on employeeId
        $employeeChanges = [];
        foreach ($changes as $change) {
            if ($change['employee_id'] == $employeeId) {
                $employeeChanges[] = $change;
            }
        }
    
        // Get all departments
        $departments = Department::all();
    
        // Return both employee changes and departments as JSON
        return response()->json([
            'employeeChanges' => $employeeChanges,
            'departments' => $departments
        ]);
    }

    public function refactorData(Request $request)
    {
        // Get the employee changes data from the request
        $employeeChanges = $request->all();

        // Get the department name based on the department_id
        $department = Department::find($employeeChanges['department_id']);
        $departmentName = $department ? $department->department_name : 'N/A';

        // Get the designation based on the department_id
        $designation = Designation::where('department_id', $employeeChanges['department_id'])->first();
        $designationName = $designation ? $designation->designation : 'N/A';

        // Get the employee name using the employee_id
        $employeeInfo = PersonalInfo::find($employeeChanges['employee_id']);
        $employeeName = $employeeInfo ? $employeeInfo->first_name . ' ' . $employeeInfo->last_name : 'N/A';

        // Format the date of effectivity and date released
        $dateOfEffectivity = Carbon::parse($employeeChanges['dateOfEffectivity']['new'])->format('F d, Y');
        $dateReleased = Carbon::parse($employeeChanges['dateReleased']['new'])->format('F d, Y');

        // Get salary grade and step increment for both new and old
        $salaryGradeNew = $employeeChanges['salaryGrade']['new'];
        $stepIncrementNew = $employeeChanges['stepIncrement']['new'];
        $salaryGradeOld = $employeeChanges['salaryGrade']['old'];
        $stepIncrementOld = $employeeChanges['stepIncrement']['old'];

        // Get the corresponding salary values for new salary grade and step increment
        $salaryRowNew = SalaryGrade::find($salaryGradeNew);
        $salaryNew = null;
        if ($salaryRowNew) {
            $stepColumnNew = "step" . $stepIncrementNew;  // e.g., 'step1', 'step2', etc.
            $salaryNew = $salaryRowNew->$stepColumnNew;  // Dynamically access the step column
        }

        // Get the corresponding salary values for old salary grade and step increment
        $salaryRowOld = SalaryGrade::find($salaryGradeOld);
        $salaryOld = null;
        if ($salaryRowOld) {
            $stepColumnOld = "step" . $stepIncrementOld;  // e.g., 'step1', 'step2', etc.
            $salaryOld = $salaryRowOld->$stepColumnOld;  // Dynamically access the step column
        }

       

        // Return the necessary data for the response
        return response()->json([
            'department' => $departmentName,
            'designation' => $designationName,
            'employeeName' => $employeeName,
            'dateOfEffectivity' => $dateOfEffectivity,
            'dateReleased' => $dateReleased,
            'newSalary' => $salaryNew,  // Return the new salary
            'oldSalary' => $salaryOld   // Return the old salary
        ]);
    }
}
