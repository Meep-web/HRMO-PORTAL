import * as XLSX from "xlsx";

// Get the modal, button, and close elements
const modal = document.getElementById("uploadModal");
const uploadButton = document.getElementById("uploadButton");
const closeButton = document.querySelector(".close");

// Step navigation
let currentStep = 1;
const totalSteps = 5;

// Open the modal when the upload button is clicked
uploadButton.addEventListener("click", () => {
    modal.style.display = "block";
    showStep(currentStep);
});

// Close the modal when the close button is clicked
closeButton.addEventListener("click", () => {
    modal.style.display = "none";
    resetForm();
});

// Close the modal when clicking outside of it
window.addEventListener("click", (event) => {
    if (event.target === modal) {
        modal.style.display = "none";
        resetForm();
    }
});

// Show the current step and hide others
function showStep(step) {
    for (let i = 1; i <= totalSteps; i++) {
        document.getElementById(`step${i}`).style.display =
            i === step ? "block" : "none";
    }
}

// Next button functionality
document.getElementById("nextButton1").addEventListener("click", () => {
    currentStep = 2;
    showStep(currentStep);
});

document.getElementById("nextButton2").addEventListener("click", () => {
    currentStep = 3;
    showStep(currentStep);
});

document.getElementById("nextButton3").addEventListener("click", () => {
    currentStep = 4;
    showStep(currentStep);
});

document.getElementById("nextButton4").addEventListener("click", () => {
    currentStep = 5;
    showStep(currentStep);
});

// Previous button functionality
document.getElementById("prevButton2").addEventListener("click", () => {
    currentStep = 1;
    showStep(currentStep);
});

document.getElementById("prevButton3").addEventListener("click", () => {
    currentStep = 2;
    showStep(currentStep);
});

document.getElementById("prevButton4").addEventListener("click", () => {
    currentStep = 3;
    showStep(currentStep);
});

document.getElementById("prevButton5").addEventListener("click", () => {
    currentStep = 4;
    showStep(currentStep);
});

// Cancel button functionality
document.querySelectorAll("#cancelButton").forEach((button) => {
    button.addEventListener("click", () => {
        modal.style.display = "none";
        resetForm();
    });
});

// Submit button functionality
document.getElementById("submitButton").addEventListener("click", () => {
    // Collect all form data
    const formData = collectFormData();

    // Log the form data to the console
    console.log("Form Data:", formData);

    // Show success message
    alert("Form submitted successfully!");

    // Close the modal and reset the form
    modal.style.display = "none";
    resetForm();
});

