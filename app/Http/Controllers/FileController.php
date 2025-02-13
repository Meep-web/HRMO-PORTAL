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
use Illuminate\Support\Facades\Log;



class FileController extends Controller
{
    protected function validateRequest(Request $request)
    {
        // Define base validation rules
        $rules = [
            // Personal Info Validation
            'personal_info_id' => 'nullable| int |max:255',
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

            // Citizenship Validation
            'is_filipino' => 'sometimes|boolean', // True if Filipino
            'is_dual_citizen' => 'sometimes|boolean', // True if Dual Citizen
            'dual_citizen_type' => 'nullable|string|in:byBirth,byNaturalization', // Must be either "byBirth" or "byNaturalization"
            'dual_citizen_country' => 'nullable|required_if:is_dual_citizen,true|string|max:100', // Required if Dual Citizen is true

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

            // Save Personal Info (including citizenship)
            $personalInfo = PersonalInfo::create([
                'first_name' => $validatedData['first_name'],
                'last_name' => $validatedData['last_name'],
                'middle_name' => $validatedData['middle_name'] ?? null,
                'name_extension' => $validatedData['name_extension'] ?? null,
                'date_of_birth' => $validatedData['date_of_birth'],
                'place_of_birth' => $validatedData['place_of_birth'],
                'sex' => $validatedData['sex'],
                'civil_status' => $validatedData['civil_status'],
                'height' => $validatedData['height'],
                'weight' => $validatedData['weight'],
                'blood_type' => $validatedData['blood_type'] ?? null,
                'gsis_id' => $validatedData['gsis_id'] ?? null,
                'pagibig_id' => $validatedData['pagibig_id'] ?? null,
                'philhealth_id' => $validatedData['philhealth_id'] ?? null,
                'sss_no' => $validatedData['sss_no'] ?? null,
                'tin_no' => $validatedData['tin_no'] ?? null,
                'agency_employee_no' => $validatedData['agency_employee_no'] ?? null,
                'telephone_no' => $validatedData['telephone_no'] ?? null,
                'mobile_no' => $validatedData['mobile_no'] ?? null,
                'email' => $validatedData['email'],
                // Citizenship
                'is_filipino' => $validatedData['is_filipino'] ?? false,
                'is_dual_citizen' => $validatedData['is_dual_citizen'] ?? false,
                'dual_citizen_type' => $validatedData['dual_citizen_type'] ?? null,
                'dual_citizen_country' => $validatedData['dual_citizen_country'] ?? null,
            ]);

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
                FamilyBackground::create(array_merge(
                    ['personal_info_id' => $personalInfo->id],
                    $validatedData['family_background']
                ));
            }

            // Save Educational Background
            $educationLevels = ['elementary', 'secondary', 'vocational', 'college', 'graduateStudies'];
            foreach ($educationLevels as $level) {
                if ($request->has($level)) {
                    EducationalBackground::create(array_merge(
                        ['personal_info_id' => $personalInfo->id, 'level' => $level],
                        $request->input($level)
                    ));
                }
            }

            // Save Civil Service Eligibility
            if ($request->has('civil_service_eligibility')) {
                foreach ($request->input('civil_service_eligibility') as $eligibility) {
                    CivilServiceEligibility::create(array_merge(
                        ['personal_info_id' => $personalInfo->id],
                        $eligibility
                    ));
                }
            }

            // Save Work Experience
            if ($request->has('work_experience')) {
                foreach ($request->input('work_experience') as $experience) {
                    WorkExperience::create(array_merge(
                        ['personal_info_id' => $personalInfo->id],
                        $experience
                    ));
                }
            }

            // Save PDS Update
            PdsUpdate::create([
                'employeeName' => $request->input('currentEmployeeName'),
                'PDSId' => $personalInfo->id,
            ]);

            DB::commit();

            return response()->json(['message' => 'Data saved successfully']);

        } catch (\Exception $e) {
            DB::rollBack();
            logger()->error('Error storing PDS data: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while saving data.'], 500);
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
                    'personal_info_id' => $personalInfo->id,
                    'last_name' => $personalInfo->last_name, // surname in the modal
                    'first_name' => $personalInfo->first_name,
                    'middle_name' => $personalInfo->middle_name,
                    'date_of_birth' => $personalInfo->date_of_birth,
                    'place_of_birth' => $personalInfo->place_of_birth,
                    'sex' => $personalInfo->sex,
                    'civil_status' => $personalInfo->civil_status,
                    'height' => $personalInfo->height,
                    'weight' => $personalInfo->weight,
                    'blood_type' => $personalInfo->blood_type,
                    'gsis_id' => $personalInfo->gsis_id,
                    'pagibig_id' => $personalInfo->pagibig_id,
                    'philhealth_id' => $personalInfo->philhealth_id,
                    'sss_no' => $personalInfo->sss_no,
                    'tin_no' => $personalInfo->tin_no,
                    'agency_employee_no' => $personalInfo->agency_employee_no,
                    'telephone_no' => $personalInfo->telephone_no,
                    'mobile_no' => $personalInfo->mobile_no,
                    'email' => $personalInfo->email,

