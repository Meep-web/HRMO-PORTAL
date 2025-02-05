@extends('layouts.master')

@section('title', 'Personal Data Sheet')
@vite(['resources/css/pds.css', 'resources/js/pds.js']) <!-- Include CSS & JS -->

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
                    <th>Department</th>
                    <th>Last Updated</th>
                    <th>Action</th>
                    <th>Updated By</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Human Resources</td>
                    <td>2023-10-15</td>
                    <td>
                        <button class="action-button">Edit</button>
                        <button class="action-button">Delete</button>
                    </td>
                    <td>Admin</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Modal -->
    <div id="uploadModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Personal Data Sheet (CSC Form 212 Revised 2017)</h2>
            <form id="uploadForm">

                <!-- Step 1: Personal Information -->
                <div id="step1" class="form-step">
                    <!-- Upload Field and Extract Data Button -->
                    <div class="form-group">
                        <label for="excelUpload">Upload Excel File:</label>
                        <input type="file" id="excelUpload" name="excelUpload" accept=".xlsx, .xls">

                        <!-- Extract Data Button -->
                        <button type="button" class="action-button" id="extractDataButton">Extract Data</button>

                        <!-- Download PDS Excel Button -->
                        <a href="{{ asset('docs/CS Form No. 212 Personal Data Sheet revised.xlsx') }}" download>
                            <button type="button" class="action-button" id="downloadButton">Download PDS Excel</button>
                        </a>
                    </div>

                    <hr>
                    <fieldset>
                        <legend>Personal Information</legend>

                        <!-- Name Fields -->
                        <div class="form-row">
                            <div class="form-group required">
                                <label for="surname">Surname</label>
                                <input type="text" id="surname" required>
                            </div>
                            <div class="form-group required">
                                <label for="firstName">First Name</label>
                                <input type="text" id="firstName" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="middleName">Middle Name</label>
                                <input type="text" id="middleName">
                            </div>
                            <div class="form-group required">
                                <label for="nameExtension">Name Extension (e.g., Jr., Sr.):</label>
                                <input type="text" id="nameExtension" name="nameExtension">
                            </div>
                        </div>

                        <!-- Date of Birth and Place of Birth -->
                        <div class="form-row">
                            <div class="form-group">
                                <label for="dateOfBirth">Date of Birth:</label>
                                <input type="date" id="dateOfBirth" name="dateOfBirth" required>
                            </div>
                            <div class="form-group">
                                <label for="placeOfBirth">Place of Birth:</label>
                                <input type="text" id="placeOfBirth" name="placeOfBirth" required>
                            </div>
                        </div>

                        <!-- Sex and Civil Status -->
                        <div class="form-row">
                            <div class="form-group sex-group">
                                <label>Sex</label>
                                <div class="radio-group">
                                    <div class="radio-option">
                                        <input type="radio" id="sexMale" name="sex" value="Male" required>
                                        <label for="sexMale">Male</label>
                                    </div>
                                    <div class="radio-option">
                                        <input type="radio" id="sexFemale" name="sex" value="Female" required>
                                        <label for="sexFemale">Female</label>
                                    </div>
                                </div>
                                <div class="radio-group">
                                </div>
                                <div class="radio-group">
                                </div>
                                <div class="radio-group">
                                </div>
                            </div>

                            <div class="form-group civil-status-group">
                                <label>Civil Status:</label>
                                <div class="radio-group-columns">
                                    <!-- Column 1 -->
                                    <div class="radio-group">
                                        <div class="radio-option">
                                            <input type="radio" id="civilStatusSingle" name="civilStatus" value="Single"
                                                required>
                                            <label for="civilStatusSingle">Single</label>
                                        </div>
                                        <div class="radio-option">
                                            <input type="radio" id="civilStatusWidowed" name="civilStatus" value="Widowed"
                                                required>
                                            <label for="civilStatusWidowed">Widowed</label>
                                        </div>
                                    </div>

                                    <!-- Column 2 -->
                                    <div class="radio-group">
                                        <div class="radio-option">
                                            <input type="radio" id="civilStatusMarried" name="civilStatus" value="Married"
                                                required>
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
                        </div>

                        <!-- Height, Weight, and Blood Type -->
                        <div class="form-group">
                            <label for="height">Height (m):</label>
                            <input type="number" id="height" name="height" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label for="weight">Weight (kg):</label>
                            <input type="number" id="weight" name="weight" step="0.01" required>
                        </div>
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

                        <!-- Citizenship -->
                        <div class="form-group">
                            <label>Citizenship:</label>

                            <!-- Filipino Checkbox -->
                            <div class="citizenship-option">
                                <input type="checkbox" id="filipino" name="citizenship" value="Filipino">
                                <label for="filipino">Filipino</label>
                            </div>

                            <!-- Dual Citizenship Checkbox -->
                            <div class="citizenship-option">
                                <input type="checkbox" id="dualCitizenship" name="citizenship" value="Dual Citizenship">
                                <label for="dualCitizenship">Dual Citizenship</label>

                                <!-- Dual Citizenship Sub-options -->
                                <div class="sub-options" style="display: none;">
                                    <div class="radio-group">
                                        <div class="radio-option">
                                            <input type="radio" id="byBirth" name="dualCitizenType" value="byBirth">
                                            <label for="byBirth">By Birth</label>
                                        </div>
                                        <div class="radio-option">
                                            <input type="radio" id="byNaturalization" name="dualCitizenType"
                                                value="byNaturalization">
                                            <label for="byNaturalization">By Naturalization</label>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>Please indicate country:</label>
                                        <select id="countrySelect" class="country-select" name="dualCitizenshipCountry">
                                            <option value="">Loading countries...</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Residential Address -->
                        <fieldset>
                            <legend>Residential Address</legend>
                            <div class="form-group">
                                <label for="residentialProvince">Province:</label>
                                <select id="residentialProvince" name="residentialProvince" required>
                                    <option value="">Select Province</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="residentialCity">City/Municipality:</label>
                                <select id="residentialCity" name="residentialCity" required>
                                    <option value="">Select City/Municipality</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="residentialBarangay">Barangay:</label>
                                <select id="residentialBarangay" name="residentialBarangay" required>
                                    <option value="">Select Barangay</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="residentialSubdivision">Subdivision/Village:</label>
                                <input type="text" id="residentialSubdivision" name="residentialSubdivision">
                            </div>
                            <div class="form-group">
                                <label for="residentialStreet">Street:</label>
                                <input type="text" id="residentialStreet" name="residentialStreet" required>
                            </div>
                            <div class="form-group">
                                <label for="residentialHouseNo">House/Block/Lot No.:</label>
                                <input type="text" id="residentialHouseNo" name="residentialHouseNo" required>
                            </div>

                            <div class="form-group">
                                <label for="residentialZipCode">Zip Code:</label>
                                <input type="text" id="residentialZipCode" name="residentialZipCode" required>
                            </div>
                        </fieldset>

                        <!-- Button to Copy Address -->
                        <button type="button" class="action-button" id="copyAddress">Same as the Residential
                            Address</button>

                        <!-- Permanent Address -->
                        <fieldset>
                            <legend>Permanent Address</legend>
                            <div class="form-group">
                                <label for="permanentProvince">Province:</label>
                                <select id="permanentProvince" name="permanentProvince" required>
                                    <option value="">Select Province</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="permanentCity">City/Municipality:</label>
                                <select id="permanentCity" name="permanentCity" required>
                                    <option value="">Select City/Municipality</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="permanentBarangay">Barangay:</label>
                                <select id="permanentBarangay" name="permanentBarangay" required>
                                    <option value="">Select Barangay</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="permanentSubdivision">Subdivision/Village:</label>
                                <input type="text" id="permanentSubdivision" name="permanentSubdivision">
                            </div>
                            <div class="form-group">
                                <label for="permanentStreet">Street:</label>
                                <input type="text" id="permanentStreet" name="permanentStreet" required>
                            </div>
                            <div class="form-group">
                                <label for="permanentHouseNo">House/Block/Lot No.:</label>
                                <input type="text" id="permanentHouseNo" name="permanentHouseNo" required>
                            </div>

                            <div class="form-group">
                                <label for="permanentZipCode">Zip Code:</label>
                                <input type="text" id="permanentZipCode" name="permanentZipCode" required>
                            </div>
                        </fieldset>

                        <!-- Contact Information -->
                        <div class="form-group">
                            <label for="telephoneNo">Telephone No.:</label>
                            <input type="text" id="telephoneNo" name="telephoneNo">
                        </div>
                        <div class="form-group">
                            <label for="mobileNo">Mobile No.:</label>
                            <input type="text" id="mobileNo" name="mobileNo" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email Address (if applicable):</label>
                            <input type="email" id="email" name="email">
                        </div>
                    </fieldset>
                    <div class="form-actions">
                        <button type="button" class="action-button" id="cancelButton">Cancel</button>
                        <button type="button" class="action-button" id="nextButton1">Next</button>
                    </div>
                </div>

                <!-- Step 2: Family Background -->
                <div id="step2" class="form-step" style="display: none;">
                    <fieldset>
                        <legend>Family Background</legend>

                        <!-- Spouse's Name Section -->
                        <div class="form-group">
                            <label>Spouse's Name:</label>
                            <div class="spouse-name-group">
                                <!-- Surname -->
                                <div class="form-group">
                                    <label for="spouseSurname">Surname:</label>
                                    <input type="text" id="spouseSurname" name="spouseSurname">
                                </div>

                                <!-- First Name and Name Extension (side by side) -->
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="spouseFirstName">First Name:</label>
                                        <input type="text" id="spouseFirstName" name="spouseFirstName">
                                    </div>
                                    <div class="form-group">
                                        <label for="spouseNameExtension">Name Extension:</label>
                                        <input type="text" id="spouseNameExtension" name="spouseNameExtension">
                                    </div>
                                </div>

                                <!-- Middle Name -->
                                <div class="form-group">
                                    <label for="spouseMiddleName">Middle Name:</label>
                                    <input type="text" id="spouseMiddleName" name="spouseMiddleName">
                                </div>

                                <!-- Occupation -->
                                <div class="form-group">
                                    <label for="spouseOccupation">Occupation:</label>
                                    <input type="text" id="spouseOccupation" name="spouseOccupation">
                                </div>

                                <!-- Employer/Business Name -->
                                <div class="form-group">
                                    <label for="spouseEmployer">Employer/Business Name:</label>
                                    <input type="text" id="spouseEmployer" name="spouseEmployer">
                                </div>

                                <!-- Telephone Number -->
                                <div class="form-group">
                                    <label for="spouseTelephone">Telephone Number:</label>
                                    <input type="text" id="spouseTelephone" name="spouseTelephone">
                                </div>
                            </div>
                        </div>

                        <!-- Children Section -->
                        <div class="form-group">
                            <label>Name of Children:</label>
                            <div id="childrenContainer">
                                <!-- Initial Child Input -->
                                <div class="child-entry">
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="childName1">Child's Name:</label>
                                            <input type="text" id="childName1" name="childName1">
                                        </div>
                                        <div class="form-group">
                                            <label for="childDOB1">Date of Birth:</label>
                                            <input type="date" id="childDOB1" name="childDOB1">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Add Another Button -->
                            <button type="button" class="action-button" id="addChildButton">Add Another</button>
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

                                <!-- First Name and Name Extension (side by side) -->
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="fatherFirstName">First Name:</label>
                                        <input type="text" id="fatherFirstName" name="fatherFirstName">
                                    </div>
                                    <div class="form-group">
                                        <label for="fatherNameExtension">Name Extension:</label>
                                        <input type="text" id="fatherNameExtension" name="fatherNameExtension">
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

                        <!-- Elementary -->
                        <div class="education-level-group">
                            <h3>Elementary</h3>
                            <div class="form-group">
                                <label for="elementarySchool">Name of School:</label>
                                <input type="text" id="elementarySchool" name="elementarySchool">
                            </div>
                            <div class="form-group">
                                <label for="elementaryDegree">Basic Education/Degree/Course:</label>
                                <input type="text" id="elementaryDegree" name="elementaryDegree">
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="elementaryFrom">Attendance Period (From):</label>
                                    <input type="date" id="elementaryFrom" name="elementaryFrom">
                                </div>
                                <div class="form-group">
                                    <label for="elementaryTo">Attendance Period (To):</label>
                                    <input type="date" id="elementaryTo" name="elementaryTo">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="elementaryHighestLevel">Highest Level/Units Earned (if not graduated):</label>
                                <input type="text" id="elementaryHighestLevel" name="elementaryHighestLevel">
                            </div>
                            <div class="form-group">
                                <label for="elementaryHonors">Scholarship/Academic Honors Received:</label>
                                <input type="text" id="elementaryHonors" name="elementaryHonors">
                            </div>
                        </div>

                        <!-- Secondary -->
                        <div class="education-level-group">
                            <h3>Secondary</h3>
                            <div class="form-group">
                                <label for="secondarySchool">Name of School:</label>
                                <input type="text" id="secondarySchool" name="secondarySchool">
                            </div>
                            <div class="form-group">
                                <label for="secondaryDegree">Basic Education/Degree/Course:</label>
                                <input type="text" id="secondaryDegree" name="secondaryDegree">
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="secondaryFrom">Attendance Period (From):</label>
                                    <input type="date" id="secondaryFrom" name="secondaryFrom">
                                </div>
                                <div class="form-group">
                                    <label for="secondaryTo">Attendance Period (To):</label>
                                    <input type="date" id="secondaryTo" name="secondaryTo">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="secondaryHighestLevel">Highest Level/Units Earned (if not graduated):</label>
                                <input type="text" id="secondaryHighestLevel" name="secondaryHighestLevel">
                            </div>
                            <div class="form-group">
                                <label for="secondaryHonors">Scholarship/Academic Honors Received:</label>
                                <input type="text" id="secondaryHonors" name="secondaryHonors">
                            </div>
                        </div>

                        <!-- Vocational/Trade Course -->
                        <div class="education-level-group">
                            <h3>Vocational/Trade Course</h3>
                            <div class="form-group">
                                <label for="vocationalSchool">Name of School:</label>
                                <input type="text" id="vocationalSchool" name="vocationalSchool">
                            </div>
                            <div class="form-group">
                                <label for="vocationalDegree">Basic Education/Degree/Course:</label>
                                <input type="text" id="vocationalDegree" name="vocationalDegree">
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="vocationalFrom">Attendance Period (From):</label>
                                    <input type="date" id="vocationalFrom" name="vocationalFrom">
                                </div>
                                <div class="form-group">
                                    <label for="vocationalTo">Attendance Period (To):</label>
                                    <input type="date" id="vocationalTo" name="vocationalTo">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="vocationalHighestLevel">Highest Level/Units Earned (if not graduated):</label>
                                <input type="text" id="vocationalHighestLevel" name="vocationalHighestLevel">
                            </div>
                            <div class="form-group">
                                <label for="vocationalHonors">Scholarship/Academic Honors Received:</label>
                                <input type="text" id="vocationalHonors" name="vocationalHonors">
                            </div>
                        </div>

                        <!-- College -->
                        <div class="education-level-group">
                            <h3>College</h3>
                            <div class="form-group">
                                <label for="collegeSchool">Name of School:</label>
                                <input type="text" id="collegeSchool" name="collegeSchool">
                            </div>
                            <div class="form-group">
                                <label for="collegeDegree">Basic Education/Degree/Course:</label>
                                <input type="text" id="collegeDegree" name="collegeDegree">
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="collegeFrom">Attendance Period (From):</label>
                                    <input type="date" id="collegeFrom" name="collegeFrom">
                                </div>
                                <div class="form-group">
                                    <label for="collegeTo">Attendance Period (To):</label>
                                    <input type="date" id="collegeTo" name="collegeTo">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="collegeHighestLevel">Highest Level/Units Earned (if not graduated):</label>
                                <input type="text" id="collegeHighestLevel" name="collegeHighestLevel">
                            </div>
                            <div class="form-group">
                                <label for="collegeHonors">Scholarship/Academic Honors Received:</label>
                                <input type="text" id="collegeHonors" name="collegeHonors">
                            </div>
                        </div>

                        <!-- Graduate Studies -->
                        <div class="education-level-group">
                            <h3>Graduate Studies</h3>
                            <div class="form-group">
                                <label for="graduateSchool">Name of School:</label>
                                <input type="text" id="graduateSchool" name="graduateSchool">
                            </div>
                            <div class="form-group">
                                <label for="graduateDegree">Basic Education/Degree/Course:</label>
                                <input type="text" id="graduateDegree" name="graduateDegree">
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="graduateFrom">Attendance Period (From):</label>
                                    <input type="date" id="graduateFrom" name="graduateFrom">
                                </div>
                                <div class="form-group">
                                    <label for="graduateTo">Attendance Period (To):</label>
                                    <input type="date" id="graduateTo" name="graduateTo">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="graduateHighestLevel">Highest Level/Units Earned (if not graduated):</label>
                                <input type="text" id="graduateHighestLevel" name="graduateHighestLevel">
                            </div>
                            <div class="form-group">
                                <label for="graduateHonors">Scholarship/Academic Honors Received:</label>
                                <input type="text" id="graduateHonors" name="graduateHonors">
                            </div>
                        </div>

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
                        <div id="civilServiceContainer">
                            <!-- Initial Civil Service Entry -->
                            <div class="civil-service-entry">
                                <div class="form-group">
                                    <label for="eligibilityName1">Eligibility Name:</label>
                                    <input type="text" id="eligibilityName1" name="eligibilityName1">
                                </div>
                                <div class="form-group">
                                    <label for="rating1">Rating(if applicable):</label>
                                    <input type="text" id="rating1" name="rating1">
                                </div>
                                <div class="form-group">
                                    <label for="dateOfExam1">Date of Exam/ Confement:</label>
                                    <input type="date" id="dateOfExam1" name="dateOfExam1">
                                </div>
                                <div class="form-group">
                                    <label for="placeOfExam1">Place of Exam/ Conferment:</label>
                                    <input type="text" id="placeOfExam1" name="placeOfExam1">
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="licenseNumber1">License Number (if applicable):</label>
                                        <input type="text" id="licenseNumber1" name="licenseNumber1">
                                    </div>
                                    <div class="form-group">
                                        <label for="licenseValidity1">Date of Validity:</label>
                                        <input type="date" id="licenseValidity1" name="licenseValidity1">
                                    </div>
                                </div>
                                <hr> <!-- Horizontal line to separate entries -->

                            </div>
                        </div>
                        <!-- Add Another Button -->
                        <button type="button" class="action-button" id="addCivilServiceButton">Add Another</button>
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
                            <!-- Initial Work Experience Entry -->
                            <div class="work-experience-entry">
                                <!-- Inclusive Dates (From and To) -->
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="inclusiveDatesFrom1">Inclusive Dates (From):</label>
                                        <input type="date" id="inclusiveDatesFrom1" name="inclusiveDatesFrom1">
                                    </div>
                                    <div class="form-group">
                                        <label for="inclusiveDatesTo1">Inclusive Dates (To):</label>
                                        <input type="date" id="inclusiveDatesTo1" name="inclusiveDatesTo1">
                                    </div>
                                </div>

                                <!-- Position Title -->
                                <div class="form-group">
                                    <label for="positionTitle1">Position Title:</label>
                                    <input type="text" id="positionTitle1" name="positionTitle1">
                                </div>

                                <!-- Department/Agency/Office/Company -->
                                <div class="form-group">
                                    <label for="department1">Department/Agency/Office/Company:</label>
                                    <input type="text" id="department1" name="department1">
                                </div>

                                <!-- Monthly Salary -->
                                <div class="form-group">
                                    <label for="monthlySalary1">Monthly Salary:</label>
                                    <input type="text" id="monthlySalary1" name="monthlySalary1">
                                </div>

                                <!-- Salary/Job/Pay Grade & Step Increment -->
                                <div class="form-group">
                                    <label for="salaryGrade1">Salary/Job/Pay Grade:</label>
                                    <input type="text" id="salaryGrade1" name="salaryGrade1">
                                </div>
                                <div class="form-group">
                                    <label for="stepIncrement1">Step Increment:</label>
                                    <input type="text" id="stepIncrement1" name="stepIncrement1">
                                </div>

                                <!-- Status of Appointment -->
                                <div class="form-group">
                                    <label for="appointmentStatus1">Status of Appointment:</label>
                                    <input type="text" id="appointmentStatus1" name="appointmentStatus1">
                                </div>

                                <!-- Gov't Service (Yes or No) -->
                                <div class="form-group">
                                    <label>Gov't Service:</label>
                                    <div class="radio-group">
                                        <div class="radio-option">
                                            <input type="radio" id="govtServiceYes1" name="govtService1"
                                                value="Yes">
                                            <label for="govtServiceYes1">Yes</label>
                                        </div>
                                        <div class="radio-option">
                                            <input type="radio" id="govtServiceNo1" name="govtService1"
                                                value="No">
                                            <label for="govtServiceNo1">No</label>
                                        </div>
                                    </div>
                                </div>

                                <hr class="separator"> <!-- Horizontal line to separate entries -->
                            </div>
                        </div>
                        <!-- Add Another Button -->
                        <button type="button" class="action-button" id="addWorkExperienceButton">Add Another</button>
                    </fieldset>
                    <div class="form-actions">
                        <button type="button" class="action-button" id="cancelButton">Cancel</button>
                        <button type="button" class="action-button" id="prevButton5">Previous</button>
                        <button type="button" class="action-button" id="submitButton">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
