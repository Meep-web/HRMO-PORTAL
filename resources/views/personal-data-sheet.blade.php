@extends('layouts.master')

@section('title', 'Personal Data Sheet')
@vite(['resources/css/pds.css', 'resources/js/pds.js']) <!-- Include CSS & JS -->
<input type="hidden" id="saveNosaRoute" value="{{ route('save.nosa') }}" />
<meta name="csrf-token" content="{{ csrf_token() }}">


@section('content')
    <div class="search-container">
        <div class="search-bar-container">
            <input type="text" class="search-bar" placeholder="🔍 Search..." />
        </div>
        <button class="upload-button" id="uploadButton">Upload</button>
    </div>
   
    <div class="table-container">
        <table class="salary-adjustment-table">
            <thead>
                <tr>
                    <th>
                        <a href="{{ route('personalDataSheet', ['sort' => 'first_name', 'order' => $order === 'asc' ? 'desc' : 'asc']) }}">
                            Employee Name
                            @if ($order === 'asc')
                                <span>&#9650;</span>  <!-- Ascending Arrow -->
                            @else
                                <span>&#9660;</span>  <!-- Descending Arrow -->
                            @endif
                        </a>
                    </th>
                    <th>Last Updated</th>
                    <th>Action</th>
                    <th>Updated By</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($personalInfos as $personalInfo)
                    <tr>
                        <td>{{ $personalInfo->first_name }} {{ $personalInfo->last_name }}</td>
                        <td>{{ $personalInfo->updated_at ? $personalInfo->updated_at->format('F j, Y h:i A') : 'N/A' }}</td>
                        <td>
                            <!-- Edit Button -->
                            <button class="btn btn-primary edit-btn" data-id="{{ $personalInfo->id }}">Edit</button>
                            <!-- Print Button (No functionality yet) -->
                            <button class="btn btn-secondary print-btn" data-id="{{ $personalInfo->id }}">Print</button>
                        </td>
                        <td>{{ $personalInfo->updated_by }}</td> <!-- Now fetching from pdsupdates -->
                    </tr>
                @endforeach
            </tbody>            
        </table>
    </div>
    

    <!-- Modal -->
    <div id="uploadModal" class="modal">
        <div class="modal-content-pds">
            <span class="close">&times;</span>
            <h2>Personal Data Sheet (CSC Form 212 Revised 2017)</h2>
            <form id="uploadForm">
                <input type="hidden" id="currentEmployeeName" name="currentEmployeeName" value="{{ session('employeeName') }}" />
                <input type="hidden" id="personalInfoId" name="personal_info_id" value="">
                <!-- Step 1: Personal Information -->
                <div id="step1" class="form-step">
                    <!-- Upload Field and Extract Data Button -->
                    <div class="form-group">
                        <label for="excelUpload">Upload Personal Data Sheet Excel File:</label>
                        <input type="file" id="excelUpload" name="excelUpload" accept=".xlsx, .xls">

                        <!-- Extract Data Button -->
                        <button type="button" class="action-button" id="extractDataButton">Extract Data</button>

                        <!-- Download PDS Excel Button -->
                        <button type="button" class="action-button" id="downloadButton">Download PDS Excel</button>

                    </div>

                    <hr>
                    <fieldset>
                        <legend>Personal Information</legend>

                        <!-- Name Fields -->
                        <div class="name-field-row">
                            <div class="name-field full-width">
                                <label for="surname">Surname</label>
                                <input type="text" id="surname" required>
                            </div>
                        </div>

                        <div class="name-field-row">
                            <div class="name-field half-width">
                                <label for="firstName">First Name</label>
                                <input type="text" id="firstName" required>
                            </div>
                            <div class="name-field quarter-width">
                                <label for="nameExtension">Name Extension (e.g., Jr., Sr.):</label>
                                <input type="text" id="nameExtension" name="nameExtension">
                            </div>
                        </div>

                        <div class="name-field-row">
                            <div class="name-field full-width">
                                <label for="middleName">Middle Name</label>
                                <input type="text" id="middleName">
                            </div>
                        </div>

                        <!-- Date of Birth, Place of Birth, and Sex (Left Column) -->
                        <div class="form-row birth-sex-citizenship">
                            <div class="form-group left-column">
                                <label for="dateOfBirth">Date of Birth:</label>
                                <input type="date" id="dateOfBirth" name="dateOfBirth" required>

                                <label for="placeOfBirth">Place of Birth:</label>
                                <input type="text" id="placeOfBirth" name="placeOfBirth" required>

                                <!-- Sex Section -->

                                <label>Sex:</label>
                                <div class="horizontal-radio-group">
                                    <div class="radio-option">
                                        <input type="radio" id="sexMale" name="sex" value="Male" required>
                                        <label for="sexMale">Male</label>
                                    </div>
                                    <div class="radio-option">
                                        <input type="radio" id="sexFemale" name="sex" value="Female" required>
                                        <label for="sexFemale">Female</label>
                                    </div>
                                </div>



                            </div>

                            <div class="form-group right-column">
                                <label>Citizenship:</label>
                                <div class="citizenship-options">
                                    <!-- Filipino Checkbox -->
                                    <div class="citizenship-option">
                                        <input type="checkbox" id="filipino" name="citizenship" value="Filipino">
                                        <label for="filipino">Filipino</label>
                                    </div>

                                    <!-- Dual Citizenship Checkbox & Sub-options -->
                                    <div class="citizenship-option">
                                        <input type="checkbox" id="dualCitizenship" name="citizenship"
                                            value="Dual Citizenship">
                                        <label for="dualCitizenship">Dual Citizenship</label>

                                        <!-- Dual Citizenship Sub-options -->
                                        <div class="sub-options">
                                            <div class="radio-group">
                                                <div class="radio-option">
                                                    <input type="radio" id="byBirth" name="dualCitizenType"
                                                        value="byBirth">
                                                    <label for="byBirth">By Birth</label>
                                                </div>
                                                <div class="radio-option">
                                                    <input type="radio" id="byNaturalization" name="dualCitizenType"
                                                        value="byNaturalization">
                                                    <label for="byNaturalization">By Naturalization</label>
                                                </div>
                                            </div>

                                            <div class="form-group-country">
                                                <label>Please indicate country:</label>
                                                <select id="countrySelect" class="country-select"
                                                    name="dualCitizenshipCountry">
                                                    <option value="">Loading countries...</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>



                        </div>

                        <!-- Second Form Row -->
                        <!-- Residential Address Section (on Top) -->
                        <div class="form-row second-row">
                            <!-- Left Column: Civil Status, Height, and Weight -->
                            <div class="form-column left-column">
                                <!-- Civil Status Group -->
                                <div class="form-group civil-status-group">
                                    <label>Civil Status:</label>
                                    <div class="radio-group-columns">
                                        <!-- Column 1 -->
                                        <div class="radio-group">
                                            <div class="radio-option">
                                                <input type="radio" id="civilStatusSingle" name="civilStatus"
                                                    value="Single" required>
                                                <label for="civilStatusSingle">Single</label>
                                            </div>
                                            <div class="radio-option">
                                                <input type="radio" id="civilStatusWidowed" name="civilStatus"
                                                    value="Widowed" required>
                                                <label for="civilStatusWidowed">Widowed</label>
                                            </div>
                                        </div>
                                        <!-- Column 2 -->
                                        <div class="radio-group">
                                            <div class="radio-option">
                                                <input type="radio" id="civilStatusMarried" name="civilStatus"
                                                    value="Married" required>
                                                <label for="civilStatusMarried">Married</label>
                                            </div>
                                            <div class="radio-option">
                                                <input type="radio" id="civilStatusSeparated" name="civilStatus"
                                                    value="Separated" required>
                                                <label for="civilStatusSeparated">Separated</label>
                                            </div>
                                        </div>
                                        <!-- Last Option (if odd) -->
                                        <div class="radio-group">
                                            <div class="radio-option">
                                                <input type="radio" id="civilStatusOther" name="civilStatus"
                                                    value="Other" required>
                                                <label for="civilStatusOther">Other</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Height -->
                                <div class="form-group">
                                    <label for="height">Height (m):</label>
                                    <input type="number" id="height" name="height" step="0.01" required>
                                </div>

                                <!-- Weight -->
                                <div class="form-group">
                                    <label for="weight">Weight (kg):</label>
                                    <input type="number" id="weight" name="weight" step="0.01" required>
                                </div>
                            </div>

                            <!-- Right Column: Residential Address Details -->
                            <div class="form-column right-column">
                                <fieldset>
                                    <legend>Residential Address</legend>
                                    <!-- Two-Column Layout for Address Fields -->
                                    <div class="residential-address-columns">
                                        <!-- Left Sub-column -->
                                        <div class="residential-col">
                                            <div class="form-group">
                                                <label for="residentialProvince">Province:</label>
                                                <select id="residentialProvince" name="residentialProvince" required>
                                                    <option value="">Select Province</option>
                                                    <!-- More options -->
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="residentialCity">City/Municipality:</label>
                                                <select id="residentialCity" name="residentialCity">
                                                    <option value="">Select City/Municipality</option>
                                                    <!-- More options -->
                                                </select>
                                            </div>

                                            <div class="form-group" style="display: none;">
                                                <label for="cityTextField">Selected City:</label>
                                                <input type="text" id="cityTextField" name="cityTextField" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label for="residentialBarangay">Barangay:</label>
                                                <select id="residentialBarangay" name="residentialBarangay" required>
                                                    <option value="">Select Barangay</option>
                                                    <!-- More options -->
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Right Sub-column -->
                                        <div class="residential-col">
                                            <div class="form-group">
                                                <label for="residentialSubdivision">Subdivision/Village:</label>
                                                <input type="text" id="residentialSubdivision"
                                                    name="residentialSubdivision">
                                            </div>
                                            <div class="form-group">
                                                <label for="residentialStreet">Street:</label>
                                                <input type="text" id="residentialStreet" name="residentialStreet"
                                                    required>
                                            </div>
                                            <div class="form-group">
                                                <label for="residentialHouseNo">House/Block/Lot No.:</label>
                                                <input type="text" id="residentialHouseNo" name="residentialHouseNo"
                                                    required>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Full-Width Zip Code Field -->
                                    <div class="form-group full-width">
                                        <label for="residentialZipCode">Zip Code:</label>
                                        <input type="text" id="residentialZipCode" name="residentialZipCode" required>
                                    </div>
                                </fieldset>
                            </div>
                        </div>

                        <!-- Permanent Address Section (Below Residential Address) -->
                        <div class="form-row second-row">
                            <!-- Left Column: Blood Type, Government IDs, and Contact Information -->
                            <div class="form-column">
                                <!-- Blood Type -->
                                <div class="form-group">
                                    <label for="bloodType">Blood Type:</label>
                                    <input type="text" id="bloodType" name="bloodType" required>
                                </div>
                                <!-- Government IDs -->
                                <div class="form-group">
                                    <label for="gsisId">GSIS ID No.:</label>
                                    <input type="text" id="gsisId" name="gsisId">
                                </div>
                                <div class="form-group">
                                    <label for="pagibigId">PAGIBIG ID No.:</label>
                                    <input type="text" id="pagibigId" name="pagibigId">
                                </div>
                                <div class="form-group">
                                    <label for="philhealthId">Philhealth ID No.:</label>
                                    <input type="text" id="philhealthId" name="philhealthId">
                                </div>
                                <div class="form-group">
                                    <label for="sssNo">SSS No.:</label>
                                    <input type="text" id="sssNo" name="sssNo">
                                </div>
                                <div class="form-group">
                                    <label for="tinNo">TIN No.:</label>
                                    <input type="text" id="tinNo" name="tinNo">
                                </div>
                                <div class="form-group">
                                    <label for="agencyEmployeeNo">Agency Employee No.:</label>
                                    <input type="text" id="agencyEmployeeNo" name="agencyEmployeeNo">
                                </div>
                                <!-- Contact Information -->
                                <div class="form-group">
                                    <label for="telephoneNo">Telephone No.:</label>
                                    <input type="text" id="telephoneNo" name="telephoneNo">
                                </div>

                            </div>



                            <!-- Right Column: Permanent Address Details -->
                            <div class="form-column right-column">
                                <fieldset>
                                    <legend>Permanent Address</legend>

                                    <!-- Button to Copy Address -->
                                    <button type="button" class="action-button" id="copyAddress">Same as the
                                        Residential
                                        Address</button>
                                    <!-- Two-Column Layout for Address Fields -->
                                    <div class="permanent-address-columns">


                                        <!-- Left Sub-column -->
                                        <div class="permanent-col">
                                            <div class="form-group">
                                                <label for="permanentProvince">Province:</label>
                                                <select id="permanentProvince" name="permanentProvince" required>
                                                    <option value="">Select Province</option>
                                                    <!-- More options -->
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="permanentCity">City/Municipality:</label>
                                                <select id="permanentCity" name="permanentCity" required>
                                                    <option value="">Select City/Municipality</option>
                                                    <!-- More options -->
                                                </select>
                                            </div>

                                            <!-- Text Field for Permanent City -->
                                            <div class="form-group"style="display: none;">
                                                <label for="permanentCityTextField">Permanent City Name:</label>
                                                <input type="text" id="permanentCityTextField"
                                                    name="permanentCityTextField" readonly />
                                            </div>

                                            <div class="form-group">
                                                <label for="permanentBarangay">Barangay:</label>
                                                <select id="permanentBarangay" name="permanentBarangay" required>
                                                    <option value="">Select Barangay</option>
                                                    <!-- More options -->
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Right Sub-column -->
                                        <div class="permanent-col">
                                            <div class="form-group">
                                                <label for="permanentSubdivision">Subdivision/Village:</label>
                                                <input type="text" id="permanentSubdivision"
                                                    name="permanentSubdivision">
                                            </div>
                                            <div class="form-group">
                                                <label for="permanentStreet">Street:</label>
                                                <input type="text" id="permanentStreet" name="permanentStreet"
                                                    required>
                                            </div>
                                            <div class="form-group">
                                                <label for="permanentHouseNo">House/Block/Lot No.:</label>
                                                <input type="text" id="permanentHouseNo" name="permanentHouseNo"
                                                    required>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Full-Width Zip Code Field -->
                                    <div class="form-group full-width">
                                        <label for="permanentZipCode">Zip Code:</label>
                                        <input type="text" id="permanentZipCode" name="permanentZipCode" required>
                                    </div>
                                </fieldset>

                                <div class="form-group">
                                    <label for="mobileNo">Mobile No.:</label>
                                    <input type="text" id="mobileNo" name="mobileNo" required>
                                </div>

                                <div class="form-group">
                                    <label for="email">Email Address (if applicable):</label>
                                    <input type="email" id="email" name="email">
                                </div>
                            </div>

                        </div>





                    </fieldset>
                    <div class="form-actions">
                        <button type="button" class="action-button" id="cancelButton">Cancel</button>
                        <button type="button" class="action-button" id="nextButton1">Next</button>
                    </div>
                </div>


                <!-- Step 2: Family Background -->
                <div id="step2" class="family-step2-form-step" style="display: none;">
                    <fieldset>
                        <legend>Family Background</legend>

                        <!-- Two-Column Layout -->
                        <div class="family-columns">
                            <!-- Left Column: Spouse's Name, Father's Name, and Mother's Maiden Name -->
                            <div class="family-column family-left">
                                <!-- Spouse's Name Section -->
                                <div class="form-group">
                                    <label>Spouse's Name:</label>
                                    <div class="spouse-name-group">
                                        <!-- Surname -->
                                        <div class="form-group">
                                            <label for="spouseSurname">Surname:</label>
                                            <input type="text" id="spouseSurname" name="spouseSurname">
                                        </div>
                                        <!-- First Name and Name Extension on the same row -->
                                        <div class="form-row inline-fields">
                                            <div class="form-group first-name">
                                                <label for="spouseFirstName">First Name:</label>
                                                <input type="text" id="spouseFirstName" name="spouseFirstName">
                                            </div>
                                            <div class="form-group name-extension">
                                                <label for="spouseNameExtension">Name Extension:</label>
                                                <input type="text" id="spouseNameExtension"
                                                    name="spouseNameExtension">
                                            </div>
                                        </div>
                                        <!-- Other fields -->
                                        <div class="form-group">
                                            <label for="spouseMiddleName">Middle Name:</label>
                                            <input type="text" id="spouseMiddleName" name="spouseMiddleName">
                                        </div>
                                        <div class="form-group">
                                            <label for="spouseOccupation">Occupation:</label>
                                            <input type="text" id="spouseOccupation" name="spouseOccupation">
                                        </div>
                                        <div class="form-group">
                                            <label for="spouseEmployer">Employer/Business Name:</label>
                                            <input type="text" id="spouseEmployer" name="spouseEmployer">
                                        </div>
                                        <div class="form-group">
                                            <label for="spouseTelephone">Telephone Number:</label>
                                            <input type="text" id="spouseTelephone" name="spouseTelephone">
                                        </div>
                                    </div>
                                </div>

                                <!-- Father's Name Section -->
                                <div class="form-group">
                                    <label>Father's Name:</label>
                                    <div class="father-name-group">
                                        <!-- Surname -->
                                        <div class="form-group">
                                            <label for="fatherSurname">Surname:</label>
                                            <input type="text" id="fatherSurname" name="fatherSurname">
                                        </div>
                                        <!-- First Name and Name Extension on the same row -->
                                        <div class="form-row inline-fields">
                                            <div class="form-group first-name">
                                                <label for="fatherFirstName">First Name:</label>
                                                <input type="text" id="fatherFirstName" name="fatherFirstName">
                                            </div>
                                            <div class="form-group name-extension">
                                                <label for="fatherNameExtension">Name Extension:</label>
                                                <input type="text" id="fatherNameExtension"
                                                    name="fatherNameExtension">
                                            </div>
                                        </div>
                                        <!-- Middle Name -->
                                        <div class="form-group">
                                            <label for="fatherMiddleName">Middle Name:</label>
                                            <input type="text" id="fatherMiddleName" name="fatherMiddleName">
                                        </div>
                                    </div>
                                </div>

                                <!-- Mother's Maiden Name Section -->
                                <div class="form-group">
                                    <label>Mother's Maiden Name:</label>
                                    <div class="mother-name-group">
                                        <!-- Surname -->
                                        <div class="form-group">
                                            <label for="motherSurname">Surname:</label>
                                            <input type="text" id="motherSurname" name="motherSurname">
                                        </div>
                                        <!-- First Name -->
                                        <div class="form-group">
                                            <label for="motherFirstName">First Name:</label>
                                            <input type="text" id="motherFirstName" name="motherFirstName">
                                        </div>
                                        <!-- Middle Name -->
                                        <div class="form-group">
                                            <label for="motherMiddleName">Middle Name:</label>
                                            <input type="text" id="motherMiddleName" name="motherMiddleName">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column: Children Section -->
                            <div class="family-column family-right">
                                <!-- Children Section -->
                                <div class="form-group">
                                    <label>Name of Children:</label>
                                    <div id="childrenContainer">
                                        <!-- Initial Child Input -->
                                        <div class="child-entry">
                                            <div class="form-row">
                                                <div class="form-group child-name">
                                                    <label for="childName1">Child's Name:</label>
                                                    <input type="text" id="childName1" name="childName1">
                                                </div>
                                                <div class="form-group child-dob">
                                                    <label for="childDOB1">Date of Birth:</label>
                                                    <input type="date" id="childDOB1" name="childDOB1">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Buttons: Add Another & Remove Last Child -->
                                    <div class="button-group">
                                        <button type="button" class="action-button" id="addChildButton">Add
                                            Another</button>
                                        <button type="button" class="action-button"
                                            id="removeChildButton">Remove</button>
                                    </div>
                                </div>
                            </div>

                        </div><!-- End of family-columns -->
                    </fieldset>

                    <!-- Form Actions -->
                    <div class="form-actions">
                        <button type="button" class="action-button" id="cancelButton">Cancel</button>
                        <button type="button" class="action-button" id="prevButton2">Previous</button>
                        <button type="button" class="action-button" id="nextButton2">Next</button>
                    </div>
                </div>

                <!-- Step 3: Educational Background -->
                <div id="step3" class="form-step" style="display: none;">
                    <fieldset>
                        <legend>Educational Background</legend>

                        <table class="education-table">
                            <!-- Elementary -->
                            <tr>
                                <th colspan="2">Elementary</th>
                            </tr>
                            <tr>
                                <td>Name of School:</td>
                                <td><input type="text" id="elementarySchool" name="elementarySchool"></td>
                            </tr>
                            <tr>
                                <td>Basic Education/Degree/Course:</td>
                                <td><input type="text" id="elementaryDegree" name="elementaryDegree"></td>
                            </tr>
                            <tr>
                                <td>Attendance Period (From):</td>
                                <td><input type="date" id="elementaryFrom" name="elementaryFrom"></td>
                            </tr>
                            <tr>
                                <td>Attendance Period (To):</td>
                                <td><input type="date" id="elementaryTo" name="elementaryTo"></td>
                            </tr>
                            <tr>
                                <td>Highest Level/Units Earned (if not graduated):</td>
                                <td><input type="text" id="elementaryHighestLevel" name="elementaryHighestLevel"></td>
                            </tr>
                            <tr>
                                <td>Scholarship/Academic Honors Received:</td>
                                <td><input type="text" id="elementaryHonors" name="elementaryHonors"></td>
                            </tr>
                            <tr>
                                <td>Year Graduated:</td>
                                <td><input type="number" id="elementaryYearGraduated" name="elementaryYearGraduated">
                                </td>
                            </tr>

                            <!-- Secondary -->
                            <tr>
                                <th colspan="2">Secondary</th>
                            </tr>
                            <tr>
                                <td>Name of School:</td>
                                <td><input type="text" id="secondarySchool" name="secondarySchool"></td>
                            </tr>
                            <tr>
                                <td>Basic Education/Degree/Course:</td>
                                <td><input type="text" id="secondaryDegree" name="secondaryDegree"></td>
                            </tr>
                            <tr>
                                <td>Attendance Period (From):</td>
                                <td><input type="date" id="secondaryFrom" name="secondaryFrom"></td>
                            </tr>
                            <tr>
                                <td>Attendance Period (To):</td>
                                <td><input type="date" id="secondaryTo" name="secondaryTo"></td>
                            </tr>
                            <tr>
                                <td>Highest Level/Units Earned (if not graduated):</td>
                                <td><input type="text" id="secondaryHighestLevel" name="secondaryHighestLevel"></td>
                            </tr>
                            <tr>
                                <td>Scholarship/Academic Honors Received:</td>
                                <td><input type="text" id="secondaryHonors" name="secondaryHonors"></td>
                            </tr>
                            <tr>
                                <td>Year Graduated:</td>
                                <td><input type="number" id="secondaryYearGraduated" name="secondaryYearGraduated"></td>
                            </tr>

                            <!-- Vocational/Trade Course -->
                            <tr>
                                <th colspan="2">Vocational/Trade Course</th>
                            </tr>
                            <tr>
                                <td>Name of School:</td>
                                <td><input type="text" id="vocationalSchool" name="vocationalSchool"></td>
                            </tr>
                            <tr>
                                <td>Basic Education/Degree/Course:</td>
                                <td><input type="text" id="vocationalDegree" name="vocationalDegree"></td>
                            </tr>
                            <tr>
                                <td>Attendance Period (From):</td>
                                <td><input type="date" id="vocationalFrom" name="vocationalFrom"></td>
                            </tr>
                            <tr>
                                <td>Attendance Period (To):</td>
                                <td><input type="date" id="vocationalTo" name="vocationalTo"></td>
                            </tr>
                            <tr>
                                <td>Highest Level/Units Earned (if not graduated):</td>
                                <td><input type="text" id="vocationalHighestLevel" name="vocationalHighestLevel"></td>
                            </tr>
                            <tr>
                                <td>Scholarship/Academic Honors Received:</td>
                                <td><input type="text" id="vocationalHonors" name="vocationalHonors"></td>
                            </tr>
                            <tr>
                                <td>Year Graduated:</td>
                                <td><input type="number" id="vocationalYearGraduated" name="vocationalYearGraduated">
                                </td>
                            </tr>

                            <!-- College -->
                            <tr>
                                <th colspan="2">College</th>
                            </tr>
                            <tr>
                                <td>Name of School:</td>
                                <td><input type="text" id="collegeSchool" name="collegeSchool"></td>
                            </tr>
                            <tr>
                                <td>Basic Education/Degree/Course:</td>
                                <td><input type="text" id="collegeDegree" name="collegeDegree"></td>
                            </tr>
                            <tr>
                                <td>Attendance Period (From):</td>
                                <td><input type="date" id="collegeFrom" name="collegeFrom"></td>
                            </tr>
                            <tr>
                                <td>Attendance Period (To):</td>
                                <td><input type="date" id="collegeTo" name="collegeTo"></td>
                            </tr>
                            <tr>
                                <td>Highest Level/Units Earned (if not graduated):</td>
                                <td><input type="text" id="collegeHighestLevel" name="collegeHighestLevel"></td>
                            </tr>
                            <tr>
                                <td>Scholarship/Academic Honors Received:</td>
                                <td><input type="text" id="collegeHonors" name="collegeHonors"></td>
                            </tr>
                            <tr>
                                <td>Year Graduated:</td>
                                <td><input type="number" id="collegeYearGraduated" name="collegeYearGraduated"></td>
                            </tr>

                            <!-- Graduate Studies -->
                            <tr>
                                <th colspan="2">Graduate Studies</th>
                            </tr>
                            <tr>
                                <td>Name of School:</td>
                                <td><input type="text" id="graduateSchool" name="graduateSchool"></td>
                            </tr>
                            <tr>
                                <td>Basic Education/Degree/Course:</td>
                                <td><input type="text" id="graduateDegree" name="graduateDegree"></td>
                            </tr>
                            <tr>
                                <td>Attendance Period (From):</td>
                                <td><input type="date" id="graduateFrom" name="graduateFrom"></td>
                            </tr>
                            <tr>
                                <td>Attendance Period (To):</td>
                                <td><input type="date" id="graduateTo" name="graduateTo"></td>
                            </tr>
                            <tr>
                                <td>Highest Level/Units Earned (if not graduated):</td>
                                <td><input type="text" id="graduateHighestLevel" name="graduateHighestLevel"></td>
                            </tr>
                            <tr>
                                <td>Scholarship/Academic Honors Received:</td>
                                <td><input type="text" id="graduateHonors" name="graduateHonors"></td>
                            </tr>
                            <tr>
                                <td>Year Graduated:</td>
                                <td><input type="number" id="graduateYearGraduated" name="graduateYearGraduated"></td>
                            </tr>
                        </table>
                    </fieldset>

                    <!-- Form Actions -->
                    <div class="form-actions">
                        <button type="button" class="action-button" id="cancelButton">Cancel</button>
                        <button type="button" class="action-button" id="prevButton3">Previous</button>
                        <button type="button" class="action-button" id="nextButton3">Next</button>
                    </div>
                </div>


                <!-- Step 4: Civil Service Eligibility -->
                <div id="step4" class="form-step" style="display: none;">
                    <fieldset>
                        <legend>Civil Service Eligibility</legend>

                        <!-- Civil Service Table -->
                        <table class="civilServiceContainer">
                            <thead>
                                <tr>
                                    <th>Eligibility Name</th>
                                    <th>Rating (if applicable)</th>
                                    <th>Date of Exam/Conferment</th>
                                    <th>Place of Exam/Conferment</th>
                                    <th>License Number (if applicable)</th>
                                    <th>Date of Validity</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="civilServiceContainer">
                                <!-- Rows will be dynamically inserted here -->
                            </tbody>
                        </table>
                        <button type="button" id="addCivilServiceButton">Add Another</button>
                    </fieldset>

                    <div class="form-actions">
                        <button type="button" class="action-button" id="cancelButton">Cancel</button>
                        <button type="button" class="action-button" id="prevButton4">Previous</button>
                        <button type="button" class="action-button" id="nextButton4">Next</button>
                    </div>
                </div>


                <!-- Step 5: Work Experience -->
                <div id="step5" class="form-step" style="display: none;">
                    <fieldset>
                        <legend>Work Experience</legend>

                        <div id="workExperienceContainer">
                            <!-- Work Experience Table -->
                            <table class="work-experience-table">
                                <thead>
                                    <tr>
                                        <th>Inclusive Dates (From)</th>
                                        <th>Inclusive Dates (To)</th>
                                        <th>Position Title</th>
                                        <th>Department/Agency/Office/Company</th>
                                        <th>Monthly Salary</th>
                                        <th>Salary/Job/Pay Grade</th>
                                        <th>Step Increment</th>
                                        <th>Status of Appointment</th>
                                        <th>Gov't Service</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Rows will be dynamically inserted here -->
                                </tbody>
                            </table>

                            <!-- Add Another Button -->

                        </div>
                        <button type="button" class="action-button" id="addWorkExperienceButton">Add
                            Another</button>
                    </fieldset>

                    <div class="form-actions">
                        <button type="button" class="action-button" id="cancelButton">Cancel</button>
                        <button type="button" class="action-button" id="prevButton5">Previous</button>
                        <button type="button" class="action-button" id="submitButton">Submit</button>
                        <button type="button" class="action-button" id="savePDSButton" style="display: none;">Save</button>
                    </div>
                    
                </div>
                
                

            </form>
        </div>
    </div>
@endsection