// Function to collect form data
function collectFormData() {
    const formData = {};

    // Step 1: Personal Information
    formData.personalInfo = {
        firstName: document.getElementById("firstName").value,
        lastName: document.getElementById("surname").value,
        middleName: document.getElementById("middleName").value,
        nameExtension: document.getElementById("nameExtension").value,
        dateOfBirth: document.getElementById("dateOfBirth").value,
        placeOfBirth: document.getElementById("placeOfBirth").value,
        sex: document.querySelector('input[name="sex"]:checked')?.value || "",
        civilStatus:
            document.querySelector('input[name="civilStatus"]:checked')
                ?.value || "",
        height: document.getElementById("height").value,
        weight: document.getElementById("weight").value,
        bloodType: document.getElementById("bloodType").value,
        gsisId: document.getElementById("gsisId").value,
        pagibigId: document.getElementById("pagibigId").value,
        philhealthId: document.getElementById("philhealthId").value,
        sssNo: document.getElementById("sssNo").value,
        tinNo: document.getElementById("tinNo").value,
        agencyEmployeeNo: document.getElementById("agencyEmployeeNo").value,

        // Updated Citizenship Section
        citizenship: {
            isFilipino: document.getElementById("filipino").checked,
            isDualCitizen: document.getElementById("dualCitizenship").checked,
            dualCitizenType:
                document.querySelector('input[name="dualCitizenType"]:checked')
                    ?.value || "",
            dualCitizenCountry: document.getElementById("countrySelect").value,
        },

        // Residential Address
        residentialAddress: {
            houseNo: document.getElementById("residentialHouseNo").value,
            street: document.getElementById("residentialStreet").value,
            subdivision: document.getElementById("residentialSubdivision")
                .value,
            barangay: document.getElementById("residentialBarangay").value,
            city: document.getElementById("residentialCity").value,
            province: document.getElementById("residentialProvince").value,
            zipCode: document.getElementById("residentialZipCode").value,
        },

        // Permanent Address
        permanentAddress: {
            houseNo: document.getElementById("permanentHouseNo").value,
            street: document.getElementById("permanentStreet").value,
            subdivision: document.getElementById("permanentSubdivision").value,
            barangay: document.getElementById("permanentBarangay").value,
            city: document.getElementById("permanentCity").value,
            province: document.getElementById("permanentProvince").value,
            zipCode: document.getElementById("permanentZipCode").value,
        },

        // Contact Information
        telephoneNo: document.getElementById("telephoneNo").value,
        mobileNo: document.getElementById("mobileNo").value,
        email: document.getElementById("email").value,
    };

    // Step 2: Family Background
    formData.familyBackground = {
        // Spouse's Information
        spouse: {
            surname: document.getElementById("spouseSurname").value,
            firstName: document.getElementById("spouseFirstName").value,
            middleName: document.getElementById("spouseMiddleName").value,
            nameExtension: document.getElementById("spouseNameExtension").value,
            occupation: document.getElementById("spouseOccupation").value,
            employer: document.getElementById("spouseEmployer").value,
            telephone: document.getElementById("spouseTelephone").value,
        },

        // Children Information
        children: (() => {
            let childrenArray = [];
            let childEntries = document.querySelectorAll(
                "#childrenContainer .child-entry"
            );
            childEntries.forEach((entry, index) => {
                let name =
                    entry.querySelector(`#childName${index + 1}`)?.value || "";
                let dob =
                    entry.querySelector(`#childDOB${index + 1}`)?.value || "";
                if (name.trim()) {
                    childrenArray.push({ name, dateOfBirth: dob });
                }
            });
            return childrenArray;
        })(),

        // Father's Information
        father: {
            surname: document.getElementById("fatherSurname").value,
            firstName: document.getElementById("fatherFirstName").value,
            middleName: document.getElementById("fatherMiddleName").value,
            nameExtension: document.getElementById("fatherNameExtension").value,
        },

        // Mother's Information
        mother: {
            title: document.getElementById("motherTitle").value,
            surname: document.getElementById("motherSurname").value,
            firstName: document.getElementById("motherFirstName").value,
            middleName: document.getElementById("motherMiddleName").value,
        },
    };

    // Step 3: Educational Background
    formData.educationalBackground = {
        elementary: {
            school: document.getElementById("elementarySchool").value,
            degree: document.getElementById("elementaryDegree").value,
            from: document.getElementById("elementaryFrom").value,
            to: document.getElementById("elementaryTo").value,
            highestLevel: document.getElementById("elementaryHighestLevel")
                .value,
            yearGraduated: document.getElementById("elementaryYearGraduated")
                .value,
            honors: document.getElementById("elementaryHonors").value,
        },
        secondary: {
            school: document.getElementById("secondarySchool").value,
            degree: document.getElementById("secondaryDegree").value,
            from: document.getElementById("secondaryFrom").value,
            to: document.getElementById("secondaryTo").value,
            highestLevel: document.getElementById("secondaryHighestLevel")
                .value,
            yearGraduated: document.getElementById("secondaryYearGraduated")
                .value,
            honors: document.getElementById("secondaryHonors").value,
        },
        vocational: {
            school: document.getElementById("vocationalSchool").value,
            degree: document.getElementById("vocationalDegree").value,
            from: document.getElementById("vocationalFrom").value,
            to: document.getElementById("vocationalTo").value,
            highestLevel: document.getElementById("vocationalHighestLevel")
                .value,
            yearGraduated: document.getElementById("vocationalYearGraduated")
                .value,
            honors: document.getElementById("vocationalHonors").value,
        },
        college: {
            school: document.getElementById("collegeSchool").value,
            degree: document.getElementById("collegeDegree").value,
            from: document.getElementById("collegeFrom").value,
            to: document.getElementById("collegeTo").value,
            highestLevel: document.getElementById("collegeHighestLevel").value,
            yearGraduated: document.getElementById("collegeYearGraduated")
                .value,
            honors: document.getElementById("collegeHonors").value,
        },
        graduate: {
            school: document.getElementById("graduateSchool").value,
            degree: document.getElementById("graduateDegree").value,
            from: document.getElementById("graduateFrom").value,
            to: document.getElementById("graduateTo").value,
            highestLevel: document.getElementById("graduateHighestLevel").value,
            yearGraduated: document.getElementById("graduateYearGraduated")
                .value,
            honors: document.getElementById("graduateHonors").value,
        },
    };

    // Step 4: Civil Service Eligibility
    formData.civilServiceEligibility = [];
    const civilServiceContainer = document.getElementById(
        "civilServiceContainer"
    );
    const civilServiceEntries = civilServiceContainer.querySelectorAll(
        ".civil-service-entry"
    );
    civilServiceEntries.forEach((entry, index) => {
        formData.civilServiceEligibility.push({
            eligibilityName: entry.querySelector(`#eligibilityName${index + 1}`)
                .value,
            rating: entry.querySelector(`#rating${index + 1}`).value,
            dateOfExam: entry.querySelector(`#dateOfExam${index + 1}`).value,
            placeOfExam: entry.querySelector(`#placeOfExam${index + 1}`).value,
            licenseNumber: entry.querySelector(`#licenseNumber${index + 1}`)
                .value,
            licenseValidity: entry.querySelector(`#licenseValidity${index + 1}`)
                .value,
        });
    });

    // Step 5: Work Experience
    formData.workExperience = [];
    const workExperienceContainer = document.getElementById(
        "workExperienceContainer"
    );
    const workExperienceEntries = workExperienceContainer.querySelectorAll(
        ".work-experience-entry"
    );

    workExperienceEntries.forEach((entry, index) => {
        formData.workExperience.push({
            inclusiveDatesFrom: entry.querySelector(
                `#inclusiveDatesFrom${index + 1}`
            ).value,
            inclusiveDatesTo: entry.querySelector(
                `#inclusiveDatesTo${index + 1}`
            ).value,
            positionTitle: entry.querySelector(`#positionTitle${index + 1}`)
                .value,
            department: entry.querySelector(`#department${index + 1}`).value,
            monthlySalary: entry.querySelector(`#monthlySalary${index + 1}`)
                .value,
            salaryGrade: entry.querySelector(`#salaryGrade${index + 1}`).value,
            stepIncrement: entry.querySelector(`#stepIncrement${index + 1}`)
                .value,
            appointmentStatus: entry.querySelector(
                `#appointmentStatus${index + 1}`
            ).value,
            govtService:
                entry.querySelector(
                    `input[name="govtService${index + 1}"]:checked`
                )?.value || "",
        });
    });

    return formData;
}

