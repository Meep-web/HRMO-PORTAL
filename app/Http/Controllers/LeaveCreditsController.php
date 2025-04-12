<?php

namespace App\Http\Controllers;

use App\Models\PersonalInfo;
use App\Models\Employment;
use App\Models\Department;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LeaveCreditsController extends Controller
{
    public function index()
    {
        // Fetch employees with necessary data (Personal Info and Employment)
        $employees = Employment::with(['personalInfo', 'department'])->get();
    
        // Add leave balance and sick leave calculation for each employee
        foreach ($employees as $employee) {
            $dateHired = Carbon::parse($employee->date_hired); // Get the hire date
            $monthsWorked = $dateHired->diffInMonths(Carbon::now()); // Get number of months worked (ignores days)
    
            // Base leave is 15 days, with 1.5 days added for each full month worked
            $leaveBalance = 15 + (1.5 * $monthsWorked);
            
            // Assuming sick leave is calculated the same way
            $sickLeave = 15 + (1.5 * $monthsWorked);
    
            // Save the values as they are, but display them as integers (no decimals)
            $employee->leave_balance = $leaveBalance;
            $employee->sick_leave = $sickLeave;
    
            // Format the date_hired for display in the view
            $employee->date_hired_formatted = $dateHired->format('F d, Y');
        }
    
        // Return the view with the employees data
        return view('leaveCredits', compact('employees'));
    }
}
