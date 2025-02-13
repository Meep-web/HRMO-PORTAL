<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PdsUpdate;
use App\Models\PersonalInfo;

class PersonalDataSheetController extends Controller
{
    public function index()
{
    // Fetch personal_info data with the latest update from pdsupdates
    $personalInfos = PersonalInfo::all()->map(function ($personalInfo) {
        $latestUpdate = PdsUpdate::where('PDSId', $personalInfo->id)
                                 ->latest('updated_at')
                                 ->first();
        $personalInfo->updated_by = $latestUpdate ? $latestUpdate->employeeName : 'N/A';
        return $personalInfo;
    });

    // Pass the data to the view
    return view('personal-data-sheet', [
        'personalInfos' => $personalInfos,
    ]);
}

    

    
}