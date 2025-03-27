<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User; // Import your User model

class UserAccountController extends Controller
{
    public function index()
    {
        $employees = Employee::where('role', '!=', 'Admin')->get(); // Exclude Admin
        return view('editAccount', compact('employees'));
    }

    public function resetPassword($id)
    {
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json(['success' => false, 'message' => 'Employee not found.'], 404);
        }

        // Generate a new random password
        $newPassword = Str::random(8); // Generates an 8-character password

        // Update the password (assuming Employee has a `password` field)
        $employee->password = Hash::make($newPassword);
        $employee->save();

        return response()->json([
            'success' => true,
            'newPassword' => $newPassword // Send new password to frontend
        ]);
    }



    public function getEmployee($id)
    {
        $employee = Employee::where('id', $id)->first();

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $employee->id,
                'name' => $employee->employeeName,
                'role' => $employee->role,
                'image' => $employee->imagePath ? asset($employee->imagePath) : null,
            ]
        ]);
    }

    public function updateEmployee(Request $request, $id)
{
    $employee = Employee::find($id);

    if (!$employee) {
        return response()->json(['success' => false, 'message' => 'Employee not found.'], 404);
    }

    // Check if trying to assign another Encoder
    if ($request->has('role') && $request->role === 'Encoder') {
        $existingEncoder = Employee::where('role', 'Encoder')->where('id', '!=', $id)->first();

        if ($existingEncoder) {
            return response()->json([
                'success' => false,
                'message' => 'There can only be one Encoder.',
            ], 400);
        }
    }

    // Update Role
    if ($request->has('role')) {
        $employee->role = $request->role;
    }

    // Handle Image Upload (Store in public/employeeImage)
    if ($request->hasFile('profile_picture')) {
        // Delete the old image if it exists
        if ($employee->imagePath && file_exists(public_path($employee->imagePath))) {
            unlink(public_path($employee->imagePath));
        }

        // Save the new image
        $file = $request->file('profile_picture');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('employeeImage'), $filename);

        // Update image path in database
        $employee->imagePath = '/employeeImage/' . $filename;
    }

    $employee->save();

    return response()->json([
        'success' => true,
        'message' => 'Employee updated successfully.',
        'imagePath' => $employee->imagePath ?? null
    ]);
}

}
