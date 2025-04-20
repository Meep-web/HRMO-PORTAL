<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use DB;
use Exception;
use Illuminate\Support\Facades\Log;

class DatabaseBackupController extends Controller
{
    public function backup()
    {
        $dbConfig = config('database.connections.mysql');

        $dbName = $dbConfig['database'];
        $user = $dbConfig['username'];
        $password = $dbConfig['password'];
        $host = $dbConfig['host'];

        $timestamp = now()->format('Y-m-d_H-i-s');
        $backupDir = storage_path("app/backup");
        $sqlFile = "{$backupDir}/{$dbName}_backup_{$timestamp}.sql";
        $zipFile = "{$backupDir}/{$dbName}_backup_{$timestamp}.zip";

        if (!file_exists($backupDir)) {
            mkdir($backupDir, 0777, true);
        }

        $mysqldump = 'mysqldump';
        $passwordPart = $password ? "-p{$password}" : "";

        $command = "{$mysqldump} -u {$user} {$passwordPart} {$dbName} > \"{$sqlFile}\"";
        exec($command, $output, $result);

        if ($result !== 0) {
            return response()->json([
                'message' => 'Database backup failed.',
                'error' => $output
            ], 500);
        }

        $zip = new \ZipArchive();
        if ($zip->open($zipFile, \ZipArchive::CREATE) === TRUE) {
            $zip->addFile($sqlFile, basename($sqlFile));

            // Add JSON files
            $leaveCreditsPath = storage_path('app/leave-credits/leave_usage_all.json');
            $employmentChangesPath = storage_path('app/employment_changes.json');

            if (file_exists($leaveCreditsPath)) {
                $zip->addFile($leaveCreditsPath, 'leave_usage_all.json');
            }

            if (file_exists($employmentChangesPath)) {
                $zip->addFile($employmentChangesPath, 'employment_changes.json');
            }

            $zip->close();

            // Delete the raw SQL file after zipping
            unlink($sqlFile);

            return response()->download($zipFile)->deleteFileAfterSend(true);
        } else {
            return response()->json([
                'message' => 'Could not create ZIP archive.',
            ], 500);
        }
    }
    public function uploadBackup(Request $request)
{
    try {
        Log::info('Starting upload backup process.');

        // Check if the necessary files are uploaded
        if ($request->hasFile('databaseFile') && $request->hasFile('employmentChangesFile') && $request->hasFile('leaveCreditsFile')) {
            $databaseFile = $request->file('databaseFile');
            $employmentChangesFile = $request->file('employmentChangesFile');
            $leaveCreditsFile = $request->file('leaveCreditsFile');

            // Log file details
            Log::info('Uploaded database file MIME type: ' . $databaseFile->getMimeType());
            Log::info('Uploaded employment changes file MIME type: ' . $employmentChangesFile->getMimeType());
            Log::info('Uploaded leave credits file MIME type: ' . $leaveCreditsFile->getMimeType());

            // Manually check for .sql and .json extensions
            if ($databaseFile->getClientOriginalExtension() !== 'sql') {
                return response()->json(['success' => false, 'error' => 'The database file must be a .sql file.'], 400);
            }

            if ($employmentChangesFile->getClientOriginalExtension() !== 'json') {
                return response()->json(['success' => false, 'error' => 'The employment changes file must be a .json file.'], 400);
            }

            if ($leaveCreditsFile->getClientOriginalExtension() !== 'json') {
                return response()->json(['success' => false, 'error' => 'The leave credits file must be a .json file.'], 400);
            }

            // Store the files temporarily and get their paths
            $tempSQLPath = storage_path('app/temp_sql_file.sql');
            $tempEmploymentChangesPath = storage_path('app/temp_employment_changes.json');
            $tempLeaveCreditsPath = storage_path('app/temp_leave_credits.json');

            // Move files to storage
            $databaseFile->move(storage_path('app'), 'temp_sql_file.sql');
            $employmentChangesFile->move(storage_path('app'), 'temp_employment_changes.json');
            $leaveCreditsFile->move(storage_path('app'), 'temp_leave_credits.json');

            // Handle the JSON files (employment changes and leave credits)
            $this->handleJSONFiles($employmentChangesFile, $leaveCreditsFile);

            // Just log that we received the files
            Log::info('Received and saved the files.');

            // Optionally, you can return the paths or data from the JSON files here
            return response()->json([
                'success' => true,
                'message' => 'Files uploaded and processed successfully!',
                'employmentChangesPath' => $tempEmploymentChangesPath,
                'leaveCreditsPath' => $tempLeaveCreditsPath
            ]);
        } else {
            return response()->json(['success' => false, 'error' => 'One or more files were not uploaded.'], 400);
        }

    } catch (Exception $e) {
        Log::error('Error during file upload: ' . $e->getMessage());
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
}
        private function importSQLFileFromTemp($filePath)
    {
        try {
            Log::info('Starting SQL file import process.');
    
            $dbUser = env('DB_USERNAME');
            $dbName = env('DB_DATABASE');
    
            // Convert the backslashes to forward slashes for Windows compatibility
            $formattedPath = str_replace('\\', '/', $filePath);
    
            // Run the MySQL command to import the file directly from the temporary file
            $command = 'mysql -u ' . $dbUser . ' ' . $dbName . ' < ' . escapeshellarg($formattedPath);
    
            Log::info('Executing command: ' . $command);
    
            $output = shell_exec($command);
    
            // Log command output, if any
            if ($output !== null) {
                Log::info('Shell command output: ' . $output);
            } else {
                Log::info('SQL import completed successfully with no output.');
            }
    
            // Check if the command has no output, meaning success
            if ($output !== null && strpos($output, 'ERROR') !== false) {
                throw new Exception('Failed to import SQL file. Output: ' . $output);
            }
    
        } catch (Exception $e) {
            Log::error('Error during SQL file import: ' . $e->getMessage());
            throw $e;
        }
    }
  


private function handleJSONFiles($employmentChangesFile, $leaveCreditsFile)
{
    try {
        Log::info('Starting JSON files handling.');

        $tempEmploymentPath = storage_path('app/temp_employment_changes.json');
        $tempLeaveCreditsPath = storage_path('app/temp_leave_credits.json');
        $finalEmploymentPath = storage_path('app/employment_changes.json');
        $finalLeaveCreditsPath = storage_path('app/leave-credits/leave_usage_all.json');

        // Move temp employment changes file
        if (File::exists($tempEmploymentPath)) {
            Log::info('Found temp employment changes file. Moving...');
            File::move($tempEmploymentPath, $finalEmploymentPath);
            Log::info('Employment changes file moved successfully.');
        } else {
            Log::warning('Temp employment changes file not found.');
        }

        // Move temp leave credits file
        if (File::exists($tempLeaveCreditsPath)) {
            Log::info('Found temp leave credits file. Moving...');
            File::move($tempLeaveCreditsPath, $finalLeaveCreditsPath);
            Log::info('Leave credits file moved successfully.');
        } else {
            Log::warning('Temp leave credits file not found.');
        }

        Log::info('JSON files handled successfully.');

    } catch (Exception $e) {
        Log::error('Error handling JSON files: ' . $e->getMessage());
        throw $e;
    }
}


}
