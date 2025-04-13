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
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EmploymentController;
use App\Http\Controllers\LeaveCreditsController;
use App\Models\Designation;
use App\Models\Department;
use App\Http\Controllers\ServiceRecordsController;




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
    Route::post('/show-salary-changes', [NoticeOfSalaryAdjustmentController::class, 'showSalaryChanges']);
    Route::post('/refactor_data', [NoticeOfSalaryAdjustmentController::class, 'refactorData']);
    // Personal Data Sheet route
    Route::get('/personal-data-sheet', [PersonalDataSheetController::class, 'index'])->name('personalDataSheet');
    Route::get('/personal-data-sheet/{id}', [PersonalDataSheetController::class, 'show'])->name('personal-data-sheet.show');
    Route::get('/get-employee-details/{id}', [PersonalDataSheetController::class, 'getEmployeeDetails']);
    


    Route::post('/submit-form', [FileController::class, 'store']);
    Route::post('/validate-form', [FileController::class, 'validateForm']);
    Route::get('/get-update-data/{id}', [FileController::class, 'getUpdateData']);
    Route::put('/update-personal-info', [FileController::class, 'updatePersonalInfo'])->name('update-personal-info');
    Route::post('/store-personal-info', [FileController::class, 'storePersonalInfo']);



    // Location routes
    Route::get('/provinces', [LocationController::class, 'getProvinces']);
    Route::get('/towns/{provinceCode}', [LocationController::class, 'getTowns']);
    Route::get('/barangays/{townCode}', [LocationController::class, 'getBarangays']);
    Route::get('/generate-report/{id}', [ReportController::class, 'generateReport']);

    Route::get('/account-management', function () {
        return view('accountManagement'); })->name('account.management');

    Route::get('/employment-status', [EmploymentController::class, 'index']);

    Route::post('/assign-employment', [EmploymentController::class, 'store'])->name('employment.store');



    Route::get('/edit-account', function () {
        return view('editAccount'); })->name('editAccount');



    Route::get('/user-accounts', [UserAccountController::class, 'index'])->name('user.accounts');
    Route::post('/reset-password/{id}', [UserAccountController::class, 'resetPassword']);
    Route::get('/get-employee/{id}', [UserAccountController::class, 'getEmployee']);
    Route::post('/update-employee/{id}', [UserAccountController::class, 'updateEmployee']);



    Route::get('/profile', [ProfileController::class, 'showProfile'])
        ->middleware('auth')
        ->name('profile');
    Route::post('/profile/update', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::post('/update-password', [ProfileController::class, 'updatePassword'])->middleware('auth');



});

Route::get('/data/zipcodes.json', function () {
    return response()->json(json_decode(File::get(public_path('data/zipcodes.json'))));
});

Route::get('/get-designations/{departmentId}', function ($departmentId) {
    $designations = Designation::where('department_id', $departmentId)->get();
    return response()->json($designations);
});

Route::get('/get-department-name/{departmentId}', function ($departmentId) {
    $department = Department::find($departmentId); // Find the department by its ID
    if ($department) {
        return response()->json(['name' => $department->department_name]); // Return the department name
    }
    return response()->json(['name' => 'Unknown Department']); // Return a default value if not found
});

Route::get('/leave-credits', [LeaveCreditsController::class, 'index']);
Route::get('/get-employee-data/{id}', [LeaveCreditsController::class, 'getEmployeeData'])->name('getEmployeeData');
Route::post('/save-leave-usage', [LeaveCreditsController::class, 'saveLeaveUsage']);


Route::get('/generate-service-record/{id}', [ServiceRecordsController::class, 'generateWordDoc'])->name('generate.service.record');
Route::get('/service-records', [ServiceRecordsController::class, 'index'])->name('service.records');