<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Employee;

class ProfileController extends Controller
{
    public function showProfile()
    {
        // Get logged-in user's ID
        $userId = Auth::id();

        // Fetch employee details based on user ID
        $employee = Employee::where('id', $userId)->first();

        return view('profile', compact('employee'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'employeeName' => 'required|string|max:255',
            'profileImage' => 'nullable|image|mimes:jpeg,png,jpg,gif', // Image validation
        ]);
    
        $user = Auth::user();
        $employee = Employee::where('id', $user->id)->first();
    
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
    
        // Update session
        session([
            'employeeName' => $employee->employeeName,
            'imagePath' => $employee->imagePath ?? session('imagePath'),
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

    $user = Employee::find(Auth::id()); // Get logged-in user

    if (!$user) {
        return response()->json(['error' => 'User not found.'], 404);
    }

    $user->password = Hash::make($request->password); // Hash the new password
    $user->save();

    return response()->json(['success' => 'Password updated successfully!']);
}
    
}
