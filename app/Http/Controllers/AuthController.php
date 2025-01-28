<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Employee; // Add the Employee model for accessing the employees table
use Illuminate\Support\Facades\Hash; // Add Hash facade for checking password hashes

class AuthController extends Controller
{
    // Show the login form
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Login the user
    public function login(Request $request)
    {
        // Validate the incoming login request
        $request->validate([
            'employeeName' => 'required|string', // We only need employeeName and password
            'password' => 'required|string',
        ]);
    
        // Attempt to find the employee by employeeName
        $employee = Employee::where('employeeName', $request->employeeName)->first();
    
        // Check if employee exists and password is correct
        if ($employee && Hash::check($request->password, $employee->password)) {
            // Authenticate the user - Using Auth to log in the employee
            Auth::loginUsingId($employee->id);
    
            // Store the employeeName in the session or a way to access it in the dropdown
            session(['employeeName' => $employee->employeeName]);
    
            // Redirect to the dashboard after successful login
            return redirect()->route('dashboard');
        }
    
        // If authentication fails, redirect back with error
        return back()->withErrors(['login' => 'Invalid credentials.'])->withInput();
    }
    

    // Logout the user
    public function logout(Request $request)
{
    // Check if the request is a GET request
    if ($request->isMethod('get')) {
        // You could add some kind of security check here (e.g., a token or verification)
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }

    return redirect()->route('login');
}

}
