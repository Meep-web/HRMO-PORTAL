<?php

namespace App\Http\Controllers;
use App\Models\PersonalInfo;
use App\Models\Employment;
use App\Models\Department;
use App\Models\SalaryGrade;
use App\Models\Designation; // Assuming this is the model for your personal_info table
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\TextAlignment;
use PhpOffice\PhpWord\Style\Paragraph;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\Style\TablePosition;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

use PhpOffice\PhpWord\IOFactory;
use Illuminate\Http\Request;

class ServiceRecordsController extends Controller
{
    public function index()
    {
        // Fetch only the required fields from the PersonalInfo table
        $personalInfos = PersonalInfo::select('id', 'first_name', 'middle_name', 'last_name')
            ->get();

        // Iterate over each PersonalInfo to fetch related Employment data
        foreach ($personalInfos as $personalInfo) {
            // Concatenate the name (first, middle, last)
            $personalInfo->full_name = $personalInfo->first_name . ' ' . $personalInfo->middle_name . ' ' . $personalInfo->last_name;

            // Get the Employment record based on the personalID
            $employment = Employment::where('personalID', $personalInfo->id)->first();

            // Get the Department and Designation based on the employment record
            if ($employment) {
                $department = Department::find($employment->department_id); // Fetch the department name
                $designation = Designation::find($employment->designation_id); // Fetch the designation name

                // Add these relationships to the PersonalInfo object
                $personalInfo->department_name = $department ? $department->department_name : 'N/A';
                $personalInfo->designation_name = $designation ? $designation->designation : 'N/A';
            }
        }

        // Return the data to the view
        return view('serviceRecords', compact('personalInfos'));
    }

public function generateWordDoc($id)
{
    $formattedLeaveData = $this->processLeaveData($id);
    $phpWord = $this->setupPhpWordDocument();
    $section = $this->createDocumentSection($phpWord);
    
    $this->addMainHeader($section);
    $this->addPersonalInformation($section, $id);
    $this->addServiceRecordTable($section, $id, $formattedLeaveData);
    
    return $this->generateAndReturnPDF($phpWord, $id);
}

// Data Processing
private function processLeaveData($id)
{
    $jsonPath = storage_path('app/leave-credits/leave_usage_all.json');
    
    if (!file_exists($jsonPath)) {
        dd("JSON file not found at: " . $jsonPath);
    }

    $jsonContent = file_get_contents($jsonPath);
    $allLeaveData = json_decode($jsonContent, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        dd("Failed to parse JSON: " . json_last_error_msg());
    }

    // Filter data
    $employeeLeaveData = array_filter($allLeaveData, fn($r) => $r['employeeId'] == $id);
    $withoutPayLeaveData = array_filter($employeeLeaveData, fn($r) => $r['payType'] === "Without Pay");
    $withoutPayLeaveData = array_values($withoutPayLeaveData);

    return array_map(function ($record) {
        $monthNumber = date('m', strtotime($record['leaveMonth']));
        $record['formattedDate'] = $record['leaveYear'] . '-' . $monthNumber . '-01';
        return $record;
    }, $withoutPayLeaveData);
}

// Document Setup
private function setupPhpWordDocument()
{
    return new PhpWord();
}

private function createDocumentSection($phpWord)
{
    return $phpWord->addSection([
        'orientation' => 'portrait',
        'paperSize' => 'A4',
        'marginLeft' => 600,
        'marginRight' => 600,
        'marginTop' => 600,
        'marginBottom' => 600
    ]);
}

// Header Section
private function addMainHeader($section)
{
    $fontStyle = ['bold' => true, 'size' => 12, 'name' => 'Times New Roman'];
    
    $section->addText('Republic of the Philippines', $fontStyle, ['alignment' => Jc::CENTER]);
    $section->addText('Province of Laguna', $fontStyle, ['alignment' => Jc::CENTER]);
    $section->addText('MUNICIPALITY OF PAGSANJAN', $fontStyle, ['alignment' => Jc::CENTER]);
    $section->addText('OFFICE OF THE MAYOR', $fontStyle, ['alignment' => Jc::CENTER]);
    $section->addTextBreak(2);
    $section->addText('SERVICE RECORD', ['bold' => true, 'size' => 14], ['alignment' => Jc::CENTER]);
    $section->addText('(To be Accomplished by the Employer)', ['italic' => true], ['alignment' => Jc::CENTER]);
    $section->addTextBreak(2);
}

// Personal Information Section
private function addPersonalInformation($section, $id)
{
    $personalInfo = PersonalInfo::select('id', 'first_name', 'middle_name', 'last_name', 'date_of_birth')
        ->findOrFail($id);
    
    $this->addNameAndBirthDetails($section, $personalInfo);
    $this->addEmploymentCertification($section);
}

private function addNameAndBirthDetails($section, $personalInfo)
{
    $style = ['bold' => true, 'underline' => 'single'];
    $fullName = "{$personalInfo->first_name} {$personalInfo->middle_name} {$personalInfo->last_name}";
    $birthDate = \Carbon\Carbon::parse($personalInfo->date_of_birth)->format('F d, Y');

    $this->createTextRun($section, "NAME    : ", $fullName, $style, " (If married, give maiden name)");
    $this->createTextRun($section, "BIRTH   : ", $birthDate, $style, 
        " (Date herein should be checked from birth or baptismal certificate or some reliable documents)");
}

private function createTextRun($section, $label, $value, $style, $note)
{
    $textRun = $section->addTextRun();
    $textRun->addText($label);
    $textRun->addText($value, $style);
    $textRun->addText($note);
}

private function addEmploymentCertification($section)
{
    $section->addText(
        "This is to certify that the employee name herein above actual rendered service in this office as shown by the service records below, each line of which is supported by appointment and other papers actually issued by this office and approved by the authorities concerned.",
        null,
        ['alignment' => Jc::BOTH]
    );
    $section->addTextBreak(1);
}

// Table Section
private function addServiceRecordTable($section, $id, $formattedLeaveData)
{
    $table = $section->addTable($this->getTableStyle());
    $this->addTableHeaders($table);
    $this->populateTableData($table, $id, $formattedLeaveData);
}

private function getTableStyle()
{
    $phpWord = new PhpWord();
    $phpWord->addTableStyle('CustomServiceRecordTable', [
        'borderSize' => 6,
        'borderColor' => '000000',
        'cellMargin' => 80,
    ], ['bgColor' => 'c0c0c0']);
    
    return 'CustomServiceRecordTable';
}

private function addTableHeaders($table)
{
    $table->addRow();
$table->addCell(1200, ['gridSpan' => 2, 'vMerge' => 'restart'])->addText('SERVICE', ['bold' => true], ['alignment' => Jc::CENTER]);
$table->addCell(3600, ['gridSpan' => 3, 'vMerge' => 'restart'])->addText('RECORD OF APPOINTMENT', ['bold' => true], ['alignment' => Jc::CENTER]);
$table->addCell(2400, ['vMerge' => 'restart'])->addText('OFFICE/ ENTITY DIVISION', ['bold' => true], ['alignment' => Jc::CENTER]);
$table->addCell(1600, ['vMerge' => 'restart'])->addText('LEAVE W/O PAY', ['bold' => true], ['alignment' => Jc::CENTER]);

$table->addRow();
$table->addCell(1200)->addText('From', ['bold' => true], ['alignment' => Jc::CENTER]);
$table->addCell(1200)->addText('To', ['bold' => true], ['alignment' => Jc::CENTER]);
$table->addCell(1400)->addText('Designation', ['bold' => true], ['alignment' => Jc::CENTER]);
$table->addCell(1200)->addText('Status', ['bold' => true], ['alignment' => Jc::CENTER]);
$table->addCell(1200)->addText('Salary', ['bold' => true], ['alignment' => Jc::CENTER]);
$table->addCell(2400)->addText('Station/Place', ['bold' => true], ['alignment' => Jc::CENTER]);
$table->addCell(1600)->addText('', ['bold' => true]);

}

private function populateTableData($table, $id, $formattedLeaveData)
{
    $employmentChanges = $this->getEmploymentChanges($id);
    
    if ($employmentChanges) {
        usort($employmentChanges, fn($a, $b) => strtotime($a['timestamp']) - strtotime($b['timestamp']));
        $lastIndex = count($employmentChanges) - 1;

        foreach ($employmentChanges as $index => $change) {
            $this->addEmploymentRecordRow($table, $change, $index, $lastIndex, $formattedLeaveData);
        }
    }
}

private function addEmploymentRecordRow($table, $change, $index, $lastIndex, $formattedLeaveData)
{
    $servicePeriod = $change['service_period'];
    $appointmentRecord = $change['appointment_record'];
    $officeDivision = $change['office_division'];
    
    $fromDate = $servicePeriod['from'];
    $isLastIndex = ($index === $lastIndex);
    $toDateDisplay = $isLastIndex ? 'Present' : $servicePeriod['to'];
    $toDateCalc = $isLastIndex ? '2025-04-21' : $servicePeriod['to'];

    $monthlyLeaveCounts = [];
    foreach ($formattedLeaveData as $leave) {
        if ($leave['formattedDate'] >= $fromDate && $leave['formattedDate'] <= $toDateCalc) {
            $monthYear = date('Y-m', strtotime($leave['formattedDate']));
            $monthlyLeaveCounts[$monthYear] = ($monthlyLeaveCounts[$monthYear] ?? 0) + $leave['creditsUsed'];
        }
    }

    if (empty($monthlyLeaveCounts)) {
        $this->addEmptyLeaveRow($table, $fromDate, $toDateDisplay, $appointmentRecord, $officeDivision);
        return;
    }

    $this->addLeaveDataRows($table, $fromDate, $toDateDisplay, $appointmentRecord, $officeDivision, $monthlyLeaveCounts);
}

private function addEmptyLeaveRow($table, $fromDate, $toDateDisplay, $appointmentRecord, $officeDivision)
{
    $table->addRow();
    $table->addCell(2000)->addText($fromDate, null, ['alignment' => Jc::CENTER]);
    $table->addCell(2000)->addText($toDateDisplay, null, ['alignment' => Jc::CENTER]);
    $table->addCell(2000)->addText($appointmentRecord['designation'], null, ['alignment' => Jc::CENTER]);
    $table->addCell(2000)->addText($appointmentRecord['status'], null, ['alignment' => Jc::CENTER]);
    $table->addCell(2000)->addText($appointmentRecord['salary'], null, ['alignment' => Jc::CENTER]);
    $table->addCell(3000)->addText($officeDivision['station_place'], null, ['alignment' => Jc::CENTER]);
    $table->addCell(2000)->addText('0', null, ['alignment' => Jc::CENTER]);
}

private function addLeaveDataRows($table, $fromDate, $toDateDisplay, $appointmentRecord, $officeDivision, $monthlyLeaveCounts)
{
    $firstMonth = true;
    foreach ($monthlyLeaveCounts as $month => $count) {
        $table->addRow();
        $table->addCell(2000)->addText($firstMonth ? $fromDate : '', null, ['alignment' => Jc::CENTER]);
        $table->addCell(2000)->addText(($month === array_key_last($monthlyLeaveCounts)) ? $toDateDisplay : '', null, ['alignment' => Jc::CENTER]);
        $table->addCell(2000)->addText($firstMonth ? $appointmentRecord['designation'] : '', null, ['alignment' => Jc::CENTER]);
        $table->addCell(2000)->addText($firstMonth ? $appointmentRecord['status'] : '', null, ['alignment' => Jc::CENTER]);
        $table->addCell(2000)->addText($firstMonth ? $appointmentRecord['salary'] : '', null, ['alignment' => Jc::CENTER]);
        $table->addCell(3000)->addText($firstMonth ? $officeDivision['station_place'] : '', null, ['alignment' => Jc::CENTER]);
        $table->addCell(2000)->addText($count, null, ['alignment' => Jc::CENTER]);
        $firstMonth = false;
    }
}

// File Handling
private function generateAndReturnPDF($phpWord, $id)
{
    $fileName = 'service_record_' . $id . '_' . time();
    $docxPath = storage_path('app/public/' . $fileName . '.docx');
    $pdfPath = storage_path('app/public/' . $fileName . '.pdf');

    if (File::exists($docxPath)) {
        File::delete($docxPath);
    }

    $phpWord->save($docxPath, 'Word2007');
    exec('"C:/Program Files/LibreOffice/program/soffice.exe" --headless --convert-to pdf --outdir ' . escapeshellarg(storage_path('app/public')) . ' ' . escapeshellarg($docxPath));

    if (file_exists($pdfPath)) {
        $response = response()->file($pdfPath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($pdfPath) . '"'
        ]);

        // When the response finishes sending, delete both files
        register_shutdown_function(function () use ($docxPath, $pdfPath) {
            if (file_exists($docxPath)) {
                unlink($docxPath);
            }
            if (file_exists($pdfPath)) {
                unlink($pdfPath);
            }
        });

        return $response;
    }

