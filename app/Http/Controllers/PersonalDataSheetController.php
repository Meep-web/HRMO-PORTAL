<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PdsUpdate;
use App\Models\PersonalInfo;
use App\Models\Employment;
use App\Models\Department;

class PersonalDataSheetController extends Controller
{
    public function index(Request $request)
{
    // Default sorting
    $sort = $request->get('sort', 'first_name');
    $order = $request->get('order', 'asc');

    // Fetch personal_info data with the latest update from pdsupdates, and order by specified column
    $personalInfos = PersonalInfo::orderBy($sort, $order)
        ->get()
        ->map(function ($personalInfo) {
            $latestUpdate = PdsUpdate::where('PDSId', $personalInfo->id)
                                     ->latest('updated_at')
                                     ->first();
            $personalInfo->updated_by = $latestUpdate ? $latestUpdate->employeeName : 'N/A';
            return $personalInfo;
        });

    // Pass the data to the view with current sorting parameters
    return view('personal-data-sheet', [
        'personalInfos' => $personalInfos,
        'order' => $order,
    ]);
}
public function getEmployeeDetails($id)
{
    // Find the personal info
    $employee = PersonalInfo::find($id);

    if (!$employee) {
        return response()->json(['error' => 'Employee not found'], 404);
    }

    // Find the employment details (if available)
    $employment = Employment::where('personalID', $id)->first();

    // Get department name if employment data exists
    $departmentName = null;
    if ($employment && $employment->department_id) {
        $department = Department::find($employment->department_id);
        $departmentName = $department ? $department->department_name : null;
    }

    return response()->json([
        'id' => $employee->id,
        'first_name' => $employee->first_name,
        'last_name' => $employee->last_name,
        'middle_name' => $employee->middle_name,
        'name_extension' => $employee->name_extension,
        'date_of_birth' => $employee->date_of_birth,
        'place_of_birth' => $employee->place_of_birth,
        'sex' => $employee->sex,
        'civil_status' => $employee->civil_status,
        'height' => $employee->height,
        'weight' => $employee->weight,
        'blood_type' => $employee->blood_type,
        'gsis_id' => $employee->gsis_id,
        'pagibig_id' => $employee->pagibig_id,
        'philhealth_id' => $employee->philhealth_id,
        'sss_no' => $employee->sss_no,
        'tin_no' => $employee->tin_no,
        'agency_employee_no' => $employee->agency_employee_no,
        'telephone_no' => $employee->telephone_no,
        'mobile_no' => $employee->mobile_no,
        'email' => $employee->email,
        'is_filipino' => $employee->is_filipino,
        'is_dual_citizen' => $employee->is_dual_citizen,
        'dual_citizen_type' => $employee->dual_citizen_type,
        'dual_citizen_country' => $employee->dual_citizen_country,
        'department' => $departmentName
    ]);
}


}