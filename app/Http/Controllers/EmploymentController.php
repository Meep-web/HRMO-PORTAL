<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employment;
use App\Models\PersonalInfo;
use App\Models\Department; // Import Department model
use App\Models\Designation;
use App\Helpers\LogHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EmploymentController extends Controller
{


    public function index()
    {
        $employmentData = PersonalInfo::leftJoin('employment', 'personal_info.id', '=', 'employment.personalID')
            ->leftJoin('designations', 'employment.designation_id', '=', 'designations.id')
            ->leftJoin('departments', 'employment.department_id', '=', 'departments.id') // Added left join for departments
            ->select(
                'personal_info.id',
                'personal_info.first_name',
                'personal_info.last_name',
                'employment.department_id',
                'employment.designation_id',
                'employment.stepIncrement',
                'employment.salaryGrade',
                'employment.date_hired', // Assuming you want to fetch the date_hired as well
                'employment.dateOfEffectivity',
                'employment.dateReleased',
                'departments.department_name', // Fetch department name from the departments table
                'designations.designation as designation_name' // Fetch designation name
            )
            ->get();
    
        $departments = Department::all();
        $designations = Designation::all(); // ✅ for dropdowns or lists
    
        return view('employmentStatus', compact('employmentData', 'departments', 'designations'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'employeeId' => 'required|exists:personal_info,id',
            'department' => 'required|exists:departments,id',
            'salaryGrade' => 'required|integer|min:1|max:33',
            'stepIncrement' => 'required|integer|min:1|max:8',
            'position' => 'required|exists:designations,id', // Assuming position is a designation
            'dateHired' => 'required|date', // Validation for Date Hired
            'dateOfEffectivity' => 'nullable|date',  // Nullable for salary adjustments
            'dateReleased' => 'nullable|date',       // Nullable for salary adjustments
        ]);
    
        try {
            // Fetch the current employee record
            $employment = Employment::where('personalID', $request->employeeId)->first();
    
            // Prepare an array of changes (always log data regardless of if it changed)
            $changes = [];
    
            // If there is an existing employment record
            if ($employment) {
                // Log old data and new data without checking if they're the same
                $changes['department_id'] = [
                    'old' => $employment->department_id,
                    'new' => $request->department
                ];
                $changes['salaryGrade'] = [
                    'old' => $employment->salaryGrade,
                    'new' => $request->salaryGrade
                ];
                $changes['stepIncrement'] = [
                    'old' => $employment->stepIncrement,
                    'new' => $request->stepIncrement
                ];
                $changes['position'] = [
                    'old' => $employment->designation_id,
                    'new' => $request->position
                ];
                $changes['date_hired'] = [
                    'old' => $employment->date_hired,
                    'new' => $request->dateHired
                ];
                $changes['dateOfEffectivity'] = [
                    'old' => $employment->dateOfEffectivity,
                    'new' => $request->dateOfEffectivity
                ];
                $changes['dateReleased'] = [
                    'old' => $employment->dateReleased,
                    'new' => $request->dateReleased
                ];
    
                // Update the employee record
                $employment->update([
                    'department_id' => $request->department,
                    'salaryGrade' => $request->salaryGrade,
                    'stepIncrement' => $request->stepIncrement,
                    'designation_id' => $request->position,
                    'updatedBy' => session('employeeName'),
                    'date_hired' => $request->dateHired,
                    'dateOfEffectivity' => $request->dateOfEffectivity,
                    'dateReleased' => $request->dateReleased,
                ]);
    
            } else {
                // If no employee record exists, treat all fields as "new" and set "old" to null
                $changes['department_id'] = [
                    'old' => null,
                    'new' => $request->department
                ];
                $changes['salaryGrade'] = [
                    'old' => null,
                    'new' => $request->salaryGrade
                ];
                $changes['stepIncrement'] = [
                    'old' => null,
                    'new' => $request->stepIncrement
                ];
                $changes['position'] = [
                    'old' => null,
                    'new' => $request->position
                ];
                $changes['date_hired'] = [
                    'old' => null,
                    'new' => $request->dateHired
                ];
                $changes['dateOfEffectivity'] = [
                    'old' => null,
                    'new' => $request->dateOfEffectivity
                ];
                $changes['dateReleased'] = [
                    'old' => null,
                    'new' => $request->dateReleased
                ];
    
                // Create a new employment record
                $employment = Employment::create([
                    'personalID' => $request->employeeId,
                    'department_id' => $request->department,
                    'salaryGrade' => $request->salaryGrade,
                    'stepIncrement' => $request->stepIncrement,
                    'designation_id' => $request->position,
                    'updatedBy' => session('employeeName'),
                    'date_hired' => $request->dateHired,
                    'dateOfEffectivity' => $request->dateOfEffectivity,
                    'dateReleased' => $request->dateReleased,
                ]);
            }
    
            // Log the changes (always log the changes, even if nothing changed)
            LogHelper::logChanges($request->employeeId, $changes);
    
            // Return the response
            return response()->json([
                'success' => true,
                'message' => 'Employment assignment saved and changes logged successfully!',
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