    return response()->json(['error' => 'PDF file not found']);
}

    public function getEmploymentChanges($employeeId)
    {
        // Path to the JSON file
        $filePath = storage_path('app/employment_changes.json');

        // Check if the file exists
        if (!File::exists($filePath)) {
            return null;
        }

        // Read the file content
        $jsonContent = File::get($filePath);

        // Decode the JSON into an array
        $data = json_decode($jsonContent, true);

        // Filter the data by employee_id
        $employeeData = array_filter($data, function ($entry) use ($employeeId) {
            return $entry['employee_id'] == $employeeId;
        });

        // If no data found for this employee, return null
        if (empty($employeeData)) {
            return null;
        }

        $employeeData = array_values($employeeData);  // Re-index after filtering

        // Prepare the relevant data to populate the table
        $employmentChanges = [];

        foreach ($employeeData as $entry) {
            $changes = $entry['changes'];

            // Fetch department name based on department_id
            $departmentName = null;
            if (!empty($changes['department_id']['new'])) {
                $department = Department::find($changes['department_id']['new']);
                $departmentName = $department ? $department->department_name : 'N/A';
            }

            // Fetch position name based on position ID
            $positionName = null;
            if (!empty($changes['position']['new'])) {
                $position = Designation::find($changes['position']['new']);
                $positionName = $position ? $position->designation : 'N/A';
            }

            // Get the salary based on salary grade and step increment
            $salaryGrade = $changes['salaryGrade']['new'] ?? null;
            $stepIncrement = $changes['stepIncrement']['new'] ?? null;
            $salary = 'No value taken';  // Default value if no salary found

            if ($salaryGrade && $stepIncrement) {
                $salaryGradeRecord = SalaryGrade::where('grade', $salaryGrade)->first();

                if ($salaryGradeRecord) {
                    $stepColumn = 'step' . $stepIncrement;

                    if (isset($salaryGradeRecord->$stepColumn)) {
                        $salary = $salaryGradeRecord->$stepColumn;
                        \Log::info("Fetched salary: $salary for grade: $salaryGrade, step: $stepIncrement");
                    } else {
                        \Log::warning("Invalid step: $stepIncrement for grade: $salaryGrade — column '$stepColumn' not found or null.");
                    }
                } else {
                    \Log::warning("Salary grade not found: $salaryGrade");
                }
            }


            $employmentChanges[] = [
                'timestamp' => $entry['timestamp'] ?? null,  // Add this line
                'service_period' => [
                    'from' => $changes['date_hired']['new'] ?? 'N/A',
                    'to' => $changes['dateReleased']['new'] ?? 'N/A',
                ],
                'appointment_record' => [
                    'designation' => $positionName,
                    'status' => 'Permanent',
                    'salary' => $salary,
                ],
                'office_division' => [
                    'station_place' => $departmentName,
                ]
            ];

        }

        return $employmentChanges;
    }





}
