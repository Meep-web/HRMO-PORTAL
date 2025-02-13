<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PdsUpdate;
use App\Models\PersonalInfo;

class PersonalDataSheetController extends Controller
{
    public function index(Request $request)
{
    // Default sorting
    $sort = $request->get('sort', 'first_name');
    $order = $request->get('order', 'asc');

    // Fetch personal_info data with the latest update from pdsupdates, and order by specified column
    $personalInfos = PersonalInfo::orderBy($sort, $order)
        ->get()
        ->map(function ($personalInfo) {
            $latestUpdate = PdsUpdate::where('PDSId', $personalInfo->id)
                                     ->latest('updated_at')
                                     ->first();
            $personalInfo->updated_by = $latestUpdate ? $latestUpdate->employeeName : 'N/A';
            return $personalInfo;
        });

    // Pass the data to the view with current sorting parameters
    return view('personal-data-sheet', [
        'personalInfos' => $personalInfos,
        'order' => $order,
    ]);
}



    

    
}