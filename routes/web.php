<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NoticeOfSalaryAdjustmentController;
use App\Http\Controllers\PersonalDataSheetController;
use App\Http\Controllers\LocationController;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\FileController;
use App\Http\Controllers\ReportController;




// Redirect root to login page
Route::get('/', function () {
    return redirect()->route('login');
});

// Login routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])
    ->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// Group routes that require authentication
Route::middleware('auth')->group(function () {
    // Dashboard route
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Notice of Salary Adjustment routes
    Route::get('/notice-of-salary-adjustment', [NoticeOfSalaryAdjustmentController::class, 'show'])->name('noticeOfSalaryAdjustment');
    Route::post('/save-nosa', [NoticeOfSalaryAdjustmentController::class, 'save'])->name('save.nosa');
    Route::get('/get-nosa-data', [NoticeOfSalaryAdjustmentController::class, 'getNosaData']);
    Route::get('/get-employee-data/{employeeId}', [NoticeOfSalaryAdjustmentController::class, 'getEmployeeData']);

    // Personal Data Sheet route
    Route::get('/personal-data-sheet', [PersonalDataSheetController::class, 'index'])->name('personalDataSheet');
    Route::get('/personal-data-sheet/{id}', [PersonalDataSheetController::class, 'show'])->name('personal-data-sheet.show');
    
});

Route::get('/data/zipcodes.json', function () {
    return response()->json(json_decode(File::get(public_path('data/zipcodes.json'))));
});

Route::post('/submit-form', [FileController::class, 'store']);
Route::post('/validate-form', [FileController::class, 'validateForm']);
Route::get('/get-update-data/{id}', [FileController::class, 'getUpdateData']);
Route::put('/update-personal-info', [FileController::class, 'updatePersonalInfo'])->name('update-personal-info');



// Location routes
Route::get('/provinces', [LocationController::class, 'getProvinces']);
Route::get('/towns/{provinceCode}', [LocationController::class, 'getTowns']);
Route::get('/barangays/{townCode}', [LocationController::class, 'getBarangays']);
Route::get('/generate-report/{id}', [ReportController::class, 'generateReport']);