function resetForm() {
    currentStep = 1;
    showStep(currentStep);
    document.getElementById("uploadForm").reset(); // Reset the form, not the modal
}

// Add Another Child Button Functionality
document
    .getElementById("addChildButton")
    .addEventListener("click", function () {
        const childrenContainer = document.getElementById("childrenContainer");
        const childCount = childrenContainer.children.length + 1;

        const childEntry = document.createElement("div");
        childEntry.classList.add("child-entry");

        childEntry.innerHTML = `
        <div class="form-row">
            <div class="form-group">
                <label for="childName${childCount}">Child's Name:</label>
                <input type="text" id="childName${childCount}" name="childName${childCount}">
            </div>
            <div class="form-group">
                <label for="childDOB${childCount}">Date of Birth:</label>
                <input type="date" id="childDOB${childCount}" name="childDOB${childCount}">
            </div>
        </div>
    `;

        childrenContainer.appendChild(childEntry);
    });
document
    .getElementById("addCivilServiceButton")
    .addEventListener("click", function () {
        const civilServiceContainer = document.getElementById(
            "civilServiceContainer"
        );
        const entryCount = civilServiceContainer.children.length + 1;

        const civilServiceEntry = document.createElement("div");
        civilServiceEntry.classList.add("civil-service-entry");

        civilServiceEntry.innerHTML = `
            <div class="form-group">
                <label for="eligibilityName${entryCount}">Eligibility Name:</label>
                <input type="text" id="eligibilityName${entryCount}" name="eligibilityName${entryCount}">
            </div>
            <div class="form-group">
                <label for="rating${entryCount}">Rating:</label>
                <input type="text" id="rating${entryCount}" name="rating${entryCount}">
            </div>
            <div class="form-group">
                <label for="dateOfExam${entryCount}">Date of Exam:</label>
                <input type="date" id="dateOfExam${entryCount}" name="dateOfExam${entryCount}">
            </div>
            <div class="form-group">
                <label for="placeOfExam${entryCount}">Place of Exam:</label>
                <input type="text" id="placeOfExam${entryCount}" name="placeOfExam${entryCount}">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="licenseNumber${entryCount}">License Number (if applicable):</label>
                    <input type="text" id="licenseNumber${entryCount}" name="licenseNumber${entryCount}">
                </div>
                <div class="form-group">
                    <label for="licenseValidity${entryCount}">Date of Validity:</label>
                    <input type="date" id="licenseValidity${entryCount}" name="licenseValidity${entryCount}">
                </div>
            </div>
            <hr class="separator"> <!-- Horizontal line to separate entries -->
        `;

        civilServiceContainer.appendChild(civilServiceEntry);
    });

// Add Another Work Experience Entry
document
    .getElementById("addWorkExperienceButton")
    .addEventListener("click", function () {
        const workExperienceContainer = document.getElementById(
            "workExperienceContainer"
        );
        const entryCount = workExperienceContainer.children.length + 1;

        const workExperienceEntry = document.createElement("div");
        workExperienceEntry.classList.add("work-experience-entry");

        workExperienceEntry.innerHTML = `
        <!-- Inclusive Dates (From and To) -->
        <div class="form-row">
            <div class="form-group">
                <label for="inclusiveDatesFrom${entryCount}">Inclusive Dates (From):</label>
                <input type="date" id="inclusiveDatesFrom${entryCount}" name="inclusiveDatesFrom${entryCount}">
            </div>
            <div class="form-group">
                <label for="inclusiveDatesTo${entryCount}">Inclusive Dates (To):</label>
                <input type="date" id="inclusiveDatesTo${entryCount}" name="inclusiveDatesTo${entryCount}">
            </div>
        </div>

        <!-- Position Title -->
        <div class="form-group">
            <label for="positionTitle${entryCount}">Position Title:</label>
            <input type="text" id="positionTitle${entryCount}" name="positionTitle${entryCount}">
        </div>

        <!-- Department/Agency/Office/Company -->
        <div class="form-group">
            <label for="department${entryCount}">Department/Agency/Office/Company:</label>
            <input type="text" id="department${entryCount}" name="department${entryCount}">
        </div>

        <!-- Monthly Salary -->
        <div class="form-group">
            <label for="monthlySalary${entryCount}">Monthly Salary:</label>
            <input type="text" id="monthlySalary${entryCount}" name="monthlySalary${entryCount}">
        </div>

        <!-- Salary/Job/Pay Grade & Step Increment -->
        <div class="form-group">
            <label for="salaryGrade${entryCount}">Salary/Job/Pay Grade:</label>
            <input type="text" id="salaryGrade${entryCount}" name="salaryGrade${entryCount}">
        </div>
        <div class="form-group">
            <label for="stepIncrement${entryCount}">Step Increment:</label>
            <input type="text" id="stepIncrement${entryCount}" name="stepIncrement${entryCount}">
        </div>

        <!-- Status of Appointment -->
        <div class="form-group">
            <label for="appointmentStatus${entryCount}">Status of Appointment:</label>
            <input type="text" id="appointmentStatus${entryCount}" name="appointmentStatus${entryCount}">
        </div>

        <!-- Gov't Service (Yes or No) -->
        <div class="form-group">
            <label>Gov't Service:</label>
            <div class="radio-group">
                <div class="radio-option">
                    <input type="radio" id="govtServiceYes${entryCount}" name="govtService${entryCount}" value="Yes">
                    <label for="govtServiceYes${entryCount}">Yes</label>
                </div>
                <div class="radio-option">
                    <input type="radio" id="govtServiceNo${entryCount}" name="govtService${entryCount}" value="No">
                    <label for="govtServiceNo${entryCount}">No</label>
                </div>
            </div>
        </div>

        <hr class="separator"> <!-- Horizontal line to separate entries -->
    `;

        workExperienceContainer.appendChild(workExperienceEntry);
    });

