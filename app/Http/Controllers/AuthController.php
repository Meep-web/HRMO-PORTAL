<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use App\Models\Employee;

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

        // Check if the user is locked out due to too many failed attempts
        if (RateLimiter::tooManyAttempts($this->throttleKey($request), 3)) {
            $seconds = RateLimiter::availableIn($this->throttleKey($request));
            return back()
                ->with('throttle_error', true)
                ->with('remaining_time', $seconds) // Pass the remaining time to the view
                ->withInput();
        }

        // Attempt to find the employee by employeeName
        $employee = Employee::where('employeeName', $request->employeeName)->first();

        // Check if employee exists and password is correct
        if ($employee && Hash::check($request->password, $employee->password)) {
            // Clear the throttle for this user on successful login
            RateLimiter::clear($this->throttleKey($request));

            // Authenticate the user - Using Auth to log in the employee
            Auth::loginUsingId($employee->id);

            // Store user data in session
            session([
                'employeeName' => $employee->employeeName,
                'userId' => $employee->id, // Store user ID in session
                'usertype' => $employee->role, // Store user type in session
            ]);

            // Redirect to the dashboard after successful login
            return redirect()->route('dashboard');
        }

        // Increment the failed login attempts for this user
        RateLimiter::hit($this->throttleKey($request));

        // If authentication fails, redirect back with error
        return back()
            ->withErrors(['login' => 'Invalid credentials.'])
            ->withInput();
    }

    /**
     * Generate a unique throttle key for the login attempt.
     * This key is based on the employeeName and IP address.
     */
    protected function throttleKey(Request $request)
    {
        return Str::lower($request->input('employeeName')) . '|' . $request->ip();
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
