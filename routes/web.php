<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NoticeOfSalaryAdjustmentController;
use App\Http\Controllers\PersonalDataSheetController;
use App\Http\Controllers\LocationController;

// Redirect root to login page
Route::get('/', function () {
    return redirect()->route('login');
});

// Login routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard route (protected by auth middleware)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');



Route::get('/notice-of-salary-adjustment', [NoticeOfSalaryAdjustmentController::class, 'show'])->name('noticeOfSalaryAdjustment');
Route::post('/save-nosa', [NoticeOfSalaryAdjustmentController::class, 'save'])->name('save.nosa');
Route::get('/get-nosa-data', [NoticeOfSalaryAdjustmentController::class, 'getNosaData']);
Route::get('/get-employee-data/{employeeId}', [NoticeOfSalaryAdjustmentController::class, 'getEmployeeData']);
Route::get('/get-employee-data/{employeeId}', [NoticeOfSalaryAdjustmentController::class, 'getEmployeeData']);
Route::get('/personal-data-sheet', [PersonalDataSheetController::class, 'index'])->name('personalDataSheet');

Route::get('/provinces', [LocationController::class, 'getProvinces']);
Route::get('/towns/{provinceCode}', [LocationController::class, 'getTowns']);
Route::get('/barangays/{townCode}', [LocationController::class, 'getBarangays']);
