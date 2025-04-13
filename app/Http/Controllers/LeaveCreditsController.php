<?php

namespace App\Http\Controllers;

use App\Models\PersonalInfo;
use App\Models\Employment;
use App\Models\Designation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\Shared\Html;

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\SimpleType\TblWidth;

use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;



class LeaveCreditsController extends Controller
{

    public function index()
    {
        // Fetch employees with necessary data (Personal Info and Employment)
        $employees = Employment::with(['personalInfo', 'department'])->get();
    
        // Load leave usage data from JSON file
        $leaveUsageData = [];
        $jsonPath = storage_path('app/leave-credits/leave_usage_1.json');
    
        if (File::exists($jsonPath)) {
            $leaveUsageData = json_decode(File::get($jsonPath), true);
        }
    
        foreach ($employees as $employee) {
            $dateHired = Carbon::parse($employee->date_hired);
            $monthsWorked = $dateHired->diffInMonths(Carbon::now());
    
            // Base leave balance calculation
            $leaveBalance = 15 + (1.5 * $monthsWorked);
            $sickLeave = 15 + (1.5 * $monthsWorked);
    
            // Sum used leave based on employeeId and leaveType
            $usedVL = 0;
            $usedSL = 0;
    
            foreach ($leaveUsageData as $entry) {
                if ($entry['employeeId'] == $employee->id) {
                    if ($entry['leaveType'] === 'VL') {
                        $usedVL += $entry['creditsUsed'];
                    } elseif ($entry['leaveType'] === 'SL') {
                        $usedSL += $entry['creditsUsed'];
                    }
                }
            }
    
            // Subtract used credits from balances
            $employee->leave_balance = max(0, $leaveBalance - $usedVL);
            $employee->sick_leave = max(0, $sickLeave - $usedSL);
    
            $employee->date_hired_formatted = $dateHired->format('F d, Y');
    
            // Combine full name from personalInfo
            $employee->full_name = $employee->personalInfo->first_name . ' ' .
                                   $employee->personalInfo->middle_name . ' ' .
                                   $employee->personalInfo->last_name;
        }
    
        return view('leaveCredits', compact('employees'));
    }
    


