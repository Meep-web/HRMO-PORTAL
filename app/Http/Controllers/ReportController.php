<?php


namespace App\Http\Controllers;


use App\Models\PersonalInfo;
use App\Models\Address;
use App\Models\FamilyBackground;
use App\Models\EducationalBackground;
use App\Models\CivilServiceEligibility;
use App\Models\WorkExperience;
use Illuminate\Http\Request;


class ReportController extends Controller
{
    
    public function generateReport($id)
{
    // Retrieve data for the specific personalInfo based on the id
    $personalInfo = PersonalInfo::find($id);
    $addresses = Address::where('personal_info_id', $id)->get();
    $familyBackgrounds = FamilyBackground::where('personal_info_id', $id)->get();
    $education = EducationalBackground::where('personal_info_id', $id)->get();
    $civilServiceEligibility = CivilServiceEligibility::where('personal_info_id', $id)->get();
    $workExperiences = WorkExperience::where('personal_info_id', $id)->get();

    if ($personalInfo) {
        $reportData = [
            'personalInfo' => $personalInfo,
            'addresses' => $addresses,
            'familyBackgrounds' => $familyBackgrounds,
            'education' => $education,
            'civilServiceEligibility' => $civilServiceEligibility,
            'workExperiences' => $workExperiences,
        ];

        return response()->json([
            'success' => true,
            'reportData' => $reportData
        ]);
    }

    return response()->json([
        'success' => false,
        'message' => 'Personal information not found.'
    ]);
}


    //
}
