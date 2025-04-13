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

        // Load consolidated leave usage data
        $leaveUsageData = [];
        $jsonPath = storage_path('app/leave-credits/leave_usage_all.json'); // ✅ consolidated file

        if (File::exists($jsonPath)) {
            $leaveUsageData = json_decode(File::get($jsonPath), true);
        }

        foreach ($employees as $employee) {
            $dateHired = Carbon::parse($employee->date_hired);
            $monthsWorked = $dateHired->diffInMonths(Carbon::now());

            // Base leave balance calculation
            $leaveBalance = 15 + (1.5 * $monthsWorked);
            $sickLeave = 15 + (1.5 * $monthsWorked);

            // Sum used leave with pay
            $usedVL = 0;
            $usedSL = 0;

            foreach ($leaveUsageData as $entry) {
                if ($entry['employeeId'] == $employee->id && $entry['payType'] === 'With Pay') {
                    if ($entry['leaveType'] === 'VL') {
                        $usedVL += $entry['creditsUsed'];
                    } elseif ($entry['leaveType'] === 'SL') {
                        $usedSL += $entry['creditsUsed'];
                    }
                }
            }

            // Apply leave balances
            $employee->leave_balance = max(0, $leaveBalance - $usedVL);
            $employee->sick_leave = max(0, $sickLeave - $usedSL);

            $employee->date_hired_formatted = $dateHired->format('F d, Y');

            // Build full name
            $employee->full_name = $employee->personalInfo->first_name . ' ' .
                $employee->personalInfo->middle_name . ' ' .
                $employee->personalInfo->last_name;
        }

        return view('leaveCredits', compact('employees'));
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
            "Name: {$employeeData->full_name}\t\tCivil Status: {$employeeData->civil_status}\t\tGSIS Policy Number: {$employeeData->gsis_id}",
            $font,
            $paragraphStyle
        );
        $section->addText(
            "Position:{$employeeData->designation} \tEntrance of Duty: __________\tTIN No.: {$employeeData->tin_no}",
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
            $cell = $table->addCell();
            $textRun = $cell->addTextRun();
            foreach (explode("\n", $row[1]) as $i => $line) {
                $textRun->addText($line, ['bold' => true]);
                if ($i < 2)
                    $textRun->addTextBreak(); // add line break between lines but not after the last one
            }
            // Sample Data (you can replace this with relevant data)

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


        for ($i = count($dataRows) - 1; $i >= 0; $i--) {
            $row = $dataRows[$i];

            if (isset($row[2][2]) && isset($row[3][2])) {
                $lastVLBalance = $row[2][2];
                $lastSLBalance = $row[3][2];
                break;
            }
        }




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
        $dateHired = Carbon::parse($employmentData->date_hired);
        $employeeId = $employmentData->id;

        $jsonPath = storage_path('app/leave-credits/leave_usage_all.json');
        $leaveUsage = [];
        if (file_exists($jsonPath)) {
            $leaveUsage = json_decode(file_get_contents($jsonPath), true);
        }

        $monthsWorked = $dateHired->diffInMonths(Carbon::now());

        $vacationLeave = 15;
        $sickLeave = 15;

        $totalVlWithPayUsed = 0;
        $totalSlWithPayUsed = 0;

        $leaveData = [];
        $periodStart = $dateHired->copy();

        // Add earned leave rows first
        for ($i = 0; $i < $monthsWorked; $i++) {
            $periodEnd = $periodStart->copy()->addMonth();
            $periodLabel = $periodStart->format('M-Y') . ' - ' . $periodEnd->format('M-Y');

            // Add 1.5 credits per month for both Vacation and Sick Leave
            $vacationLeave += 1.5;
            $sickLeave += 1.5;

            // Insert the earned leave row
            $leaveData[] = [
                $periodLabel,
                'Earned',
                [1.5, 0, round($vacationLeave, 2), 0], // Earned Vacation Leave
                [1.5, 0, round($sickLeave, 2), 0], // Earned Sick Leave
                ''
            ];

            $periodStart->addMonth();
        }

        // Add usage rows (and match them to the appropriate earned row)
        foreach ($leaveUsage as $usage) {
            if ($usage['employeeId'] != $employeeId)
                continue;

            $leaveMonthYear = $usage['leaveMonth'] . ' ' . $usage['leaveYear']; // Month-Year from JSON
            $payTypeNote = $usage['payType'] === 'With Pay' ? 'Paid' : 'Unpaid';
            $creditsUsed = -abs($usage['creditsUsed']); // Ensure the value is negative for usage

            // Default structure for the row
            $leaveRow = [
                $leaveMonthYear,
                'Used',
                [0, 0, 0, 0],
                [0, 0, 0, 0],
                "{$payTypeNote}"
            ];

            if ($usage['payType'] === 'With Pay') {
                if ($usage['leaveType'] === 'VL') {
                    $leaveRow[2][1] = $creditsUsed;
                    $totalVlWithPayUsed += abs($creditsUsed);
                } elseif ($usage['leaveType'] === 'SL') {
                    $leaveRow[3][1] = $creditsUsed;
                    $totalSlWithPayUsed += abs($creditsUsed);
                }
            } else {
                if ($usage['leaveType'] === 'VL') {
                    $leaveRow[2][3] = $creditsUsed;
                } elseif ($usage['leaveType'] === 'SL') {
                    $leaveRow[3][3] = $creditsUsed;
                }
            }

            $inserted = false;
            foreach ($leaveData as $index => $row) {
                try {
                    $earnedPeriodEnd = Carbon::createFromFormat('M-Y', explode(' - ', $row[0])[1]);
                    $usageDate = Carbon::parse($usage['leaveMonth'] . ' ' . $usage['leaveYear']);
                    if ($earnedPeriodEnd->isSameMonth($usageDate) || $earnedPeriodEnd->greaterThanOrEqualTo($usageDate)) {
                        array_splice($leaveData, $index + 1, 0, [$leaveRow]);
                        $inserted = true;
                        break;
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }

            if (!$inserted) {
                $leaveData[] = $leaveRow;
            }
        }

        // Sort rows by date
        usort($leaveData, function ($a, $b) {
            try {
                $getDate = function ($label) {
                    return Carbon::createFromFormat('M-Y', explode(' - ', $label)[1]);
                };
                return $getDate($a[0])->lt($getDate($b[0])) ? -1 : 1;
            } catch (\Exception $e) {
                return 0;
            }
        });

        // Final summary row: Diminish with-pay usage from earned
        $leaveData[] = [
            '',
            "BAL.\nBROUGHT\nFORWARD",
            [0, -$totalVlWithPayUsed, round($vacationLeave - $totalVlWithPayUsed, 2), 0],
            [0, -$totalSlWithPayUsed, round($sickLeave - $totalSlWithPayUsed, 2), 0],
            ''
        ];

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
            'payType' => 'required|string',
        ]);

        // Prepare the data to be saved
        $data = [
            'employeeId' => $validated['employeeId'],
            'leaveMonth' => $validated['leaveMonth'],
            'leaveYear' => $validated['leaveYear'],
            'leaveType' => $validated['leaveType'],
            'creditsUsed' => $validated['creditsUsed'],
            'payType' => $validated['payType'],
        ];

        // Single consolidated file path
        $filePath = 'C:/Users/Chris/Desktop/HRMO-PORTAL/storage/app/leave-credits/';
        $filename = 'leave_usage_all.json'; // 👈 One consolidated file

        // Ensure the directory exists
        if (!File::exists($filePath)) {
            File::makeDirectory($filePath, 0775, true);
        }

        // Load existing data if the file exists
        $existingData = File::exists($filePath . $filename)
            ? json_decode(File::get($filePath . $filename), true)
            : [];

        // Append the new entry
        $existingData[] = $data;

        // Save everything back to the file
        File::put($filePath . $filename, json_encode($existingData, JSON_PRETTY_PRINT));

        return response()->json(['message' => 'Data has been saved successfully!']);
    }

}