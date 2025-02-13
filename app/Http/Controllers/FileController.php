<?php

namespace App\Http\Controllers;

use App\Models\PersonalInfo;
use App\Models\Address;
use App\Models\FamilyBackground;
use App\Models\EducationalBackground;
use App\Models\CivilServiceEligibility;
use App\Models\WorkExperience;
use App\Models\PdsUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FileController extends Controller
{
    protected function validateRequest(Request $request)
    {
        // Define base validation rules
        $rules = [
            // Personal Info Validation
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'name_extension' => 'nullable|string|max:10',
            'date_of_birth' => 'required|date',
            'place_of_birth' => 'required|string|max:255',
            'sex' => 'required|string|max:10',
            'civil_status' => 'required|string|max:50',
            'height' => 'required|numeric',
            'weight' => 'required|numeric',
            'blood_type' => 'nullable|string|max:5',
            'gsis_id' => 'nullable|string|max:50',
            'pagibig_id' => 'nullable|string|max:50',
            'philhealth_id' => 'nullable|string|max:50',
            'sss_no' => 'nullable|string|max:50',
            'tin_no' => 'nullable|string|max:50',
            'agency_employee_no' => 'nullable|string|max:50',
            'telephone_no' => 'nullable|string|max:50',
            'mobile_no' => 'nullable|string|max:50',
            'email' => 'required|email|max:255',

            // Address Validation
            'residential_address.house_no' => 'required|string|max:50',
            'residential_address.street' => 'nullable|string|max:100',
            'residential_address.subdivision' => 'nullable|string|max:100',
            'residential_address.barangay' => 'required|string|max:100',
            'residential_address.city' => 'required|string|max:100',
            'residential_address.province' => 'required|string|max:100',
            'residential_address.zip_code' => 'required|string|max:10',

            'permanent_address.house_no' => 'required|string|max:50',
            'permanent_address.street' => 'nullable|string|max:100',
            'permanent_address.subdivision' => 'nullable|string|max:100',
            'permanent_address.barangay' => 'required|string|max:100',
            'permanent_address.city' => 'required|string|max:100',
            'permanent_address.province' => 'required|string|max:100',
            'permanent_address.zip_code' => 'required|string|max:10',

            // Family Background Validation
            'family_background.spouse_surname' => 'nullable|string|max:255',
            'family_background.spouse_first_name' => 'nullable|string|max:255',
            'family_background.spouse_middle_name' => 'nullable|string|max:255',
            'family_background.spouse_name_extension' => 'nullable|string|max:10',
            'family_background.spouse_occupation' => 'nullable|string|max:255',
            'family_background.spouse_employer' => 'nullable|string|max:255',
            'family_background.spouse_telephone' => 'nullable|string|max:50',
            'family_background.father_surname' => 'required|string|max:255',
            'family_background.father_first_name' => 'required|string|max:255',
            'family_background.father_middle_name' => 'nullable|string|max:255',
            'family_background.father_name_extension' => 'nullable|string|max:10',
            'family_background.mother_surname' => 'required|string|max:255',
            'family_background.mother_first_name' => 'required|string|max:255',
            'family_background.mother_middle_name' => 'nullable|string|max:255',

            // Education Background Validation
            'elementary' => 'nullable|array',
            'secondary' => 'nullable|array',
            'vocational' => 'nullable|array',
            'college' => 'nullable|array',
            'graduateStudies' => 'nullable|array',


        ];

        // Add Civil Service Eligibility rules only if the data is present
        if ($request->has('civil_service_eligibility') && !empty($request->civil_service_eligibility)) {
            $rules['civil_service_eligibility'] = 'required|array';
            $rules['civil_service_eligibility.*.eligibility_name'] = 'required|string|max:255';
            $rules['civil_service_eligibility.*.rating'] = 'required|numeric';
            $rules['civil_service_eligibility.*.date_of_exam'] = 'required|date';
            $rules['civil_service_eligibility.*.place_of_exam'] = 'required|string|max:255';
            $rules['civil_service_eligibility.*.license_number'] = 'nullable|string|max:50';
            $rules['civil_service_eligibility.*.license_validity'] = 'nullable|date';
        }

        // Add Work Experience rules only if the data is present
        if ($request->has('work_experience')) {
            $rules['work_experience'] = 'required|array';
            $rules['work_experience.*.from'] = 'required|date';
            $rules['work_experience.*.to'] = 'required|date';
            $rules['work_experience.*.position_title'] = 'required|string|max:255';
            $rules['work_experience.*.department'] = 'required|string|max:255';
            $rules['work_experience.*.monthly_salary'] = 'required|numeric';
            $rules['work_experience.*.salary_grade'] = 'nullable|string|max:50';
            $rules['work_experience.*.step_increment'] = 'nullable|string|max:50';
            $rules['work_experience.*.appointment_status'] = 'required|string|max:50';
            $rules['work_experience.*.govt_service'] = 'required|in:Yes,No';
        }

        return $request->validate($rules);
    }

    public function validateForm(Request $request)
    {
        try {
            $validatedData = $this->validateRequest($request);
            return response()->json(['message' => 'Validation successful']);
        } catch (ValidationException $e) {
            return response()->json(['error' => 'Validation failed', 'errors' => $e->errors()], 422);
        }
    }

    // Store form data into the database
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // Validate the request data
            $validatedData = $this->validateRequest($request);

            // Save personal info
            $personalInfo = PersonalInfo::create($validatedData);

            // Save Residential Address
            Address::create([
                'personal_info_id' => $personalInfo->id,
                'type' => 'residential',
                'house_no' => $request->input('residential_address.house_no'),
                'street' => $request->input('residential_address.street'),
                'subdivision' => $request->input('residential_address.subdivision'),
                'barangay' => $request->input('residential_address.barangay'),
                'city' => $request->input('residential_address.city'),
                'province' => $request->input('residential_address.province'),
                'zip_code' => $request->input('residential_address.zip_code'),
            ]);

            // Save Permanent Address
            Address::create([
                'personal_info_id' => $personalInfo->id,
                'type' => 'permanent',
                'house_no' => $request->input('permanent_address.house_no'),
                'street' => $request->input('permanent_address.street'),
                'subdivision' => $request->input('permanent_address.subdivision'),
                'barangay' => $request->input('permanent_address.barangay'),
                'city' => $request->input('permanent_address.city'),
                'province' => $request->input('permanent_address.province'),
                'zip_code' => $request->input('permanent_address.zip_code'),
            ]);

            // Save Family Background
            if ($request->has('family_background')) {
                FamilyBackground::create([
                    'personal_info_id' => $personalInfo->id,
                    'spouse_surname' => $validatedData['family_background']['spouse_surname'] ?? null,
                    'spouse_first_name' => $validatedData['family_background']['spouse_first_name'] ?? null,
                    'spouse_middle_name' => $validatedData['family_background']['spouse_middle_name'] ?? null,
                    'spouse_name_extension' => $validatedData['family_background']['spouse_name_extension'] ?? null,
                    'spouse_occupation' => $validatedData['family_background']['spouse_occupation'] ?? null,
                    'spouse_employer' => $validatedData['family_background']['spouse_employer'] ?? null,
                    'spouse_telephone' => $validatedData['family_background']['spouse_telephone'] ?? null,
                    'father_surname' => $validatedData['family_background']['father_surname'],
                    'father_first_name' => $validatedData['family_background']['father_first_name'],
                    'father_middle_name' => $validatedData['family_background']['father_middle_name'] ?? null,
                    'father_name_extension' => $validatedData['family_background']['father_name_extension'] ?? null,
                    'mother_surname' => $validatedData['family_background']['mother_surname'],
                    'mother_first_name' => $validatedData['family_background']['mother_first_name'],
                    'mother_middle_name' => $validatedData['family_background']['mother_middle_name'] ?? null,
                ]);
            }

            // Save Education Background
            $educationLevels = ['elementary', 'secondary', 'vocational', 'college', 'graduateStudies'];
            foreach ($educationLevels as $level) {
                if ($request->has($level)) {
                    EducationalBackground::create([
                        'personal_info_id' => $personalInfo->id,
                        'level' => $level,
                        'school' => $validatedData[$level]['school'],
                        'degree' => $validatedData[$level]['degree'] ?? null,
                        'from' => $validatedData[$level]['from'] ?? null,
                        'to' => $validatedData[$level]['to'] ?? null,
                        'highest_level' => $validatedData[$level]['highestLevel'] ?? null,
                        'year_graduated' => $validatedData[$level]['yearGraduated'] ?? null,
                        'honors' => $validatedData[$level]['honors'] ?? null,
                    ]);
                }
            }

            // Save Civil Service Eligibility
            if ($request->has('civil_service_eligibility')) {
                foreach ($validatedData['civil_service_eligibility'] as $eligibility) {
                    CivilServiceEligibility::create([
                        'personal_info_id' => $personalInfo->id,
                        'eligibility_name' => $eligibility['eligibility_name'],
                        'rating' => $eligibility['rating'],
                        'date_of_exam' => $eligibility['date_of_exam'],
                        'place_of_exam' => $eligibility['place_of_exam'],
                        'license_number' => $eligibility['license_number'] ?? null,
                        'license_validity' => $eligibility['license_validity'] ?? null,
                    ]);
                }
            }

            // Save Work Experience
            if ($request->has('work_experience')) {
                foreach ($validatedData['work_experience'] as $experience) {
                    WorkExperience::create([
                        'personal_info_id' => $personalInfo->id,
                        'from' => $experience['from'],
                        'to' => $experience['to'],
                        'position_title' => $experience['position_title'],
                        'department' => $experience['department'],
                        'monthly_salary' => $experience['monthly_salary'],
                        'salary_grade' => $experience['salary_grade'] ?? null,
                        'step_increment' => $experience['step_increment'] ?? null,
                        'appointment_status' => $experience['appointment_status'],
                        'govt_service' => $experience['govt_service'],
                    ]);
                }
            }

            // Fetch the employeeName from the hidden input field
            $employeeName = $request->input('currentEmployeeName');

            // Debug: Log the employeeName
            logger('Employee Name:', ['employeeName' => $employeeName]);

            // Save PDS Update
            PdsUpdate::create([
                'employeeName' => $employeeName, // Use the employeeName from the hidden input
                'PDSId' => $personalInfo->id, // Use the ID of the newly created PersonalInfo record
            ]);
            DB::commit();

            return response()->json(['message' => 'Data saved successfully']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getUpdateData($id)
{
    try {
        // Fetch the PersonalInfo record
        $personalInfo = PersonalInfo::find($id);

        if (!$personalInfo) {
            return response()->json(['error' => 'Record not found'], 404);
        }

        // Fetch related data using personal_info_id
        $residentialAddress = Address::where('personal_info_id', $personalInfo->id)
            ->where('type', 'residential')
            ->first();

        $permanentAddress = Address::where('personal_info_id', $personalInfo->id)
            ->where('type', 'permanent')
            ->first();

        $familyBackground = FamilyBackground::where('personal_info_id', $personalInfo->id)->first();
        $educationalBackgrounds = EducationalBackground::where('personal_info_id', $personalInfo->id)->get();
        $civilServiceEligibilities = CivilServiceEligibility::where('personal_info_id', $personalInfo->id)->get();
        $workExperiences = WorkExperience::where('personal_info_id', $personalInfo->id)->get();

        // Format the data for the frontend
        $data = [
            'personal_info' => [
                'last_name' => $personalInfo->last_name, // surname in the modal
                'first_name' => $personalInfo->first_name,
                'middle_name' => $personalInfo->middle_name,
                'date_of_birth' => $personalInfo->date_of_birth,
                'place_of_birth' => $personalInfo->place_of_birth,
                'sex' => $personalInfo->sex,
                'civil_status'=>$personalInfo->civil_status,
                'height'=>$personalInfo->height,
                'weight'=>$personalInfo->weight,
                'blood_type'=>$personalInfo->blood_type,
                'gsis_id'=>$personalInfo->gsis_id,
                'pagibig_id'=>$personalInfo->pagibig_id,
                'philhealth_id'=>$personalInfo->philhealth_id,
                'sss_no'=>$personalInfo->sss_no,
                'tin_no'=>$personalInfo->tin_no,
                'agency_employee_no'=>$personalInfo->agency_employee_no,
                'telephone_no'=>$personalInfo->telephone_no,
                'mobile_no'=>$personalInfo->mobile_no,
                'email'=>$personalInfo->email,
                
            ],
            'residential_address' => $residentialAddress,
            'permanent_address' => $permanentAddress,
            'family_background' => $familyBackground,
            'educational_backgrounds' => $educationalBackgrounds,
            'civil_service_eligibilities' => $civilServiceEligibilities,
            'work_experiences' => $workExperiences,
        ];

        return response()->json($data);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}
}
