<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NoticeOfSalaryAdjustmentController;
use App\Http\Controllers\PersonalDataSheetController;
use App\Http\Controllers\LocationController;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\FileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserAccountController;




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

    Route::post('/submit-form', [FileController::class, 'store']);
    Route::post('/validate-form', [FileController::class, 'validateForm']);
    Route::get('/get-update-data/{id}', [FileController::class, 'getUpdateData']);
    Route::put('/update-personal-info', [FileController::class, 'updatePersonalInfo'])->name('update-personal-info');
    Route::post('/save-multiple-data', [FileController::class, 'saveMultipleData']);


    // Location routes
    Route::get('/provinces', [LocationController::class, 'getProvinces']);
    Route::get('/towns/{provinceCode}', [LocationController::class, 'getTowns']);
    Route::get('/barangays/{townCode}', [LocationController::class, 'getBarangays']);
    Route::get('/generate-report/{id}', [ReportController::class, 'generateReport']);
});

Route::get('/data/zipcodes.json', function () {
    return response()->json(json_decode(File::get(public_path('data/zipcodes.json'))));
});

Route::get('/account-management', function () {return view('accountManagement');})->name('account.management');

Route::get('/employment-status', function () {return view('employmentStatus');});

Route::get('/edit-account', function () {return view('editAccount');})->name('editAccount');



Route::get('/user-accounts', [UserAccountController::class, 'index'])->name('user.accounts');
Route::post('/reset-password/{id}', [UserAccountController::class, 'resetPassword']);
Route::get('/get-employee/{id}', [UserAccountController::class, 'getEmployee']);
Route::post('/update-employee/{id}', [UserAccountController::class, 'updateEmployee']);





