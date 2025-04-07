<?php

// app/Helpers/LogHelper.php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class LogHelper
{
    public static function logChanges($employeeId, $changes)
{
    // Prepare the log data
    $logData = [
        'employee_id' => $employeeId,
        'changes' => $changes,
        'timestamp' => now()->toDateTimeString(),
    ];

    // Path to the log file
    $logFilePath = storage_path('app/employment_changes.json');
    
    // Check if the file exists, if not create it
    if (!file_exists($logFilePath)) {
        // Ensure the directory exists before attempting to write
        $directory = dirname($logFilePath);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true); // Create directory if not exists
        }

        // Create an empty file with an empty array
        file_put_contents($logFilePath, json_encode([])); 
    }

    // Retrieve existing log data from the file
    $existingLogs = json_decode(file_get_contents($logFilePath), true);
    
    // Add the new change to the log data
    $existingLogs[] = $logData;

    // Save updated log data back to the file
    file_put_contents($logFilePath, json_encode($existingLogs, JSON_PRETTY_PRINT));
}

}