                    // Citizenship Validation
                    'is_filipino' => $personalInfo->is_filipino,
                    'is_dual_citizen' => $personalInfo->is_dual_citizen,
                    'dual_citizen_type' => $personalInfo->dual_citizen_type,
                    'dual_citizen_country' => $personalInfo->dual_citizen_country,

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

    public function updatePersonalInfo(Request $request)
    {
        try {
            DB::beginTransaction();

            // Validate the request data
            $validatedData = $this->validateRequest($request);

            // Find the existing personal info entry by ID
            $personalInfo = PersonalInfo::findOrFail($validatedData['personal_info_id']);

            // Update Personal Info (including citizenship)
            $personalInfo->update([
                'first_name' => $validatedData['first_name'],
                'last_name' => $validatedData['last_name'],
                'middle_name' => $validatedData['middle_name'] ?? null,
                'name_extension' => $validatedData['name_extension'] ?? null,
                'date_of_birth' => $validatedData['date_of_birth'],
                'place_of_birth' => $validatedData['place_of_birth'],
                'sex' => $validatedData['sex'],
                'civil_status' => $validatedData['civil_status'],
                'height' => $validatedData['height'],
                'weight' => $validatedData['weight'],
                'blood_type' => $validatedData['blood_type'] ?? null,
                'gsis_id' => $validatedData['gsis_id'] ?? null,
                'pagibig_id' => $validatedData['pagibig_id'] ?? null,
                'philhealth_id' => $validatedData['philhealth_id'] ?? null,
                'sss_no' => $validatedData['sss_no'] ?? null,
                'tin_no' => $validatedData['tin_no'] ?? null,
                'agency_employee_no' => $validatedData['agency_employee_no'] ?? null,
                'telephone_no' => $validatedData['telephone_no'] ?? null,
                'mobile_no' => $validatedData['mobile_no'] ?? null,
                'email' => $validatedData['email'],
                // Citizenship
                'is_filipino' => $validatedData['is_filipino'] ?? false,
                'is_dual_citizen' => $validatedData['is_dual_citizen'] ?? false,
                'dual_citizen_type' => $validatedData['dual_citizen_type'] ?? null,
                'dual_citizen_country' => $validatedData['dual_citizen_country'] ?? null,
            ]);

            // Update Residential Address
            Address::where('personal_info_id', $personalInfo->id)
                ->where('type', 'residential')
                ->update([
                    'house_no' => $request->input('residential_address.house_no'),
                    'street' => $request->input('residential_address.street'),
                    'subdivision' => $request->input('residential_address.subdivision'),
                    'barangay' => $request->input('residential_address.barangay'),
                    'city' => $request->input('residential_address.city'),
                    'province' => $request->input('residential_address.province'),
                    'zip_code' => $request->input('residential_address.zip_code'),
                ]);

            // Update Permanent Address
            Address::where('personal_info_id', $personalInfo->id)
                ->where('type', 'permanent')
                ->update([
                    'house_no' => $request->input('permanent_address.house_no'),
                    'street' => $request->input('permanent_address.street'),
                    'subdivision' => $request->input('permanent_address.subdivision'),
                    'barangay' => $request->input('permanent_address.barangay'),
                    'city' => $request->input('permanent_address.city'),
                    'province' => $request->input('permanent_address.province'),
                    'zip_code' => $request->input('permanent_address.zip_code'),
                ]);

            // Update Family Background
            if ($request->has('family_background')) {
                $familyBackground = FamilyBackground::where('personal_info_id', $personalInfo->id)->first();
                if ($familyBackground) {
                    $familyBackground->update($validatedData['family_background']);
                }
            }

            // Update Educational Background
            $educationLevels = ['elementary', 'secondary', 'vocational', 'college', 'graduateStudies'];
            foreach ($educationLevels as $level) {
                if ($request->has($level)) {
                    $education = EducationalBackground::where('personal_info_id', $personalInfo->id)
                        ->where('level', $level)
                        ->first();
                    if ($education) {
                        $education->update($request->input($level));
                    }
                }
            }

            // Update Civil Service Eligibility
            if ($request->has('civil_service_eligibility')) {
                foreach ($request->input('civil_service_eligibility') as $eligibility) {
                    // Check if the eligibility exists based on eligibility_name
                    $existingEligibility = CivilServiceEligibility::where('personal_info_id', $personalInfo->id)
                        ->where('eligibility_name', $eligibility['eligibility_name'])
                        ->first();

                    // If the eligibility exists, update it
                    if ($existingEligibility) {
                        $existingEligibility->update([
                            'eligibility_name' => $eligibility['eligibility_name'],
                            'rating' => $eligibility['rating'],
                            'date_of_exam' => $eligibility['date_of_exam'],
                            'place_of_exam' => $eligibility['place_of_exam'],
                            'license_number' => $eligibility['license_number'],
                            'license_validity' => $eligibility['license_validity']
                        ]);
                    } else {
                        // If the eligibility doesn't exist, create a new one
                        CivilServiceEligibility::create([
                            'personal_info_id' => $personalInfo->id,
                            'eligibility_name' => $eligibility['eligibility_name'],
                            'rating' => $eligibility['rating'],
                            'date_of_exam' => $eligibility['date_of_exam'],
                            'place_of_exam' => $eligibility['place_of_exam'],
                            'license_number' => $eligibility['license_number'],
                            'license_validity' => $eligibility['license_validity']
                        ]);
                    }
                }
            }


            // Update Work Experience
            if ($request->has('work_experience')) {
                foreach ($request->input('work_experience') as $experience) {
                    // Check if we have an existing entry using unique identifier
                    $existingExperience = WorkExperience::where('personal_info_id', $personalInfo->id)
                        ->where('from', $experience['from'])
                        ->where('to', $experience['to'])
                        ->first();

                    // If experience exists, update it
                    if ($existingExperience) {
                        $existingExperience->update([
                            'from' => $experience['from'],
                            'to' => $experience['to'],
                            'position_title' => $experience['position_title'],
                            'department' => $experience['department'],
                            'monthly_salary' => $experience['monthly_salary'],
                            'salary_grade' => $experience['salary_grade'],
                            'step_increment' => $experience['step_increment'],
                            'appointment_status' => $experience['appointment_status'],
                            'govt_service' => $experience['govt_service']
                        ]);
                    } else {
                        // Create new entry if it doesn't exist
                        WorkExperience::create([
                            'personal_info_id' => $personalInfo->id,
                            'from' => $experience['from'],
                            'to' => $experience['to'],
                            'position_title' => $experience['position_title'],
                            'department' => $experience['department'],
                            'monthly_salary' => $experience['monthly_salary'],
                            'salary_grade' => $experience['salary_grade'],
                            'step_increment' => $experience['step_increment'],
                            'appointment_status' => $experience['appointment_status'],
                            'govt_service' => $experience['govt_service']
                        ]);
                    }
                }

            }



            // Update PDS Update Record
            PdsUpdate::create([
                'employeeName' => $request->input('currentEmployeeName'),
                'PDSId' => $personalInfo->id,
            ]);

            DB::commit();

            return response()->json(['message' => 'Data updated successfully']);

        } catch (\Exception $e) {
            DB::rollBack();
            logger()->error('Error updating PDS data: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while updating data.'], 500);
        }
    }
    
}


