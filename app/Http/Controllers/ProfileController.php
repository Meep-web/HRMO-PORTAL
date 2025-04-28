<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Employee;
use Illuminate\Support\Facades\Log; // Make sure this is at the top of your file
use Illuminate\Support\Facades\Session;


class ProfileController extends Controller
{

    public function showProfile()
    {
        // Get logged-in user's ID from session
        $userId = session('userId'); // or Session::get('userId')
    
        // Log the user ID
        Log::info('Session userId: ' . $userId);
    
        // Fetch employee details based on user ID
        $employee = Employee::where('id', $userId)->first();
    
        return view('profile', compact('employee'));
    }


    public function updateProfile(Request $request)
    {
        $request->validate([
            'employeeName' => 'required|string|max:255',
            'profileImage' => 'nullable|image|mimes:jpeg,png,jpg,gif', // Image validation
            'employeeId' => 'required|exists:employees,id', // Validate that the ID exists in the database
        ]);
    
        // Get the employeeId from the request
        $employeeId = $request->employeeId;
    
        // Retrieve employee based on the provided ID
        $employee = Employee::find($employeeId);
    
        if (!$employee) {
            return response()->json(['success' => false, 'message' => 'Employee not found.'], 404);
        }
    
        // Update name
        $employee->employeeName = $request->employeeName;
    
        // Handle Image Upload
        if ($request->hasFile('profileImage')) {
            // Delete old image if it exists
            if ($employee->imagePath && file_exists(public_path($employee->imagePath))) {
                unlink(public_path($employee->imagePath));
            }
    
            // Save new image
            $file = $request->file('profileImage');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('employeeImage'), $filename);
    
            // Update image path in database
            $employee->imagePath = 'employeeImage/' . $filename;
        }
    
        $employee->save();
    
        // Update session with new employee name and image path
        session([
            'employeeName' => $employee->employeeName,
            'employeeImage' => $employee->imagePath ?: 'employeeImage/default-user.png',
        ]);
    
        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully.',
            'newImage' => asset($employee->imagePath), // Return new image URL
        ]);
    }
    
    public function updatePassword(Request $request)
{
    $request->validate([
        'password' => 'required|confirmed', // Ensures password & confirmPassword match
    ]);

    // Get the userId from the session
    $userId = session('userId');
    if (!$userId) {
        return response()->json(['error' => 'User not found in session.'], 404);
    }

    // Find the employee using the session userId
    $user = Employee::find($userId);
    if (!$user) {
        return response()->json(['error' => 'Employee not found.'], 404);
    }

    // Log the userId and a masked version of the password (for security)
    Log::info('Password update requested', [
        'userId' => $userId,
        'password' => '********' // Masked password for security
    ]);

    // Hash and save the new password
    $user->password = Hash::make($request->password); 
    $user->save();

    return response()->json(['success' => 'Password updated successfully!']);
}

    
}
