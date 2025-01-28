<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NOSA;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;


class NoticeOfSalaryAdjustmentController extends Controller
{
    // Show the form
    public function show()
    {
        // Fetch the latest record for each unique department
        $nosaRecords = NOSA::select('department', 'updated_at', 'userName')
            ->whereIn('id', function ($query) {
                $query->selectRaw('MAX(id)') // Get the latest record ID for each department
                    ->from('NOSA')
                    ->groupBy('department');
            })
            ->orderBy('department', 'asc')
            ->get();

        // Pass the data to the view
        return view('noticeOfSalaryAdjustment', compact('nosaRecords'));
    }
    // Save the data
    public function save(Request $request)
    {
        // Log the received data for debugging
        Log::info('Received data:', $request->all());

        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'currentEmployeeName' => 'required|string',
            'salaryAdjustments' => 'required|array',
            'salaryAdjustments.*.employeeName' => 'required|string',
            'salaryAdjustments.*.position' => 'required|string',
            'salaryAdjustments.*.department' => 'required|string',
            'salaryAdjustments.*.previousSalary' => 'required|numeric',
            'salaryAdjustments.*.newSalary' => 'required|numeric',
            'salaryAdjustments.*.dateOfEffectivity' => 'required|date',
            'salaryAdjustments.*.dateReleased' => 'required|date',
            'salaryAdjustments.*.salaryGrade' => 'required|integer',
            'salaryAdjustments.*.stepIncrement' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Get the validated data
        $data = $validator->validated();

        try {
            // Save each salary adjustment to the database
            foreach ($data['salaryAdjustments'] as $adjustment) {
                NOSA::create([
                    'employeeName' => $adjustment['employeeName'],
                    'position' => $adjustment['position'],
                    'department' => $adjustment['department'],
                    'previousSalary' => $adjustment['previousSalary'],
                    'newSalary' => $adjustment['newSalary'],
                    'dateOfEffectivity' => $adjustment['dateOfEffectivity'],
                    'dateReleased' => $adjustment['dateReleased'],
                    'salaryGrade' => $adjustment['salaryGrade'],
                    'stepIncrement' => $adjustment['stepIncrement'],
                    'userName' => $data['currentEmployeeName'], // Add the current employeeName as the userName
                ]);
            }

            // Return a success response
            return response()->json([
                'success' => true,
                'message' => 'Data saved successfully!',
            ]);
        } catch (\Exception $e) {
            Log::error('Database error: ' . $e->getMessage()); // Log the error
            return response()->json([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage(),
            ], 500);
        }
    }
    //Show the Data Per Department
    public function getNosaData(Request $request)
    {
        $department = $request->query('department');

        // Fetch data from the database
        $data = NOSA::where('department', $department)
            ->select('employeeName', 'position', 'dateReleased', 'userName')
            ->get();

        return response()->json($data);
    }
}
