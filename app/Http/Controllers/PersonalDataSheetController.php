<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PdsUpdate;
use App\Models\PersonalInfo;

class PersonalDataSheetController extends Controller
{
    public function index()
    {
        // Fetch data from pdsUpdates table
        $pdsUpdates = PdsUpdate::all();
    
        // Fetch data from personal_info table
        $personalInfos = PersonalInfo::all();
    
        // Pass the data to the view
        return view('personal-data-sheet', [
            'pdsUpdates' => $pdsUpdates,
            'personalInfos' => $personalInfos,
        ]);
    }

    
}