document.addEventListener("DOMContentLoaded", function () {
    // First part: Excel extraction logic
    const extractButton = document.getElementById("extractDataButton");
    const excelUpload = document.getElementById("excelUpload");

    if (extractButton && excelUpload) {
        extractButton.addEventListener("click", function () {
            const file = excelUpload.files[0];

            if (!file) {
                console.log("Please select a file first.");
                return;
            }

            const reader = new FileReader();

            reader.onload = function (e) {
                const data = new Uint8Array(e.target.result);
                const workbook = XLSX.read(data, { type: "array" });

                const firstSheet = workbook.Sheets[workbook.SheetNames[0]];

                // Accessing the specific cells
                const surnameCell = firstSheet["D10"];
                const firstnameCell = firstSheet["D11"];
                const middlenameCell = firstSheet["D12"];
                const nameExtensionCell = firstSheet["L11"];
                const dobCell = firstSheet["D13"];
                const pobCell = firstSheet["D15"];

                // Access gender checkboxes
                const maleCheckboxCell = firstSheet["A62"];
                const femaleCheckboxCell = firstSheet["B62"];

                // Access civil status checkboxes
                const singleCheckboxCell = firstSheet["A63"];
                const marriedCheckboxCell = firstSheet["B63"];
                const widowedCheckboxCell = firstSheet["C63"];
                const separatedCheckboxCell = firstSheet["D63"];
                const otherCheckboxCell = firstSheet["E63"];

                // Access additional fields
                const heightCell = firstSheet["D22"];
                const weightCell = firstSheet["D24"];
                const bloodTypeCell = firstSheet["D25"];
                const gsisIdCell = firstSheet["D27"];
                const pagIbigIdCell = firstSheet["D29"];
                const philhealthNoCell = firstSheet["D31"];
                const sssNoCell = firstSheet["D32"];
                const tinNoCell = firstSheet["D33"];
                const agencyEmpNoCell = firstSheet["D34"];

                // Access citizenship checkboxes and text field
                const filipinoCheckboxCell = firstSheet["A64"];
                const dualCitizenshipCheckboxCell = firstSheet["B64"];
                const byBirthCheckboxCell = firstSheet["C64"];
                const byNaturalizationCheckboxCell = firstSheet["D64"];
                const citizenshipCountryCell = firstSheet["F64"];

                // Access residential address fields
                const residentialHouseBlockLotCell = firstSheet["I17"];
                const residentialStreetCell = firstSheet["L17"];
                const residentialSubdivisionVillageCell = firstSheet["I19"];
                const residentialBarangayCell = firstSheet["L19"];
                const residentialCityMunicipalityCell = firstSheet["I22"];
                const residentialProvinceCell = firstSheet["L22"];
                const residentialZipCodeCell = firstSheet["I24"];

                // Access permanent address fields
                const permanentHouseBlockLotCell = firstSheet["I25"];
                const permanentStreetCell = firstSheet["L25"];
                const permanentSubdivisionVillageCell = firstSheet["I27"];
                const permanentBarangayCell = firstSheet["L27"];
                const permanentCityMunicipalityCell = firstSheet["I29"];
                const permanentProvinceCell = firstSheet["L29"];
                const permanentZipCodeCell = firstSheet["I31"];

                const telephoneNumberCell = firstSheet["I32"];
                const mobileNumberCell = firstSheet["I33"];
                const emailAddressCell = firstSheet["I34"];

                // Assign values to variables for existing fields
                const surname = surnameCell ? surnameCell.v : "Not available";
                const firstname = firstnameCell
                    ? firstnameCell.v
                    : "Not available";
                const middlename = middlenameCell
                    ? middlenameCell.v
                    : "Not available";
                const nameExtension = nameExtensionCell
                    ? nameExtensionCell.v
                    : "Not available";

                function excelSerialToJSDate(serial) {
                    const excelStartDate = new Date(Date.UTC(1899, 11, 30)); // Set to UTC to prevent timezone shifts
                    const jsDate = new Date(
                        excelStartDate.getTime() + serial * 86400000
                    );
                    return jsDate.toISOString().split("T")[0]; // Format to YYYY-MM-DD
                }

                // Assign Date of Birth value with proper formatting
                const dob =
                    dobCell && !isNaN(dobCell.v)
                        ? excelSerialToJSDate(dobCell.v)
                        : "Not available";

                const pob = pobCell ? pobCell.v : "Not available";

                // Process the name extension to remove unwanted text and get the next text
                let processedNameExtension = "";
                if (nameExtensionCell && nameExtensionCell.v) {
                    processedNameExtension = nameExtensionCell.v
                        .replace(/NAME EXTENSION \((.*)\)/, "")
                        .trim();
                }

                // Determine gender based on checkbox state (TRUE if checked, FALSE if not)
                let gender = "";
                if (maleCheckboxCell && femaleCheckboxCell) {
                    if (
                        maleCheckboxCell.v === true &&
                        femaleCheckboxCell.v === true
                    ) {
                        gender = ""; // Both checked, set gender to empty string
                    } else if (maleCheckboxCell.v === true) {
                        gender = "Male";
                    } else if (femaleCheckboxCell.v === true) {
                        gender = "Female";
                    } else {
                        gender = "Not available";
                    }
                } else {
                    gender = "Not available";
                }

                // Determine civil status based on checkbox state
                let civilStatus = "";
                const checkedStatuses = [
                    singleCheckboxCell && singleCheckboxCell.v === true
                        ? "Single"
                        : null,
                    marriedCheckboxCell && marriedCheckboxCell.v === true
                        ? "Married"
                        : null,
                    widowedCheckboxCell && widowedCheckboxCell.v === true
                        ? "Widowed"
                        : null,
                    separatedCheckboxCell && separatedCheckboxCell.v === true
                        ? "Separated"
                        : null,
                    otherCheckboxCell && otherCheckboxCell.v === true
                        ? "Other/s"
                        : null,
                ].filter((status) => status !== null); // Filter out nulls

                if (checkedStatuses.length > 1) {
                    civilStatus = ""; // If more than one checkbox is checked, set civil status to empty string
                } else if (checkedStatuses.length === 1) {
                    civilStatus = checkedStatuses[0]; // Set civil status to the only checked option
                } else {
                    civilStatus = "Not available";
                }

                // Extract additional fields (height, weight, etc.)
                const height = heightCell ? heightCell.v : "Not available";
                const weight = weightCell ? weightCell.v : "Not available";
                const bloodType = bloodTypeCell
                    ? bloodTypeCell.v
                    : "Not available";
                const gsisId = gsisIdCell ? gsisIdCell.v : "Not available";
                const pagIbigId = pagIbigIdCell
                    ? pagIbigIdCell.v
                    : "Not available";
                const philhealthNo = philhealthNoCell
                    ? philhealthNoCell.v
                    : "Not available";
                const sssNo = sssNoCell ? sssNoCell.v : "Not available";
                const tinNo = tinNoCell ? tinNoCell.v : "Not available";
                const agencyEmpNo = agencyEmpNoCell
                    ? agencyEmpNoCell.v
                    : "Not available";

                // Determine citizenship values
                const isFilipino = filipinoCheckboxCell
                    ? filipinoCheckboxCell.v === true
                    : false;
                const hasDualCitizenship = dualCitizenshipCheckboxCell
                    ? dualCitizenshipCheckboxCell.v === true
                    : false;
                const acquiredByBirth = byBirthCheckboxCell
                    ? byBirthCheckboxCell.v === true
                    : false;
                const acquiredByNaturalization = byNaturalizationCheckboxCell
                    ? byNaturalizationCheckboxCell.v === true
                    : false;
                const dualCitizenshipCountry = citizenshipCountryCell
                    ? citizenshipCountryCell.v.trim()
                    : "Not available";

                // Assign values to residential address variables
                const residentialHouseBlockLot = residentialHouseBlockLotCell
                    ? residentialHouseBlockLotCell.v
                    : "Not available";
                const residentialStreet = residentialStreetCell
                    ? residentialStreetCell.v
                    : "Not available";
                const residentialSubdivisionVillage =
                    residentialSubdivisionVillageCell
                        ? residentialSubdivisionVillageCell.v
                        : "Not available";
                const excelResidentialProvince = residentialProvinceCell
                    ? residentialProvinceCell.v.toString().trim().toUpperCase()
                    : "Not available";

                const excelResidentialCity = residentialCityMunicipalityCell
                    ? residentialCityMunicipalityCell.v
                          .toString()
                          .trim()
                          .toUpperCase()
                    : "Not available";

                const excelResidentialBarangay = residentialBarangayCell
                    ? residentialBarangayCell.v.toString().trim()
                    : "Not available";

                const residentialZipCode = residentialZipCodeCell
                    ? residentialZipCodeCell.v
                    : "Not available";

                // Assign values to permanent address variables
                const permanentHouseBlockLot = permanentHouseBlockLotCell
                    ? permanentHouseBlockLotCell.v
                    : "Not available";
                const permanentStreet = permanentStreetCell
                    ? permanentStreetCell.v
                    : "Not available";
                const permanentSubdivisionVillage =
                    permanentSubdivisionVillageCell
                        ? permanentSubdivisionVillageCell.v
                        : "Not available";
                const excelPermanentProvince = permanentProvinceCell
                    ? permanentProvinceCell.v.toString().trim().toUpperCase()
                    : "Not available";

                const excelPermanentCity = permanentCityMunicipalityCell
                    ? permanentCityMunicipalityCell.v
                          .toString()
                          .trim()
                          .toUpperCase()
                    : "Not available";

                const excelPermanentBarangay = permanentBarangayCell
                    ? permanentBarangayCell.v.toString().trim()
                    : "Not available";
                const permanentZipCode = permanentZipCodeCell
                    ? permanentZipCodeCell.v
                    : "Not available";

                const telephoneNumber = telephoneNumberCell
                    ? telephoneNumberCell.v
                    : "Not available";
                const mobileNumber = mobileNumberCell
                    ? mobileNumberCell.v
                    : "Not available";
                const emailAddress = emailAddressCell
                    ? emailAddressCell.v
                    : "Not available";

                // Populate the form fields
                document.getElementById("surname").value = surname;
                document.getElementById("firstName").value = firstname;
                document.getElementById("middleName").value = middlename;
                document.getElementById("nameExtension").value =
                    processedNameExtension;
                document.getElementById("dateOfBirth").value = dob;
                document.getElementById("placeOfBirth").value = pob;
                document.getElementById("height").value = height;
                document.getElementById("weight").value = weight;
                document.getElementById("bloodType").value = bloodType;
                document.getElementById("telephoneNo").value = telephoneNumber;
                document.getElementById("mobileNo").value = mobileNumber;
                document.getElementById("email").value = emailAddress;
                document.getElementById("gsisId").value = gsisId;
                document.getElementById("pagibigId").value = pagIbigId;
                document.getElementById("philhealthId").value = philhealthNo;
                document.getElementById("sssNo").value = sssNo;
                document.getElementById("tinNo").value = tinNo;
                document.getElementById("agencyEmployeeNo").value = agencyEmpNo;
                document.getElementById("residentialHouseNo").value =
                    residentialHouseBlockLot;
                document.getElementById("residentialStreet").value =
                    residentialStreet;
                document.getElementById("residentialSubdivision").value =
                    residentialSubdivisionVillage;
                // Only try to set the dropdown if the Excel value is available
                if (excelResidentialProvince !== "Not available") {
                    setTimeout(() => {
                        const resProvinceSelect = document.getElementById(
                            "residentialProvince"
                        );
                        for (let option of resProvinceSelect.options) {
                            if (
                                option.text.trim().toUpperCase() ===
                                excelResidentialProvince
                            ) {
                                resProvinceSelect.value = option.value;
                                resProvinceSelect.dispatchEvent(
                                    new Event("change")
                                );
                                break;
                            }
                        }
                        // Wait for cities to load after province change
                        if (excelResidentialCity !== "Not available") {
                            setTimeout(() => {
                                const resCitySelect =
                                    document.getElementById("residentialCity");
                                for (let option of resCitySelect.options) {
                                    if (
                                        option.text.trim().toUpperCase() ===
                                        excelResidentialCity
                                    ) {
                                        resCitySelect.value = option.value;
                                        resCitySelect.dispatchEvent(
                                            new Event("change")
                                        );
                                        break;
                                    }
                                }
                                // Wait for barangays to load after city change
                                if (
                                    excelResidentialBarangay !== "Not available"
                                ) {
                                    setTimeout(() => {
                                        const resBarangaySelect =
                                            document.getElementById(
                                                "residentialBarangay"
                                            );
                                        for (let option of resBarangaySelect.options) {
                                            if (
                                                option.text.trim() ===
                                                excelResidentialBarangay
                                            ) {
                                                resBarangaySelect.value =
                                                    option.value;
                                                break;
                                            }
                                        }
                                    }, 500);
                                }
                            }, 500);
                        }
                    }, 500);
                }
                document.getElementById("residentialZipCode").value =
                    residentialZipCode;
                document.getElementById("permanentHouseNo").value =
                    permanentHouseBlockLot;
                document.getElementById("permanentStreet").value =
                    permanentStreet;
                document.getElementById("permanentSubdivision").value =
                    permanentSubdivisionVillage;

                if (excelPermanentProvince !== "Not available") {
                    setTimeout(() => {
                        const permProvinceSelect =
                            document.getElementById("permanentProvince");
                        for (let option of permProvinceSelect.options) {
                            if (
                                option.text.trim().toUpperCase() ===
                                excelPermanentProvince
                            ) {
                                permProvinceSelect.value = option.value;
                                permProvinceSelect.dispatchEvent(
                                    new Event("change")
                                );
                                break;
                            }
                        }
                        // Wait for cities to load after province change
                        if (excelPermanentCity !== "Not available") {
                            setTimeout(() => {
                                const permCitySelect =
                                    document.getElementById("permanentCity");
                                for (let option of permCitySelect.options) {
                                    if (
                                        option.text.trim().toUpperCase() ===
                                        excelPermanentCity
                                    ) {
                                        permCitySelect.value = option.value;
                                        permCitySelect.dispatchEvent(
                                            new Event("change")
                                        );
                                        break;
                                    }
                                }
                                // Wait for barangays to load after city change
                                if (
                                    excelPermanentBarangay !== "Not available"
                                ) {
                                    setTimeout(() => {
                                        const permBarangaySelect =
                                            document.getElementById(
                                                "permanentBarangay"
                                            );
                                        for (let option of permBarangaySelect.options) {
                                            if (
                                                option.text.trim() ===
                                                excelPermanentBarangay
                                            ) {
                                                permBarangaySelect.value =
                                                    option.value;
                                                break;
                                            }
                                        }
                                    }, 500);
                                }
                            }, 500);
                        }
                    }, 500);
                }

                document.getElementById("permanentZipCode").value =
                    permanentZipCode;

                // Select the radio buttons
                const maleRadio = document.getElementById("sexMale");
                const femaleRadio = document.getElementById("sexFemale");

                if (gender === "Male") {
                    maleRadio.checked = true;
                } else if (gender === "Female") {
                    femaleRadio.checked = true;
                }

                // Select all civil status radio buttons
                const singleRadio =
                    document.getElementById("civilStatusSingle");
                const marriedRadio =
                    document.getElementById("civilStatusMarried");
                const widowedRadio =
                    document.getElementById("civilStatusWidowed");
                const separatedRadio = document.getElementById(
                    "civilStatusSeparated"
                );
                const otherRadio = document.getElementById("civilStatusOther");

                // Set the checked property based on civilStatus value
                if (civilStatus === "Single") {
                    singleRadio.checked = true;
                } else if (civilStatus === "Married") {
                    marriedRadio.checked = true;
                } else if (civilStatus === "Widowed") {
                    widowedRadio.checked = true;
                } else if (civilStatus === "Separated") {
                    separatedRadio.checked = true;
                } else if (civilStatus === "Other/s") {
                    otherRadio.checked = true;
                }

                // Get elements
                const filipinoCheckbox = document.getElementById("filipino");
                const dualCitizenshipCheckbox =
                    document.getElementById("dualCitizenship");
                const byBirthRadio = document.getElementById("byBirth");
                const byNaturalizationRadio =
                    document.getElementById("byNaturalization");
                const subOptionsDiv = document.querySelector(".sub-options");
                const countrySelect = document.getElementById("countrySelect");

                // Set checkbox states
                filipinoCheckbox.checked = isFilipino;
                dualCitizenshipCheckbox.checked = hasDualCitizenship;

                // Show/hide sub-options for dual citizenship
                subOptionsDiv.style.display = hasDualCitizenship
                    ? "block"
                    : "none";

                // Set radio button states for how citizenship was acquired
                if (acquiredByBirth) {
                    byBirthRadio.checked = true;
                } else if (acquiredByNaturalization) {
                    byNaturalizationRadio.checked = true;
                }

                // Set country selection if applicable
                if (dualCitizenshipCountry !== "Not available") {
                    countrySelect.value = dualCitizenshipCountry;
                }
            };

            reader.readAsArrayBuffer(file);
        });
    }

    // Second part: Province, City/Municipality, Barangay logic
    const residentialProvince = document.getElementById("residentialProvince");
    const residentialCity = document.getElementById("residentialCity");
    const residentialBarangay = document.getElementById("residentialBarangay");

    const permanentProvince = document.getElementById("permanentProvince");
    const permanentCity = document.getElementById("permanentCity");
    const permanentBarangay = document.getElementById("permanentBarangay");

    if (
        residentialProvince &&
        residentialCity &&
        residentialBarangay &&
        permanentProvince &&
        permanentCity &&
        permanentBarangay
    ) {
        // Function to fetch provinces
        function fetchProvinces(provinceSelect) {
            fetch("/provinces")
                .then((response) => response.json())
                .then((data) => {
                    provinceSelect.innerHTML =
                        '<option value="">Select Province</option>';
                    data.forEach((province) => {
                        const option = document.createElement("option");
                        option.value = province.provinceCode;
                        option.textContent = province.provinceName;
                        provinceSelect.appendChild(option);
                    });
                });
        }

        // Function to fetch towns
        function fetchTowns(provinceCode, citySelect) {
            fetch(`/towns/${provinceCode}`)
                .then((response) => response.json())
                .then((data) => {
                    citySelect.innerHTML =
                        '<option value="">Select City/Municipality</option>';
                    data.forEach((town) => {
                        const option = document.createElement("option");
                        option.value = town.townCode;
                        option.textContent = town.townName;
                        citySelect.appendChild(option);
                    });
                });
        }

        // Function to fetch barangays
        function fetchBarangays(townCode, barangaySelect) {
            fetch(`/barangays/${townCode}`)
                .then((response) => response.json())
                .then((data) => {
                    barangaySelect.innerHTML =
                        '<option value="">Select Barangay</option>';
                    data.forEach((barangay) => {
                        const option = document.createElement("option");
                        option.value = barangay.barangayCode;
                        option.textContent = barangay.barangayName;
                        barangaySelect.appendChild(option);
                    });
                });
        }

        // Fetch provinces for residential address
        fetchProvinces(residentialProvince);
        fetchProvinces(permanentProvince);

        // Event listener for residential province change
        residentialProvince.addEventListener("change", function () {
            const provinceCode = this.value;
            fetchTowns(provinceCode, residentialCity);
            residentialBarangay.innerHTML =
                '<option value="">Select Barangay</option>';
        });

        // Event listener for residential city change
        residentialCity.addEventListener("change", function () {
            const townCode = this.value;
            fetchBarangays(townCode, residentialBarangay);
        });

        // Event listener for permanent province change
        permanentProvince.addEventListener("change", function () {
            const provinceCode = this.value;
            fetchTowns(provinceCode, permanentCity);
            permanentBarangay.innerHTML =
                '<option value="">Select Barangay</option>';
        });

        // Event listener for permanent city change
        permanentCity.addEventListener("change", function () {
            const townCode = this.value;
            fetchBarangays(townCode, permanentBarangay);
        });
    }
});
function loadCountries() {
    fetch("data/countries.json") // Path to your local JSON file
        .then((response) => response.json())
        .then((data) => {
            const countrySelect = document.getElementById("countrySelect");
            countrySelect.innerHTML =
                '<option value="">Select Country</option>';

            // Sort countries alphabetically by name
            data.sort((a, b) =>
                a.name.common.localeCompare(b.name.common)
            ).forEach((country) => {
                const option = document.createElement("option");
                option.value = country.name.common;
                option.textContent = country.name.common;
                countrySelect.appendChild(option);
            });
        })
        .catch((error) => {
            console.error("Error loading countries:", error);
            document.getElementById("countrySelect").innerHTML =
                '<option value="">Failed to load countries</option>';
        });
}