    public function generateWordDoc()
    {
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();

        // Centered Title
        $section->addText("EMPLOYEE'S LEAVE CARD", [
            'bold' => true,
            'size' => 14,
            'name' => 'Times New Roman'
        ], ['alignment' => Jc::CENTER]);

        // Add space
        $section->addTextBreak(2);

        $paragraphStyle = ['tabs' => [2500, 3100]];
        $font = ['name' => 'Times New Roman', 'size' => 10];

        // Info Lines
        $section->addText(
            "Name: _____________\tCivil Status: ______________\tGSIS Policy Number: ____________________",
            $font,
            $paragraphStyle
        );
        $section->addText(
            "Position: ___________\tEntrance of Duty: __________\tTIN No.: _____________________________",
            $font,
            $paragraphStyle
        );
        $section->addText(
            "Status: _____________\tUnit: ___________________\tNational Reference Card No.: ______________",
            $font,
            $paragraphStyle
        );

        $section->addTextBreak(2);

        // Create table with full width
        $table = $section->addTable([
            'borderSize' => 6,
            'borderColor' => '000000',
            'cellMargin' => 80,
            'width' => 100 * 50, // 100% width in twips
            'unit' => TblWidth::PERCENT
        ]);

        // Header row 1
        $table->addRow();
        $table->addCell(null, ['vMerge' => 'restart', 'valign' => 'center'])->addText('Period');
        $table->addCell(null, ['vMerge' => 'restart', 'valign' => 'center'])->addText('Particulars');
        $table->addCell(null, ['gridSpan' => 4, 'valign' => 'center'])->addText('Vacation Leave', ['bold' => true], ['alignment' => Jc::CENTER]);
        $table->addCell(null, ['gridSpan' => 4, 'valign' => 'center'])->addText('Sick Leave', ['bold' => true], ['alignment' => Jc::CENTER]);
        $table->addCell(null, ['vMerge' => 'restart', 'valign' => 'center'])->addText('Remarks');

        // Header row 2
        $table->addRow();
        $table->addCell(null, ['vMerge' => 'continue']);
        $table->addCell(null, ['vMerge' => 'continue']);
        $subHeaders = ['Earned', 'W/ Pay', 'Balance', 'W/O Pay'];
        foreach (range(1, 2) as $i) {
            foreach ($subHeaders as $sub) {
                $table->addCell()->addText($sub, ['bold' => true], ['alignment' => Jc::CENTER]);
            }
        }
        $table->addCell(null, ['vMerge' => 'continue']);

        // Example data rows
        $dataRows = [
            ['Jan-Mar', 'Sample Data', ['12', '0', '12', '0'], ['15', '1', '14', '0'], 'Notes'],
            ['Apr-Jun', 'Sample Data', ['10', '2', '8', '1'], ['20', '0', '20', '0'], 'Notes']
        ];

        foreach ($dataRows as $row) {
            $table->addRow();
            $table->addCell()->addText($row[0]);
            $table->addCell()->addText($row[1]);
            foreach ($row[2] as $val) {
                $table->addCell()->addText($val);
            }
            foreach ($row[3] as $val) {
                $table->addCell()->addText($val);
            }
            $table->addCell()->addText($row[4]);
        }

        // Add a footer row (bottom of the table)
        $table->addRow();

        // Empty cell for "Period"
        $table->addCell()->addText('');

        // Insert "BAL. BROUGHT FORWARD" in the "Particulars" column with line breaks
        $particularsCell = $table->addCell();
        $textRun = $particularsCell->addTextRun();
        $textRun->addText('BAL.', ['bold' => true]);
        $textRun->addTextBreak(1); // Line break
        $textRun->addText('BROUGHT', ['bold' => true]);
        $textRun->addTextBreak(1); // Line break
        $textRun->addText('FORWARD', ['bold' => true]);

        // Empty cells for "Vacation Leave" columns
        $table->addCell()->addText('');
        $table->addCell()->addText('');
        $table->addCell()->addText('');
        $table->addCell()->addText('');

        // Empty cell for "Remarks" column
        $table->addCell()->addText('');
        $table->addCell()->addText('');
        $table->addCell()->addText('');
        $table->addCell()->addText('');
        $table->addCell()->addText('');

        // Save and download
        $fileName = 'Employee_Leave_Card.docx';
        $filePath = storage_path("app/public/{$fileName}");
        $phpWord->save($filePath, 'Word2007');

        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    public function getEmployeeData($id)
    {
        // Get the specific employee data from the database based on the provided ID
        $employeeData = PersonalInfo::select('first_name', 'middle_name', 'last_name', 'civil_status', 'gsis_id', 'tin_no')
            ->findOrFail($id);

        // Get the Employment data related to this employee
        $employmentData = Employment::where('personalID', $id)->firstOrFail();

        // Combine the name fields into a full_name field
        $employeeData->full_name = $employeeData->first_name . ' ' . $employeeData->middle_name . ' ' . $employeeData->last_name;

        // Get the month and year from the dateHired (assuming dateHired is in Y-m-d format)
        $dateHired = Carbon::parse($employmentData->date_hired);
        $employeeData->date_hired_month_year = $dateHired->format('F Y'); // Formats as "January 2025"

        // Get the designation using the designation_id from the Designation table
        $designation = Designation::where('id', $employmentData->designation_id)->first();
        $employeeData->designation = $designation ? $designation->designation : 'N/A'; // Default to 'N/A' if no designation found

        // Get full name
        $fullName = $employeeData->full_name;

        $phpWord = new PhpWord();
        $section = $phpWord->addSection([
            'marginLeft' => 360,  // 0.5 inch
            'marginRight' => 1440, // 1 inch
            'marginTop' => 1440,   // 1 inch
            'marginBottom' => 1440 // 1 inch
        ]);

        // Add content as before
        $section->addText("EMPLOYEE'S LEAVE CARD", [
            'bold' => true,
            'size' => 14,
            'name' => 'Times New Roman'
        ], ['alignment' => Jc::CENTER]);

        // Add space
        $section->addTextBreak(2);

        $paragraphStyle = ['tabs' => [2500, 3100]];
        $font = ['name' => 'Times New Roman', 'size' => 10];

        // Info Lines - using actual employee data
        $section->addText(
            "Name: {$employeeData->full_name}\tCivil Status: {$employeeData->civil_status}\t\tGSIS Policy Number: {$employeeData->gsis_id}",
            $font,
            $paragraphStyle
        );
        $section->addText(
            "Position:{$employeeData->designation} \t\tEntrance of Duty: __________\tTIN No.: {$employeeData->tin_no}",
            $font,
            $paragraphStyle
        );
        $section->addText(
            "Status: _____________\t\t\tUnit: ___________________\tNational Reference Card No.: __",
            $font,
            $paragraphStyle
        );

        $section->addTextBreak(2);

        // Create table with full width
        $table = $section->addTable([
            'borderSize' => 6,
            'borderColor' => '000000',
            'cellMargin' => 80,
            'width' => 100 * 50, // 100% width in twips
            'unit' => TblWidth::PERCENT
        ]);

        // Header row 1
        $table->addRow();
        $table->addCell(null, ['vMerge' => 'restart', 'valign' => 'center'])->addText('Period');
        $table->addCell(null, ['vMerge' => 'restart', 'valign' => 'center'])->addText('Particulars');
        $table->addCell(null, ['gridSpan' => 4, 'valign' => 'center'])->addText('Vacation Leave', ['bold' => true], ['alignment' => Jc::CENTER]);
        $table->addCell(null, ['gridSpan' => 4, 'valign' => 'center'])->addText('Sick Leave', ['bold' => true], ['alignment' => Jc::CENTER]);
        $table->addCell(null, ['vMerge' => 'restart', 'valign' => 'center'])->addText('Remarks');

        // Header row 2
        $table->addRow();
        $table->addCell(null, ['vMerge' => 'continue']);
        $table->addCell(null, ['vMerge' => 'continue']);
        $subHeaders = ['Earned', 'W/ Pay', 'Balance', 'W/O Pay'];
        foreach (range(1, 2) as $i) {
            foreach ($subHeaders as $sub) {
                $table->addCell()->addText($sub, ['bold' => true], ['alignment' => Jc::CENTER]);
            }
        }
        $table->addCell(null, ['vMerge' => 'continue']);

        // Example of how to use the generateLeaveData function
        $employeeData = Employment::find($id);  // Retrieve the employee's data based on the ID
        $dataRows = $this->generateLeaveData($employeeData);

        // Now populate the table using the dynamic $dataRows
        foreach ($dataRows as $row) {
            $table->addRow();
            $table->addCell()->addText($row[0]);  // Period (e.g., "Feb-2025 - Mar-2025")
            $table->addCell()->addText($row[1]);  // Sample Data (you can replace this with relevant data)

            // Add the vacation leave and sick leave data for the first part (vacation)
            foreach ($row[2] as $val) {
                $table->addCell()->addText($val);  // Vacation Leave data
            }

            // Add the vacation leave and sick leave data for the second part (sick leave)
            foreach ($row[3] as $val) {
                $table->addCell()->addText($val);  // Sick Leave data
            }

            // Add any notes or other information
            $table->addCell()->addText($row[4]);  // Notes
        }



        // Add a footer row (bottom of the table)
        $table->addRow();

        // Empty cell for "Period"
        $table->addCell()->addText('');

        // Insert "BAL. BROUGHT FORWARD" in the "Particulars" column with line breaks
        $particularsCell = $table->addCell();
        $textRun = $particularsCell->addTextRun();
        $textRun->addText('BAL.', ['bold' => true]);
        $textRun->addTextBreak(1); // Line break
        $textRun->addText('BROUGHT', ['bold' => true]);
        $textRun->addTextBreak(1); // Line break
        $textRun->addText('FORWARD', ['bold' => true]);

        // Empty cells for "Vacation Leave" columns
        $table->addCell()->addText('');
        $table->addCell()->addText('');
        $table->addCell()->addText('');
        $table->addCell()->addText('');

        // Empty cell for "Remarks" column
        $table->addCell()->addText('');
        $table->addCell()->addText('');
        $table->addCell()->addText('');
        $table->addCell()->addText('');
        $table->addCell()->addText('');



        // Sanitize the full name (replace non-alphanumeric characters with underscores, but preserve periods)
        $sanitizedFullName = preg_replace('/[^a-zA-Z0-9\s]/', '_', $fullName);

        // Create the file name with sanitized full name and .docx extension
        $fileName = $sanitizedFullName . '_Leave_Card.docx';

        // File path
        $filePath = storage_path("app/public/{$fileName}");

        // Save the Word document
        $phpWord->save($filePath, 'Word2007');

        // Return the file for download and delete after sending
        return response()->download($filePath)->deleteFileAfterSend(true);

    }

    public function generateLeaveData($employmentData)
    {
        // Get the employee's hire date
        $dateHired = Carbon::parse($employmentData->date_hired);

        // Calculate the number of months since the employee was hired
        $monthsWorked = $dateHired->diffInMonths(Carbon::now());

        // Employee leave calculation
        $vacationLeave = 15;  // Initial vacation leave
        $sickLeave = 15;  // Initial sick leave

        // Array to store the leave data for each period
        $leaveData = [];

        // Calculate the periods (e.g., Feb-Mar, Mar-Apr, etc.)
        $periodStart = $dateHired->copy();  // Start from the hire date

        // Define how many months you want to calculate for (e.g., 12 months)
        $periods = $monthsWorked;  // Calculate based on months worked since the hire date
        for ($i = 0; $i < $periods; $i++) {
            // Calculate the end of the month period (e.g., Feb-Mar)
            $periodEnd = $periodStart->copy()->addMonth(); // The end of the current month

            // Format the period as 'Month Year' (e.g., 'Feb-Mar 2025')
            $periodLabel = $periodStart->format('M-Y') . ' - ' . $periodEnd->format('M-Y');

            // Increment the vacation and sick leave for the current month
            $vacationLeave += 1.5;  // Add 1.5 days for each month
            $sickLeave += 1.5;  // Add 1.5 days for each month

            // Add the calculated leave data for the period
            $leaveData[] = [
                $periodLabel,  // Period (e.g., 'Feb-Mar 2025')
                'Earned',  // Placeholder for additional data
                [round($vacationLeave, 2), 0, round($vacationLeave, 2), 0],  // Vacation Leave data
                [round($sickLeave, 2), 0, round($sickLeave, 2), 0],  // Sick Leave data
                ''  // Placeholder for any additional notes
            ];

            // Increment the period start by 1 month for the next period
            $periodStart->addMonth();
        }

        // Ensure the data is sorted in ascending order based on the periods (if necessary)
        usort($leaveData, function ($a, $b) {
            $dateA = Carbon::createFromFormat('M-Y', explode(' - ', $a[0])[1]);
            $dateB = Carbon::createFromFormat('M-Y', explode(' - ', $b[0])[1]);
            return $dateA->lt($dateB) ? -1 : 1;
        });

        // Return the leave data
        return $leaveData;
    }

    public function saveLeaveUsage(Request $request)
    {
        // Validate the incoming data
        $validated = $request->validate([
            'employeeId' => 'required|integer',
            'leaveMonth' => 'required|string',
            'leaveYear' => 'required|integer',
            'leaveType' => 'required|string',
            'creditsUsed' => 'required|numeric',
        ]);

        // Prepare the data to be saved in the JSON file
        $data = [
            'employeeId' => $validated['employeeId'],
            'leaveMonth' => $validated['leaveMonth'],
            'leaveYear' => $validated['leaveYear'],
            'leaveType' => $validated['leaveType'],
            'creditsUsed' => $validated['creditsUsed'],
        ];

        // Define the custom file path
        $filePath = 'C:/Users/Chris/Desktop/HRMO-PORTAL/storage/app/leave-credits/';
        $filename = 'leave_usage_' . $validated['employeeId'] . '.json'; // Using employee ID for the filename

        // Ensure the directory exists
        if (!File::exists($filePath)) {
            File::makeDirectory($filePath, 0775, true);
        }

        // Check if the file already exists
        if (File::exists($filePath . $filename)) {
            // If the file exists, read its contents and append the new data
            $existingData = json_decode(File::get($filePath . $filename), true);

            // Append the new data
            $existingData[] = $data;

            // Write the updated data back to the file
            File::put($filePath . $filename, json_encode($existingData, JSON_PRETTY_PRINT));
        } else {
            // If the file does not exist, create a new file and store the data
            $jsonData = json_encode([$data], JSON_PRETTY_PRINT);
            File::put($filePath . $filename, $jsonData);
        }

        // Return a success response
        return response()->json(['message' => 'Data has been saved successfully!']);
    }
}