// Function to toggle dual citizenship sub-options
function toggleDualCitizenshipOptions() {
    const dualCitizenshipCheckbox = document.getElementById("dualCitizenship");
    if (dualCitizenshipCheckbox) {
        dualCitizenshipCheckbox.addEventListener("change", function () {
            const subOptions = this.closest(
                ".citizenship-option"
            ).querySelector(".sub-options");
            subOptions.style.display = this.checked ? "block" : "none";

            if (!this.checked) {
                subOptions
                    .querySelectorAll('input[type="radio"]')
                    .forEach((radio) => (radio.checked = false));
                document.getElementById("countrySelect").value = "";
            }
        });
    }
}

// Initialize the form functionality
function initializeForm() {
    loadCountries();
    toggleDualCitizenshipOptions();
}

// Run the initialization when the DOM is fully loaded
document.addEventListener("DOMContentLoaded", initializeForm);

document.addEventListener("DOMContentLoaded", function () {
    document
        .getElementById("copyAddress")
        .addEventListener("click", function () {
            // Copy text/normal input fields immediately
            document.getElementById("permanentHouseNo").value =
                document.getElementById("residentialHouseNo").value;
            document.getElementById("permanentStreet").value =
                document.getElementById("residentialStreet").value;
            document.getElementById("permanentSubdivision").value =
                document.getElementById("residentialSubdivision").value;
            document.getElementById("permanentZipCode").value =
                document.getElementById("residentialZipCode").value;

            // Copy the Province dropdown value
            const resProvinceSelect = document.getElementById(
                "residentialProvince"
            );
            const permProvinceSelect =
                document.getElementById("permanentProvince");
            const selectedProvinceValue = resProvinceSelect.value;
            if (selectedProvinceValue && selectedProvinceValue !== "") {
                permProvinceSelect.value = selectedProvinceValue;
                // Dispatch change event to trigger the loading of the dependent cities
                permProvinceSelect.dispatchEvent(new Event("change"));
            }

            // Allow some time for the City dropdown to be populated after province change
            setTimeout(() => {
                const resCitySelect =
                    document.getElementById("residentialCity");
                const permCitySelect = document.getElementById("permanentCity");
                const selectedCityValue = resCitySelect.value;
                if (selectedCityValue && selectedCityValue !== "") {
                    permCitySelect.value = selectedCityValue;
                    // Dispatch change event to trigger the loading of the dependent barangays
                    permCitySelect.dispatchEvent(new Event("change"));
                }

                // Allow some time for the Barangay dropdown to be populated after city change
                setTimeout(() => {
                    const resBarangaySelect = document.getElementById(
                        "residentialBarangay"
                    );
                    const permBarangaySelect =
                        document.getElementById("permanentBarangay");
                    const selectedBarangayValue = resBarangaySelect.value;
                    if (selectedBarangayValue && selectedBarangayValue !== "") {
                        permBarangaySelect.value = selectedBarangayValue;
                    }
                }, 500);
            }, 500);
        });
});
