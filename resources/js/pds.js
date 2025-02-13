import * as XLSX from "xlsx";

document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.querySelector(".search-bar");
    const tableBody = document.querySelector(".salary-adjustment-table tbody");
    const tableRows = Array.from(tableBody.querySelectorAll("tr"));

    searchInput.addEventListener("keyup", function () {
        const query = searchInput.value.toLowerCase().trim();

        if (!query) {
            resetTable();
            return;
        }

        const rankedRows = tableRows.map((row) => {
            const nameCell = row.querySelector("td:first-child"); // Employee Name column
            if (!nameCell) return { row, score: Infinity }; // Ignore if no name column

            const nameText = nameCell.textContent.toLowerCase();
            const nameParts = nameText.split(/\s+/);
            const queryParts = query.split(/\s+/);

            let bestScore = Infinity;

            // Compare each part of query against each name part
            queryParts.forEach((queryPart) => {
                nameParts.forEach((namePart) => {
                    const distance = getLevenshteinDistance(
                        namePart,
                        queryPart
                    );
                    bestScore = Math.min(bestScore, distance);
                });
            });

            return { row, score: bestScore };
        });

        // Sort rows based on score (lower = better match)
        rankedRows.sort((a, b) => a.score - b.score);

        // Reorder table rows based on sorted ranking
        tableBody.innerHTML = ""; // Clear table
        rankedRows.forEach(({ row, score }) => {
            row.style.display = query.length === 1 || score <= 3 ? "" : "none";
            tableBody.appendChild(row);
        });
    });

    function resetTable() {
        tableRows.forEach((row) => (row.style.display = ""));
        tableBody.innerHTML = "";
        tableRows.forEach((row) => tableBody.appendChild(row));
    }

    function getLevenshteinDistance(a, b) {
        const matrix = Array.from({ length: a.length + 1 }, (_, i) => [i]).map(
            (row, i) =>
                row.concat(
                    Array.from({ length: b.length }, (_, j) =>
                        i === 0 ? j + 1 : 0
                    )
                )
        );

        for (let i = 1; i <= a.length; i++) {
            for (let j = 1; j <= b.length; j++) {
                matrix[i][j] =
                    a[i - 1] === b[j - 1]
                        ? matrix[i - 1][j - 1]
                        : Math.min(
                              matrix[i - 1][j],
                              matrix[i][j - 1],
                              matrix[i - 1][j - 1]
                          ) + 1;
            }
        }

        return matrix[a.length][b.length];
    }
});

// Get the modal, button, and close elements
const modal = document.getElementById("uploadModal");
const uploadButton = document.getElementById("uploadButton");
const closeButton = document.querySelector(".close");
let isEditClicked = false; // Flag to track if the edit button was clicked

// Add event listener to all Edit buttons
document.querySelectorAll(".edit-btn").forEach((editButton) => {
    editButton.addEventListener("click", function () {
        // Set the flag to true when the Edit button is clicked
        isEditClicked = true;
        console.log("Edit button clicked, isEditClicked set to true");
    });
});

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
// Function to check if required fields in a step are filled
function checkRequiredFields(step) {
    let allFieldsFilled = true;

    // Get all required inputs in the current step
    const requiredFields = document.querySelectorAll(`#step${step} .required`);

    requiredFields.forEach((field) => {
        if (!field.value.trim()) {
            allFieldsFilled = false;
            field.focus(); // Focus on the first empty required field
            return; // Exit the loop once an empty field is found
        }
    });

    return allFieldsFilled;
}

document
    .getElementById("downloadButton")
    .addEventListener("click", function () {
        // The file path (adjust as needed)
        const filePath = "/docs/ClearPDS.xlsx";

        // Trigger the download using the location.href method
        const link = document.createElement("a");
        link.href = filePath;
        link.download = "ClearPDS.xlsx"; // Optional: specify the filename
        link.click(); // Programmatically click the link to start the download
    });

// Next button functionality with check
document.getElementById("nextButton1").addEventListener("click", () => {
    if (checkRequiredFields(1)) {
        currentStep = 2;
        showStep(currentStep);
    }
});

document.getElementById("nextButton2").addEventListener("click", () => {
    if (checkRequiredFields(2)) {
        currentStep = 3;
        showStep(currentStep);
    }
});

document.getElementById("nextButton3").addEventListener("click", () => {
    if (checkRequiredFields(3)) {
        currentStep = 4;
        showStep(currentStep);
    }
});

document.getElementById("nextButton4").addEventListener("click", () => {
    if (checkRequiredFields(4)) {
        currentStep = 5;
        showStep(currentStep);

        // Check if the Edit button was clicked
        if (isEditClicked) {
            // Show the Save button, hide the Submit button
            document.getElementById("savePDSButton").style.display =
                "inline-block";
            document.getElementById("submitButton").style.display = "none";
        } else {
            // Show the Submit button, hide the Save button
            document.getElementById("submitButton").style.display =
                "inline-block";
            document.getElementById("saveButton").style.display = "none";
        }
    }
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

document.getElementById("submitButton").addEventListener("click", async () => {
    // Get the current employeeName from the hidden input field
    const currentEmployeeName = document.getElementById(
        "currentEmployeeName"
    ).value;

    // Collect form data
    const formData = collectFormData();

    // Add currentEmployeeName to the formData object
    formData.currentEmployeeName = currentEmployeeName;

    try {
        // First, call the validation endpoint
        const validationResponse = await fetch("/validate-form", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
            },
            body: JSON.stringify(formData), // Send formData with currentEmployeeName
        });

        const validationData = await validationResponse.json();

        if (validationResponse.ok) {
            // If validation is successful, proceed to submit the form
            const storeResponse = await fetch("/submit-form", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content"),
                },
                body: JSON.stringify(formData), // Send formData with currentEmployeeName
            });

            const storeData = await storeResponse.json();

            if (storeResponse.ok) {
                alert("Data submitted successfully!");

                // Redirect to the /personal-data-sheet route
                window.location.href = "/personal-data-sheet";
            } else {
                console.error("Error:", storeData);
                alert("Failed to submit data.");
            }
        } else {
            // If validation fails, format and display the errors in an alert
            console.error("Validation Error:", validationData.errors);
            const errorMessage = formatValidationErrors(validationData.errors);
            alert(errorMessage);
        }
    } catch (error) {
        console.error("Error:", error);
        alert("An error occurred while submitting the form.");
    }
});

document.getElementById("savePDSButton").addEventListener("click", async () => {
    // Get the current employeeName from the hidden input field
    const currentEmployeeName = document.getElementById(
        "currentEmployeeName"
    ).value;

    const formData = collectFormData();

    // Add currentEmployeeName to the formData object
    formData.currentEmployeeName = currentEmployeeName;

    console.log("Form Data collected:", formData);
    // Ensure isEditClicked flag is reset if needed
    if (isEditClicked) {
        console.log("Edit button was clicked, sending data to update.");
    } else {
        console.log("Edit button was not clicked, sending data to create new.");
    }

    try {
        // First, call the validation endpoint
        const validationResponse = await fetch("/validate-form", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
            },
            body: JSON.stringify(formData), // Send formData for validation
        });

        const validationData = await validationResponse.json();

        if (validationResponse.ok) {
            // If validation is successful, proceed to send the data to store or update
            const storeResponse = await fetch("/update-personal-info", {
                method: "PUT", // Use PUT for update operation
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content"),
                },
                body: JSON.stringify(formData), // Send formData to store data
            });

            const storeData = await storeResponse.json();

            if (storeResponse.ok) {
                alert("Data updated successfully!");

                // Optionally, close the modal here
                const modal = document.getElementById("uploadModal");
                modal.style.display = "none";

                // Reset the flag when the modal closes (if not done earlier)
                isEditClicked = false;

                // Redirect to the /personal-data-sheet route
                window.location.href = "/personal-data-sheet";
                // Optionally, reload or update the data on the page
            } else {
                console.error("Error:", storeData);
                alert("Failed to update data.");
            }
        } else {
            // If validation fails, format and display the errors in an alert
            console.error("Validation Error:", validationData.errors);
            const errorMessage = formatValidationErrors(validationData.errors);
            alert(errorMessage);
        }
    } catch (error) {
        console.error("Error:", error);
        alert("An error occurred while submitting the form.");
    }
});

function formatValidationErrors(errors) {
    let errorMessage = "Please fill the missing fields:\n";

    // Convert errors object to an array of [field, messages] pairs
    const errorEntries = Object.entries(errors);

    // Limit the number of fields to display
    const maxFieldsToShow = 6;
    const fieldsToShow = errorEntries.slice(0, maxFieldsToShow);
    const remainingFields = errorEntries.length - maxFieldsToShow;

    for (const [field, messages] of fieldsToShow) {
        // Convert snake case and nested fields to a more readable format
        const readableField = field
            .replace(/_/g, " ") // Replace underscores with spaces
            .replace(/\./g, " ") // Replace dots with spaces
            .replace(/\b\w/g, (char) => char.toUpperCase()); // Capitalize each word

        // Add the field to the alert message
        errorMessage += `\n- ${readableField}`;
    }

    // Add an ellipsis if there are more fields than the limit
    if (remainingFields > 0) {
        errorMessage += `\n...`;
    }

    return errorMessage;
}

function collectFormData() {
    const formData = {};

    formData.personal_info_id = document.getElementById("personalInfoId").value;

    formData.first_name = document.getElementById("firstName").value;
    formData.last_name = document.getElementById("surname").value;
    formData.middle_name = document.getElementById("middleName").value;
    formData.name_extension = document.getElementById("nameExtension").value;
    formData.date_of_birth = document.getElementById("dateOfBirth").value;
    formData.place_of_birth = document.getElementById("placeOfBirth").value;
    formData.sex =
        document.querySelector('input[name="sex"]:checked')?.value || "";
    formData.civil_status =
        document.querySelector('input[name="civilStatus"]:checked')?.value ||
        "";
    formData.height = document.getElementById("height").value;
    formData.weight = document.getElementById("weight").value;
    formData.blood_type = document.getElementById("bloodType").value;
    formData.gsis_id = document.getElementById("gsisId").value;
    formData.pagibig_id = document.getElementById("pagibigId").value;
    formData.philhealth_id = document.getElementById("philhealthId").value;
    formData.sss_no = document.getElementById("sssNo").value;
    formData.tin_no = document.getElementById("tinNo").value;
    formData.agency_employee_no =
        document.getElementById("agencyEmployeeNo").value;
    formData.telephone_no = document.getElementById("telephoneNo").value;
    formData.mobile_no = document.getElementById("mobileNo").value;
    formData.email = document.getElementById("email").value;

    // Citizenship Information
    formData.is_filipino = document.getElementById("filipino").checked; // true if checked
    formData.is_dual_citizen =
        document.getElementById("dualCitizenship").checked; // true if checked

    // Dual Citizenship Type (Only if Dual Citizenship is checked)
    if (formData.is_dual_citizen) {
        formData.dual_citizen_type =
            document.querySelector('input[name="dualCitizenType"]:checked')
                ?.value || "";
        formData.dual_citizen_country =
            document.getElementById("countrySelect").value;
    } else {
        formData.dual_citizen_type = null;
        formData.dual_citizen_country = null;
    }

    // Residential Address
    formData.residential_address = {
        type: "residential",
        house_no: document.getElementById("residentialHouseNo").value,
        street: document.getElementById("residentialStreet").value,
        subdivision: document.getElementById("residentialSubdivision").value,
        barangay: document.getElementById("residentialBarangay").value,
        city: document.getElementById("residentialCity").value,
        province: document.getElementById("residentialProvince").value,
        zip_code: document.getElementById("residentialZipCode").value,
    };

    // Permanent Address
    formData.permanent_address = {
        type: "permanent",
        house_no: document.getElementById("permanentHouseNo").value,
        street: document.getElementById("permanentStreet").value,
        subdivision: document.getElementById("permanentSubdivision").value,
        barangay: document.getElementById("permanentBarangay").value,
        city: document.getElementById("permanentCity").value,
        province: document.getElementById("permanentProvince").value,
        zip_code: document.getElementById("permanentZipCode").value,
    };

    // Family Background
    formData.family_background = {
        spouse_surname: document.getElementById("spouseSurname").value,
        spouse_first_name: document.getElementById("spouseFirstName").value,
        spouse_middle_name: document.getElementById("spouseMiddleName").value,
        spouse_name_extension: document.getElementById("spouseNameExtension")
            .value,
        spouse_occupation: document.getElementById("spouseOccupation").value,
        spouse_employer: document.getElementById("spouseEmployer").value,
        spouse_telephone: document.getElementById("spouseTelephone").value,
        father_surname: document.getElementById("fatherSurname").value,
        father_first_name: document.getElementById("fatherFirstName").value,
        father_middle_name: document.getElementById("fatherMiddleName").value,
        father_name_extension: document.getElementById("fatherNameExtension")
            .value,
        mother_surname: document.getElementById("motherSurname").value,
        mother_first_name: document.getElementById("motherFirstName").value,
        mother_middle_name: document.getElementById("motherMiddleName").value,
    };

    // Educational Background
    formData.elementary = {
        level: "Elementary",
        school: document.getElementById("elementarySchool").value,
        degree: document.getElementById("elementaryDegree").value,
        from: document.getElementById("elementaryFrom").value,
        to: document.getElementById("elementaryTo").value,
        highest_level: document.getElementById("elementaryHighestLevel").value,
        year_graduated: document.getElementById("elementaryYearGraduated")
            .value,
        honors: document.getElementById("elementaryHonors").value,
    };

    formData.secondary = {
        level: "Secondary",
        school: document.getElementById("secondarySchool").value,
        degree: document.getElementById("secondaryDegree").value,
        from: document.getElementById("secondaryFrom").value,
        to: document.getElementById("secondaryTo").value,
        highest_level: document.getElementById("secondaryHighestLevel").value,
        year_graduated: document.getElementById("secondaryYearGraduated").value,
        honors: document.getElementById("secondaryHonors").value,
    };

    formData.vocational = {
        level: "Vocational",
        school: document.getElementById("vocationalSchool").value,
        degree: document.getElementById("vocationalDegree").value,
        from: document.getElementById("vocationalFrom").value,
        to: document.getElementById("vocationalTo").value,
        highest_level: document.getElementById("vocationalHighestLevel").value,
        year_graduated: document.getElementById("vocationalYearGraduated")
            .value,
        honors: document.getElementById("vocationalHonors").value,
    };

    formData.college = {
        level: "College",
        school: document.getElementById("collegeSchool").value,
        degree: document.getElementById("collegeDegree").value,
        from: document.getElementById("collegeFrom").value,
        to: document.getElementById("collegeTo").value,
        highest_level: document.getElementById("collegeHighestLevel").value,
        year_graduated: document.getElementById("collegeYearGraduated").value,
        honors: document.getElementById("collegeHonors").value,
    };

    formData.graduateStudies = {
        level: "Graduate Studies",
        school: document.getElementById("graduateSchool").value,
        degree: document.getElementById("graduateDegree").value,
        from: document.getElementById("graduateFrom").value,
        to: document.getElementById("graduateTo").value,
        highest_level: document.getElementById("graduateHighestLevel").value,
        year_graduated: document.getElementById("graduateYearGraduated").value,
        honors: document.getElementById("graduateHonors").value,
    };

    // Civil Service Eligibility
    const civilServiceContainer = document.querySelector(
        "#civilServiceContainer"
    );
    const civilServiceRows = civilServiceContainer.querySelectorAll("tr");

    if (civilServiceRows.length > 0) {
        formData.civil_service_eligibility = [];
        civilServiceRows.forEach((row, index) => {
            // Get the input elements within the row
            const inputs = row.querySelectorAll("input");

            // Push the data into the formData object
            formData.civil_service_eligibility.push({
                eligibility_name: inputs[0].value, // Eligibility Name
                rating: inputs[1].value, // Rating
                date_of_exam: inputs[2].value, // Date of Exam/Conferment
                place_of_exam: inputs[3].value, // Place of Exam/Conferment
                license_number: inputs[4].value, // License Number
                license_validity: inputs[5].value, // Date of Validity
            });
        });
    } else {
        formData.civil_service_eligibility = []; // Ensure the array is initialized
    }

    // Work Experience
    const workExperienceTable = document.querySelector("#step5 tbody");
    const workExperienceRows = workExperienceTable.querySelectorAll("tr");

    if (workExperienceRows.length > 0) {
        formData.work_experience = [];
        workExperienceRows.forEach((row, index) => {
            formData.work_experience.push({
                from:
                    row.querySelector(`#inclusiveDatesFrom${index + 1}`)
                        ?.value || "",
                to:
                    row.querySelector(`#inclusiveDatesTo${index + 1}`)?.value ||
                    "",
                position_title:
                    row.querySelector(`#positionTitle${index + 1}`)?.value ||
                    "",
                department:
                    row.querySelector(`#department${index + 1}`)?.value || "",
                monthly_salary:
                    row.querySelector(`#monthlySalary${index + 1}`)?.value ||
                    "",
                salary_grade:
                    row.querySelector(`#salaryGrade${index + 1}`)?.value || "",
                step_increment:
                    row.querySelector(`#stepIncrement${index + 1}`)?.value ||
                    "",
                appointment_status:
                    row.querySelector(`#appointmentStatus${index + 1}`)
                        ?.value || "",
                govt_service:
                    row.querySelector(
                        `input[name="govtService${index + 1}"]:checked`
                    )?.value || "",
            });
        });
    }

    return formData;
}

function resetForm() {
    currentStep = 1;
    showStep(currentStep);
    document.getElementById("uploadForm").reset(); // Reset the form, not the modal
}

document
    .getElementById("addCivilServiceButton")
    .addEventListener("click", function () {
        const tbody = document.querySelector("#step4 table tbody");

        if (!tbody) {
            console.error("Error: Table tbody not found.");
            return;
        }

        const entryNumber = tbody.children.length + 1; // Get next entry number

        const row = document.createElement("tr");
        row.classList.add("civil-service-entry");

        row.innerHTML = `
            <td><input type="text" id="eligibilityName${entryNumber}" name="eligibilityName${entryNumber}"></td>
            <td><input type="text" id="rating${entryNumber}" name="rating${entryNumber}"></td>
            <td><input type="date" id="dateOfExam${entryNumber}" name="dateOfExam${entryNumber}"></td>
            <td><input type="text" id="placeOfExam${entryNumber}" name="placeOfExam${entryNumber}"></td>
            <td><input type="text" id="licenseNumber${entryNumber}" name="licenseNumber${entryNumber}"></td>
            <td><input type="date" id="licenseValidity${entryNumber}" name="licenseValidity${entryNumber}"></td>
            <td><button type="button" class="remove-btn">Remove</button></td>
        `;

        tbody.appendChild(row);

        // Add functionality to the remove button
        row.querySelector(".remove-btn").addEventListener("click", function () {
            row.remove();
        });
    });

// Add Another Work Experience Entry
document
    .getElementById("addWorkExperienceButton")
    .addEventListener("click", function () {
        const tableBody = document.querySelector(
            ".work-experience-table tbody"
        );
        const entryCount = tableBody.children.length + 1;

        // Create a new table row for the work experience entry
        const workExperienceRow = document.createElement("tr");

        // Add the HTML for each field in the row
        workExperienceRow.innerHTML = `
            <td><input type="date" id="inclusiveDatesFrom${entryCount}" name="inclusiveDatesFrom${entryCount}"></td>
            <td><input type="date" id="inclusiveDatesTo${entryCount}" name="inclusiveDatesTo${entryCount}"></td>
            <td><input type="text" id="positionTitle${entryCount}" name="positionTitle${entryCount}"></td>
            <td><input type="text" id="department${entryCount}" name="department${entryCount}"></td>
            <td><input type="text" id="monthlySalary${entryCount}" name="monthlySalary${entryCount}"></td>
            <td><input type="text" id="salaryGrade${entryCount}" name="salaryGrade${entryCount}"></td>
            <td><input type="text" id="stepIncrement${entryCount}" name="stepIncrement${entryCount}"></td>
            <td><input type="text" id="appointmentStatus${entryCount}" name="appointmentStatus${entryCount}"></td>
            <td>
                <input type="radio" id="govtServiceYes${entryCount}" name="govtService${entryCount}" value="Yes">
                <label for="govtServiceYes${entryCount}">Yes</label>
                <input type="radio" id="govtServiceNo${entryCount}" name="govtService${entryCount}" value="No">
                <label for="govtServiceNo${entryCount}">No</label>
            </td>
            <td><button type="button" class="remove-btn">Remove</button></td>
        `;

        // Append the new row to the table body
        tableBody.appendChild(workExperienceRow);

        // Add functionality to the remove button for each entry
        workExperienceRow
            .querySelector(".remove-btn")
            .addEventListener("click", function () {
                workExperienceRow.remove();
            });
    });

document.addEventListener("DOMContentLoaded", function () {
    // First part: Excel extraction logic
    const extractButton = document.getElementById("extractDataButton");
    const excelUpload = document.getElementById("excelUpload");

    if (extractButton && excelUpload) {
        extractButton.addEventListener("click", function () {
            const file = excelUpload.files[0];

            if (!file) {
                alert("Please select a file first.");
                return;
            }

            function safeDateFormat(value) {
                if (!value) return ""; // Return empty if no value

                let dateObj;

                if (typeof value === "number") {
                    // Convert Excel serial date to JavaScript Date
                    const excelStartDate = new Date(1899, 11, 30);
                    dateObj = new Date(
                        excelStartDate.getTime() + value * 86400000
                    );
                } else if (typeof value === "string") {
                    // Parse string date
                    dateObj = new Date(value);
                }

                if (dateObj && !isNaN(dateObj.getTime())) {
                    // Format as YYYY-MM-DD for <input type="date">
                    return dateObj.toISOString().split("T")[0];
                }

                return ""; // Return empty if invalid
            }

            // Function to get cell value safely
            function getCellValue(sheet, cell) {
                return sheet[cell] ? sheet[cell].v : "";
            }

            const reader = new FileReader();

            reader.onload = function (e) {
                const data = new Uint8Array(e.target.result);
                const workbook = XLSX.read(data, { type: "array" });
                // Access the first sheet (existing logic)
                const firstSheet = workbook.Sheets[workbook.SheetNames[0]];

                // Access the second sheet (for Civil Service Eligibility)
                const secondSheet = workbook.Sheets[workbook.SheetNames[1]];

                // Extract data from the second sheet
                const eligibilityData =
                    extractCivilServiceEligibility(secondSheet);

                // Populate the form with eligibility data
                populateCivilServiceEligibility(eligibilityData);

                // Extract data from the second sheet
                const workExperienceData = extractWorkExperience(secondSheet);

                // Populate the form with Work Experience data
                populateWorkExperience(workExperienceData);

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

                function safeTrim(value) {
                    return value && typeof value === "string"
                        ? value.trim()
                        : "";
                }

                // Extract Family Background Data from Excel
                const familyBackground = {};

                // Spouse Information
                familyBackground.spouseSurname = firstSheet["D36"]
                    ? safeTrim(firstSheet["D36"].v)
                    : "";
                familyBackground.spouseFirstName = firstSheet["D37"]
                    ? safeTrim(firstSheet["D37"].v)
                    : "";
                familyBackground.spouseMiddleName = firstSheet["D38"]
                    ? safeTrim(firstSheet["D38"].v)
                    : "";
                familyBackground.spouseNameExtension = firstSheet["G37"]
                    ? safeTrim(
                          firstSheet["G37"].v.replace(
                              /NAME EXTENSION \(JR\., SR\)/i,
                              ""
                          )
                      )
                    : ""; // Remove "NAME EXTENSION (JR., SR)" if present
                familyBackground.spouseOccupation = firstSheet["D39"]
                    ? safeTrim(firstSheet["D39"].v)
                    : "";
                familyBackground.spouseEmployerBusinessName = firstSheet["D40"]
                    ? safeTrim(firstSheet["D40"].v)
                    : "";
                familyBackground.spouseBusinessAddress = firstSheet["D41"]
                    ? safeTrim(firstSheet["D41"].v)
                    : "";
                familyBackground.spouseTelephoneNo = firstSheet["D42"]
                    ? safeTrim(firstSheet["D42"].v)
                    : "";

                // Father's Information
                familyBackground.fatherSurname = firstSheet["D43"]
                    ? safeTrim(firstSheet["D43"].v)
                    : "";
                familyBackground.fatherFirstName = firstSheet["D44"]
                    ? safeTrim(firstSheet["D44"].v)
                    : "";
                familyBackground.fatherMiddleName = firstSheet["D45"]
                    ? safeTrim(firstSheet["D45"].v)
                    : "";
                familyBackground.fatherNameExtension = firstSheet["G44"]
                    ? safeTrim(
                          firstSheet["G44"].v.replace(
                              /NAME EXTENSION \(JR\., SR\)/i,
                              ""
                          )
                      )
                    : ""; // Remove "NAME EXTENSION (JR., SR)" if present

                // Mother's Information (Maiden Name)
                familyBackground.motherSurname = firstSheet["D47"]
                    ? safeTrim(firstSheet["D47"].v)
                    : "";
                familyBackground.motherFirstName = firstSheet["D48"]
                    ? safeTrim(firstSheet["D48"].v)
                    : "";
                familyBackground.motherMiddleName = firstSheet["D49"]
                    ? safeTrim(firstSheet["D49"].v)
                    : "";

                // Extract Children Data as an Array
                familyBackground.children = [];
                for (let row = 37; row <= 48; row++) {
                    const childNameCell = firstSheet[`I${row}`];
                    const childBirthdateCell = firstSheet[`M${row}`];

                    if (childNameCell && childNameCell.v.trim()) {
                        familyBackground.children.push({
                            name: childNameCell.v.trim(),
                            birthdate: childBirthdateCell
                                ? safeDateFormat(childBirthdateCell.v)
                                : "Unknown",
                        });
                    }
                }

                // Education data extraction
                let educationData = [
                    { level: "elementary", row: 54 },
                    { level: "secondary", row: 55 },
                    { level: "vocational", row: 56 },
                    { level: "college", row: 57 },
                    { level: "graduate", row: 58 },
                ];

                educationData.forEach((edu) => {
                    let row = edu.row;
                    let prefix = edu.level;

                    document.getElementById(prefix + "School").value =
                        getCellValue(firstSheet, `D${row}`);
                    document.getElementById(prefix + "Degree").value =
                        getCellValue(firstSheet, `G${row}`);
                    document.getElementById(prefix + "From").value =
                        safeDateFormat(getCellValue(firstSheet, `J${row}`));
                    document.getElementById(prefix + "To").value =
                        safeDateFormat(getCellValue(firstSheet, `K${row}`));
                    document.getElementById(prefix + "HighestLevel").value =
                        getCellValue(firstSheet, `L${row}`);
                    document.getElementById(prefix + "YearGraduated").value =
                        getCellValue(firstSheet, `M${row}`);
                    document.getElementById(prefix + "Honors").value =
                        getCellValue(firstSheet, `N${row}`);
                });

                function extractCivilServiceEligibility(sheet) {
                    const eligibilityData = [];
                    let row = 5; // Start from row 5

                    while (row <= 11 && sheet[`A${row}`]) {
                        // Stop at row 11 or when no more data
                        eligibilityData.push({
                            careerService: sheet[`A${row}`]
                                ? sheet[`A${row}`].v
                                : "", // Column A
                            rating: sheet[`F${row}`] ? sheet[`F${row}`].v : "", // Column F
                            examinationDate: sheet[`G${row}`]
                                ? sheet[`G${row}`].v
                                : "", // Column G
                            examinationPlace: sheet[`I${row}`]
                                ? sheet[`I${row}`].v
                                : "", // Column I
                            licenseNumber: sheet[`L${row}`]
                                ? sheet[`L${row}`].v
                                : "", // Column L
                            validityDate: sheet[`M${row}`]
                                ? sheet[`M${row}`].v
                                : "", // Column M
                        });
                        row++;
                    }

                    return eligibilityData;
                }

                function extractWorkExperience(sheet) {
                    const workExperienceData = [];
                    let row = 18; // Start from row 18

                    while (row <= 45 && sheet[`A${row}`]) {
                        // Stop at row 45 or when no more data
                        workExperienceData.push({
                            inclusiveDatesFrom: sheet[`A${row}`]
                                ? sheet[`A${row}`].v
                                : "", // Column A
                            inclusiveDatesTo: sheet[`C${row - 1}`]
                                ? sheet[`C${row}`].v
                                : "", // Column C (row - 1 for "To")
                            positionTitle: sheet[`D${row}`]
                                ? sheet[`D${row}`].v
                                : "", // Column D
                            department: sheet[`G${row}`]
                                ? sheet[`G${row}`].v
                                : "", // Column G
                            monthlySalary: sheet[`J${row}`]
                                ? sheet[`J${row}`].v
                                : "", // Column J
                            salaryGrade: sheet[`K${row}`]
                                ? sheet[`K${row}`].v
                                : "", // Column K
                            appointmentStatus: sheet[`L${row}`]
                                ? sheet[`L${row}`].v
                                : "", // Column L
                            govtService: sheet[`M${row}`]
                                ? sheet[`M${row}`].v
                                : "", // Column M
                        });
                        row++;
                    }

                    return workExperienceData;
                }

                // Assign values to variables for existing fields
                const surname = surnameCell ? surnameCell.v : "";
                const firstname = firstnameCell ? firstnameCell.v : "";
                const middlename = middlenameCell ? middlenameCell.v : "";
                const nameExtension = nameExtensionCell
                    ? nameExtensionCell.v
                    : "";

                function excelSerialToJSDate(serial) {
                    // Check if serial is a string (date) or a number
                    if (typeof serial === "string") {
                        // Try converting string date to a JavaScript Date object
                        const jsDate = new Date(serial);
                        if (isNaN(jsDate)) {
                            console.error("Invalid date string:", serial);
                            return ""; // Return an empty string if the date is invalid
                        }
                        return jsDate.toISOString().split("T")[0]; // Format to YYYY-MM-DD
                    }

                    // If it's a number (Excel serial date)
                    if (
                        isNaN(serial) ||
                        serial === null ||
                        serial === undefined
                    ) {
                        console.error("Invalid Excel serial date:", serial);
                        return ""; // Return an empty string if invalid serial
                    }

                    const excelStartDate = new Date(Date.UTC(1899, 11, 30)); // Set to UTC to prevent timezone shifts
                    const jsDate = new Date(
                        excelStartDate.getTime() + serial * 86400000
                    );

                    if (isNaN(jsDate)) {
                        console.error("Invalid JS Date:", jsDate);
                        return ""; // Return empty string if JS date is invalid
                    }

                    return jsDate.toISOString().split("T")[0]; // Format to YYYY-MM-DD
                }

                // Assign Date of Birth value with proper formatting
                const dob =
                    dobCell && !isNaN(dobCell.v)
                        ? excelSerialToJSDate(dobCell.v)
                        : "";

                const pob = pobCell ? pobCell.v : "";

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
                        gender = "";
                    }
                } else {
                    gender = "";
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
                    civilStatus = "";
                }

                // Extract additional fields (height, weight, etc.)
                const height = heightCell ? heightCell.v : "";
                const weight = weightCell ? weightCell.v : "";
                const bloodType = bloodTypeCell ? bloodTypeCell.v : "";
                const gsisId = gsisIdCell ? gsisIdCell.v : "";
                const pagIbigId = pagIbigIdCell ? pagIbigIdCell.v : "";
                const philhealthNo = philhealthNoCell ? philhealthNoCell.v : "";
                const sssNo = sssNoCell ? sssNoCell.v : "";
                const tinNo = tinNoCell ? tinNoCell.v : "";
                const agencyEmpNo = agencyEmpNoCell ? agencyEmpNoCell.v : "";

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
                    : "";

                // Assign values to residential address variables
                const residentialHouseBlockLot = residentialHouseBlockLotCell
                    ? residentialHouseBlockLotCell.v
                    : "";
                const residentialStreet = residentialStreetCell
                    ? residentialStreetCell.v
                    : "";
                const residentialSubdivisionVillage =
                    residentialSubdivisionVillageCell
                        ? residentialSubdivisionVillageCell.v
                        : "";
                const excelResidentialProvince = residentialProvinceCell
                    ? residentialProvinceCell.v.toString().trim().toUpperCase()
                    : "";

                const excelResidentialCity = residentialCityMunicipalityCell
                    ? residentialCityMunicipalityCell.v
                          .toString()
                          .trim()
                          .toUpperCase()
                    : "";

                const excelResidentialBarangay = residentialBarangayCell
                    ? residentialBarangayCell.v.toString().trim()
                    : "";

                // Assign values to permanent address variables
                const permanentHouseBlockLot = permanentHouseBlockLotCell
                    ? permanentHouseBlockLotCell.v
                    : "";
                const permanentStreet = permanentStreetCell
                    ? permanentStreetCell.v
                    : "";
                const permanentSubdivisionVillage =
                    permanentSubdivisionVillageCell
                        ? permanentSubdivisionVillageCell.v
                        : "";
                const excelPermanentProvince = permanentProvinceCell
                    ? permanentProvinceCell.v.toString().trim().toUpperCase()
                    : "";

                const excelPermanentCity = permanentCityMunicipalityCell
                    ? permanentCityMunicipalityCell.v
                          .toString()
                          .trim()
                          .toUpperCase()
                    : "";

                const excelPermanentBarangay = permanentBarangayCell
                    ? permanentBarangayCell.v.toString().trim()
                    : "";
                const permanentZipCode = permanentZipCodeCell
                    ? permanentZipCodeCell.v
                    : "";

                const telephoneNumber = telephoneNumberCell
                    ? telephoneNumberCell.v
                    : "";
                const mobileNumber = mobileNumberCell ? mobileNumberCell.v : "";
                const emailAddress = emailAddressCell ? emailAddressCell.v : "";

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
                if (excelResidentialProvince !== "") {
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
                        if (excelResidentialCity !== "") {
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
                                if (excelResidentialBarangay !== "") {
                                    getZipCode();
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
                                    }, 750);
                                }
                            }, 750);
                        }
                    }, 750);
                }

                document.getElementById("permanentHouseNo").value =
                    permanentHouseBlockLot;
                document.getElementById("permanentStreet").value =
                    permanentStreet;
                document.getElementById("permanentSubdivision").value =
                    permanentSubdivisionVillage;

                if (excelPermanentProvince !== "") {
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
                        if (excelPermanentCity !== "") {
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
                                if (excelPermanentBarangay !== "") {
                                    getPermanentZipCode();
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
                if (dualCitizenshipCountry !== "") {
                    countrySelect.value = dualCitizenshipCountry;
                }

                // Spouse Information
                document.getElementById("spouseSurname").value =
                    familyBackground.spouseSurname;
                document.getElementById("spouseFirstName").value =
                    familyBackground.spouseFirstName;
                document.getElementById("spouseMiddleName").value =
                    familyBackground.spouseMiddleName;
                document.getElementById("spouseNameExtension").value =
                    familyBackground.spouseNameExtension;
                document.getElementById("spouseOccupation").value =
                    familyBackground.spouseOccupation;
                document.getElementById("spouseEmployer").value =
                    familyBackground.spouseEmployerBusinessName;
                document.getElementById("spouseTelephone").value =
                    familyBackground.spouseTelephoneNo;

                // Father's Information
                document.getElementById("fatherSurname").value =
                    familyBackground.fatherSurname;
                document.getElementById("fatherFirstName").value =
                    familyBackground.fatherFirstName;
                document.getElementById("fatherMiddleName").value =
                    familyBackground.fatherMiddleName;
                document.getElementById("fatherNameExtension").value =
                    familyBackground.fatherNameExtension;

                // Mother's Information (Maiden Name)
                document.getElementById("motherSurname").value =
                    familyBackground.motherSurname;
                document.getElementById("motherFirstName").value =
                    familyBackground.motherFirstName;
                document.getElementById("motherMiddleName").value =
                    familyBackground.motherMiddleName;

                // Children Information
                let childCount = 0; // Keep track of how many children we need to add
                familyBackground.children.forEach((child, index) => {
                    childCount++;

                    const childNameInput = document.getElementById(
                        `childName${childCount}`
                    );
                    const childDOBInput = document.getElementById(
                        `childDOB${childCount}`
                    );

                    if (childNameInput && childDOBInput) {
                        childNameInput.value = child.name;
                        childDOBInput.value = excelSerialToJSDate(
                            child.birthdate
                        ); // Use your function to convert the date
                    } else {
                        // Create new input fields if more children than available inputs
                        const newChildDiv = document.createElement("div");
                        newChildDiv.classList.add("child-entry");
                        newChildDiv.innerHTML = `
            <div class="form-row">
                <div class="form-group child-name">
                    <label for="childName${childCount}">Child's Name:</label>
                    <input type="text" id="childName${childCount}" name="childName${childCount}" value="${
                            child.name
                        }">
                </div>
                <div class="form-group child-dob">
                    <label for="childDOB${childCount}">Date of Birth:</label>
                    <input type="date" id="childDOB${childCount}" name="childDOB${childCount}" value="${excelSerialToJSDate(
                            child.birthdate
                        )}">
                </div>
            </div>
        `;
                        document
                            .getElementById("childrenContainer")
                            .appendChild(newChildDiv);
                    }
                });

                function populateCivilServiceEligibility(data) {
                    const tbody = document.querySelector(
                        "#civilServiceContainer"
                    );

                    if (!tbody) {
                        console.error("Error: Table tbody not found.");
                        return;
                    }

                    // Check if data exists and has the civil_service_eligibility key with at least one entry
                    if (
                        !data ||
                        !data.civil_service_eligibility ||
                        data.civil_service_eligibility.length === 0
                    ) {
                        // Exit the function without modifying the table
                        return;
                    }

                    // Clear existing rows only if there is data
                    tbody.innerHTML = "";

                    // Iterate over the civil_service_eligibility array and populate the table
                    data.civil_service_eligibility.forEach((item, index) => {
                        const entryNumber = index + 1; // Start numbering from 1

                        const row = document.createElement("tr");
                        row.classList.add("civil-service-entry");

                        row.innerHTML = `
                            <td><input type="text" id="eligibilityName${entryNumber}" name="eligibilityName${entryNumber}" value="${
                            item.careerService || ""
                        }"></td>
                            <td><input type="text" id="rating${entryNumber}" name="rating${entryNumber}" value="${
                            item.rating || ""
                        }"></td>
                            <td><input type="date" id="dateOfExam${entryNumber}" name="dateOfExam${entryNumber}" value="${safeDateFormat(
                            item.examinationDate || ""
                        )}"></td>
                            <td><input type="text" id="placeOfExam${entryNumber}" name="placeOfExam${entryNumber}" value="${
                            item.examinationPlace || ""
                        }"></td>
                            <td><input type="text" id="licenseNumber${entryNumber}" name="licenseNumber${entryNumber}" value="${
                            item.licenseNumber || ""
                        }"></td>
                            <td><input type="date" id="licenseValidity${entryNumber}" name="licenseValidity${entryNumber}" value="${safeDateFormat(
                            item.validityDate || ""
                        )}"></td>
                            <td><button type="button" class="remove-btn">Remove</button></td>
                        `;

                        tbody.appendChild(row);

                        // Add functionality to the remove button
                        row.querySelector(".remove-btn").addEventListener(
                            "click",
                            function () {
                                row.remove();
                            }
                        );
                    });
                }

                function populateWorkExperience(data) {
                    const container = document.getElementById(
                        "workExperienceContainer"
                    );
                    container.innerHTML = ""; // Clear existing table content

                    // Create table structure
                    const table = document.createElement("table");
                    table.classList.add("work-experience-table");

                    // Table header
                    table.innerHTML = `
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
                        <tbody id="workExperienceTableBody"></tbody>
                    `;

                    const tbody = table.querySelector(
                        "#workExperienceTableBody"
                    );

                    data.forEach((item, index) => {
                        const entryNumber = index + 1;

                        const row = document.createElement("tr");
                        row.innerHTML = `
                            <td><input type="date" id="inclusiveDatesFrom${entryNumber}" name="inclusiveDatesFrom${entryNumber}" value="${safeDateFormat(
                            item.inclusiveDatesFrom
                        )}"></td>
                            <td><input type="date" id="inclusiveDatesTo${entryNumber}" name="inclusiveDatesTo${entryNumber}" value="${safeDateFormat(
                            item.inclusiveDatesTo
                        )}"></td>
                            <td><input type="text" id="positionTitle${entryNumber}" name="positionTitle${entryNumber}" value="${
                            item.positionTitle
                        }"></td>
                            <td><input type="text" id="department${entryNumber}" name="department${entryNumber}" value="${
                            item.department
                        }"></td>
                            <td><input type="text" id="monthlySalary${entryNumber}" name="monthlySalary${entryNumber}" value="${
                            item.monthlySalary
                        }"></td>
                            <td><input type="text" id="salaryGrade${entryNumber}" name="salaryGrade${entryNumber}" value="${
                            item.salaryGrade
                        }"></td>
                            <td><input type="text" id="stepIncrement${entryNumber}" name="stepIncrement${entryNumber}" value="${
                            item.stepIncrement || ""
                        }"></td>
                            <td><input type="text" id="appointmentStatus${entryNumber}" name="appointmentStatus${entryNumber}" value="${
                            item.appointmentStatus
                        }"></td>
                            <td>
                                <label><input type="radio" id="govtServiceYes${entryNumber}" name="govtService${entryNumber}" value="Yes" ${
                            item.govtService === "Y" ? "checked" : ""
                        }> Yes</label>
                                <label><input type="radio" id="govtServiceNo${entryNumber}" name="govtService${entryNumber}" value="No" ${
                            item.govtService === "N" ? "checked" : ""
                        }> No</label>
                            </td>
                            <td><button type="button" class="remove-btn">Remove</button></td>
                        `;

                        tbody.appendChild(row);
                    });

                    container.appendChild(table);
                }

                // Remove function
                function removeWorkExperience(button) {
                    const row = button.closest("tr");
                    row.remove();
                }

                // Ensure Step 1 stays visible
                document.getElementById("step1").style.display = "block";

                // Ensure Step 2 remains hidden for now
                document.getElementById("step2").style.display = "none";
                document.getElementById("step3").style.display = "none";
                document.getElementById("step4").style.display = "none";
                document.getElementById("step5").style.display = "none";
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

        function fetchTowns(provinceCode, citySelect) {
            fetch(`/towns/${provinceCode}`)
                .then((response) => response.json())
                .then((data) => {
                    citySelect.innerHTML =
                        '<option value="">Select City/Municipality</option>';
                    data.forEach((town) => {
                        const option = document.createElement("option");
                        option.value = town.townCode; // Still using townCode as value
                        option.textContent = town.townName;
                        option.dataset.townName = town.townName; // Store townName in data attribute
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
            subOptions.classList.toggle("show", this.checked);

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
    console.log("Form initialized"); // Debugging
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
                }, 750);
            }, 750);
        });
});

document.addEventListener("DOMContentLoaded", function () {
    // Attach the change event handler after the DOM is loaded
    document
        .getElementById("residentialCity")
        .addEventListener("change", updateCityText);
    document
        .getElementById("cityTextField")
        .addEventListener("blur", getZipCode); // Trigger ZIP code lookup on blur
});

function updateCityText() {
    // Get the selected city name
    var citySelect = document.getElementById("residentialCity");
    var selectedCity = citySelect.options[citySelect.selectedIndex].text;

    // Capitalize the first letter of every word
    var capitalizedCity = selectedCity
        .split(" ") // Split the string into an array by spaces
        .map(function (word) {
            return word.charAt(0).toUpperCase() + word.slice(1).toLowerCase(); // Capitalize the first letter of each word
        })
        .join(" "); // Join the words back with spaces

    // Get the text field and set its value to the formatted city name
    var cityTextField = document.getElementById("cityTextField");
    cityTextField.value = capitalizedCity;
    getZipCode();
}

// Function to get the ZIP code based on the city name
function getZipCode() {
    // Get the city name from the text field
    var cityName = document.getElementById("cityTextField").value;

    // Load the ZIP code data from the JSON file
    fetch("/data/zipcodes.json")
        .then((response) => response.json())
        .then((zipcodes) => {
            // Loop through the zipcodes and check for a match
            for (let [zipcode, town] of Object.entries(zipcodes)) {
                if (town.includes(cityName)) {
                    // If match is found, set the ZIP code in the text field
                    document.getElementById("residentialZipCode").value =
                        zipcode;
                    return; // Exit the loop once we find the match
                }
            }

            // If no match is found, clear the ZIP code field
            document.getElementById("residentialZipCode").value = "";
        })
        .catch((error) => console.error("Error fetching ZIP codes:", error));
}

document.addEventListener("DOMContentLoaded", function () {
    // Attach the change event handler after the DOM is loaded for permanent city
    document
        .getElementById("permanentCity")
        .addEventListener("change", updatePermanentCityText);
    document
        .getElementById("permanentCityTextField")
        .addEventListener("blur", getPermanentZipCode); // Trigger ZIP code lookup on blur
});

function updatePermanentCityText() {
    // Get the selected city name from permanent city dropdown
    var citySelect = document.getElementById("permanentCity");
    var selectedCity = citySelect.options[citySelect.selectedIndex].text;

    // Capitalize the first letter of every word
    var capitalizedCity = selectedCity
        .split(" ") // Split the string into an array by spaces
        .map(function (word) {
            return word.charAt(0).toUpperCase() + word.slice(1).toLowerCase(); // Capitalize the first letter of each word
        })
        .join(" "); // Join the words back with spaces

    // Get the text field and set its value to the formatted city name
    var cityTextField = document.getElementById("permanentCityTextField");
    cityTextField.value = capitalizedCity;

    getPermanentZipCode();
}

// Function to get the ZIP code based on the permanent city name
function getPermanentZipCode() {
    // Get the city name from the text field
    var cityName = document.getElementById("permanentCityTextField").value;

    // Load the ZIP code data from the JSON file
    fetch("/data/zipcodes.json")
        .then((response) => response.json())
        .then((zipcodes) => {
            // Loop through the zipcodes and check for a match
            for (let [zipcode, town] of Object.entries(zipcodes)) {
                if (town.includes(cityName)) {
                    // If match is found, set the ZIP code in the text field
                    document.getElementById("permanentZipCode").value = zipcode;
                    return; // Exit the loop once we find the match
                }
            }

            // If no match is found, clear the ZIP code field
            document.getElementById("permanentZipCode").value = "";
        })
        .catch((error) => console.error("Error fetching ZIP codes:", error));
}

document.addEventListener("DOMContentLoaded", function () {
    const childrenContainer = document.getElementById("childrenContainer");
    const addChildButton = document.getElementById("addChildButton");
    const removeChildButton = document.getElementById("removeChildButton");

    let childCount = 1; // Track number of children

    // Function to add a new child entry
    addChildButton.addEventListener("click", function () {
        childCount++;
        const newChild = document.createElement("div");
        newChild.classList.add("child-entry");
        newChild.innerHTML = `
            <div class="form-row">
                <div class="form-group child-name">
                    <label for="childName${childCount}">Child's Name:</label>
                    <input type="text" id="childName${childCount}" name="childName${childCount}">
                </div>
                <div class="form-group child-dob">
                    <label for="childDOB${childCount}">Date of Birth:</label>
                    <input type="date" id="childDOB${childCount}" name="childDOB${childCount}">
                </div>
            </div>
        `;
        childrenContainer.appendChild(newChild);
    });

    // Function to remove the last child entry
    removeChildButton.addEventListener("click", function () {
        const childEntries = document.querySelectorAll(".child-entry");
        if (childEntries.length > 1) {
            childEntries[childEntries.length - 1].remove();
            childCount--;
        }
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const editButtons = document.querySelectorAll(".edit-btn");

    editButtons.forEach((button) => {
        button.addEventListener("click", function () {
            const updateId = this.getAttribute("data-id");
            console.log(updateId);
            // Fetch the data for the selected row
            fetch(`/get-update-data/${updateId}`)
                .then((response) => response.json())
                .then((data) => {
                    console.log(data);
                    // Populate the modal with the fetched data
                    populateModal(data);
                    // Open the modal

                    openModal();
                })
                .catch((error) => {
                    console.error("Error fetching data:", error);
                });
        });
    });

    // Function to populate the modal with data
    function populateModal(data) {
        // Personal Information
        console.log(data.personal_info.personal_info_id);
        document.getElementById("personalInfoId").value =
            data.personal_info.personal_info_id;
        document.getElementById("surname").value =
            data.personal_info.last_name || ""; // surname = last_name
        document.getElementById("firstName").value =
            data.personal_info.first_name || "";
        document.getElementById("middleName").value =
            data.personal_info.middle_name || "";
        document.getElementById("dateOfBirth").value =
            data.personal_info.date_of_birth || "";
        document.getElementById("placeOfBirth").value =
            data.personal_info.place_of_birth || "";
        document.getElementById("height").value =
            data.personal_info.height || "";
        document.getElementById("weight").value =
            data.personal_info.weight || "";
        document.getElementById("bloodType").value =
            data.personal_info.blood_type || "";
        document.getElementById("gsisId").value =
            data.personal_info.gsis_id || "";
        document.getElementById("philhealthId").value =
            data.personal_info.philhealth_id || "";
        document.getElementById("pagibigId").value =
            data.personal_info.pagibig_id || "";
        document.getElementById("sssNo").value =
            data.personal_info.sss_no || "";
        document.getElementById("tinNo").value =
            data.personal_info.tin_no || "";
        document.getElementById("agencyEmployeeNo").value =
            data.personal_info.agency_employee_no || "";
        document.getElementById("telephoneNo").value =
            data.personal_info.telephone_no || "";
        document.getElementById("mobileNo").value =
            data.personal_info.mobile_no || "";
        document.getElementById("email").value = data.personal_info.email || "";

        // Citizenship Fields
        document.getElementById("filipino").checked =
            data.personal_info.is_filipino || false;
        document.getElementById("dualCitizenship").checked =
            data.personal_info.is_dual_citizen || false;

        // Set the dual citizen type based on the value (byBirth or byNaturalization)
        if (data.personal_info.dual_citizen_type === "byBirth") {
            document.getElementById("byBirth").checked = true;
        } else if (
            data.personal_info.dual_citizen_type === "byNaturalization"
        ) {
            document.getElementById("byNaturalization").checked = true;
        }

        document.getElementById("countrySelect").value =
            data.personal_info.dual_citizen_country || "";

        // Directly show/hide dual citizenship fields based on the value of `is_dual_citizen`
        if (data.personal_info.is_dual_citizen) {
            document.querySelector(".sub-options").classList.add("show");
        } else {
            document.querySelector(".sub-options").classList.remove("show");
        }

        document.getElementById("sexMale").checked =
            data.personal_info.sex === "Male";
        document.getElementById("sexFemale").checked =
            data.personal_info.sex === "Female";

        // Civil Status
        const civilStatus = data.personal_info.civil_status || ""; // Get the civil status value
        console.log(civilStatus);
        if (civilStatus) {
            const civilStatusRadio = document.querySelector(
                `input[name="civilStatus"][value="${civilStatus}"]`
            );
            if (civilStatusRadio) {
                civilStatusRadio.checked = true; // Check the corresponding radio button
            } else {
                console.warn(
                    `Civil status value "${civilStatus}" does not match any radio button.`
                );
            }
        } else {
            console.warn("No civil status data found.");
        }

        // Residential Address
        if (data.residential_address) {
            // Set values without delay
            document.getElementById("residentialHouseNo").value =
                data.residential_address.house_no || "";
            document.getElementById("residentialStreet").value =
                data.residential_address.street || "";
            document.getElementById("residentialSubdivision").value =
                data.residential_address.subdivision || "";

            // Set Province, City, and Barangay with delay and proper flow
            const residentialProvince = data.residential_address.province || ""; // e.g., "0434"
            const residentialCity = data.residential_address.city || ""; // e.g., "043419"
            const residentialBarangay = data.residential_address.barangay || ""; // e.g., "043419014"

            if (residentialProvince !== "") {
                setTimeout(() => {
                    const resProvinceSelect = document.getElementById(
                        "residentialProvince"
                    );
                    // Find the option with the matching value (code)
                    for (let option of resProvinceSelect.options) {
                        if (option.value === residentialProvince) {
                            resProvinceSelect.value = option.value;
                            resProvinceSelect.dispatchEvent(
                                new Event("change")
                            );
                            break;
                        }
                    }

                    // Wait for cities to load after province change
                    if (residentialCity !== "") {
                        setTimeout(() => {
                            const resCitySelect =
                                document.getElementById("residentialCity");
                            // Find the option with the matching value (code)
                            for (let option of resCitySelect.options) {
                                if (option.value === residentialCity) {
                                    resCitySelect.value = option.value;
                                    resCitySelect.dispatchEvent(
                                        new Event("change")
                                    );
                                    break;
                                }
                            }

                            // Wait for barangays to load after city change
                            if (residentialBarangay !== "") {
                                setTimeout(() => {
                                    const resBarangaySelect =
                                        document.getElementById(
                                            "residentialBarangay"
                                        );
                                    // Find the option with the matching value (code)
                                    for (let option of resBarangaySelect.options) {
                                        if (
                                            option.value === residentialBarangay
                                        ) {
                                            resBarangaySelect.value =
                                                option.value;
                                            break;
                                        }
                                    }
                                }, 500); // 500ms delay after setting city
                            }
                        }, 500); // 500ms delay after setting province
                    }
                }, 500); // Initial 500ms delay
            }

            // Set Zip Code without delay
            document.getElementById("residentialZipCode").value =
                data.residential_address.zip_code || "";
        } else {
            // Clear residential address fields if no data
            document.getElementById("residentialHouseNo").value = "";
            document.getElementById("residentialStreet").value = "";
            document.getElementById("residentialSubdivision").value = "";
            document.getElementById("residentialBarangay").value = "";
            document.getElementById("residentialCity").value = "";
            document.getElementById("residentialProvince").value = "";
            document.getElementById("residentialZipCode").value = "";
        }

        // Permanent Address
        if (data.permanent_address) {
            // Set values without delay
            document.getElementById("permanentHouseNo").value =
                data.permanent_address.house_no || "";
            document.getElementById("permanentStreet").value =
                data.permanent_address.street || "";
            document.getElementById("permanentSubdivision").value =
                data.permanent_address.subdivision || "";

            // Set Province, City, and Barangay with delay and proper flow
            const permanentProvince = data.permanent_address.province || ""; // e.g., "0434"
            const permanentCity = data.permanent_address.city || ""; // e.g., "043419"
            const permanentBarangay = data.permanent_address.barangay || ""; // e.g., "043419014"

            if (permanentProvince !== "") {
                setTimeout(() => {
                    const permProvinceSelect =
                        document.getElementById("permanentProvince");

                    if (permProvinceSelect) {
                        let provinceFound = false;
                        for (let option of permProvinceSelect.options) {
                            if (option.value === permanentProvince) {
                                permProvinceSelect.value = option.value;
                                permProvinceSelect.dispatchEvent(
                                    new Event("change")
                                );
                                provinceFound = true;
                                break;
                            }
                        }
                        if (!provinceFound) {
                            console.warn(
                                "No matching province found for:",
                                permanentProvince
                            );
                        }
                    } else {
                        console.error(
                            "Permanent Province Select element not found."
                        );
                    }

                    // Wait for cities to load after province change
                    if (permanentCity !== "") {
                        setTimeout(() => {
                            const permCitySelect =
                                document.getElementById("permanentCity");

                            if (permCitySelect) {
                                let cityFound = false;
                                for (let option of permCitySelect.options) {
                                    if (option.value === permanentCity) {
                                        permCitySelect.value = option.value;
                                        permCitySelect.dispatchEvent(
                                            new Event("change")
                                        );
                                        cityFound = true;
                                        break;
                                    }
                                }
                                if (!cityFound) {
                                    console.warn(
                                        "No matching city found for:",
                                        permanentCity
                                    );
                                }
                            } else {
                                console.error(
                                    "Permanent City Select element not found."
                                );
                            }

                            // Wait for barangays to load after city change
                            if (permanentBarangay !== "") {
                                setTimeout(() => {
                                    const permBarangaySelect =
                                        document.getElementById(
                                            "permanentBarangay"
                                        );

                                    if (permBarangaySelect) {
                                        let barangayFound = false;
                                        for (let option of permBarangaySelect.options) {
                                            if (
                                                option.value ===
                                                permanentBarangay
                                            ) {
                                                permBarangaySelect.value =
                                                    option.value;
                                                barangayFound = true;
                                                break;
                                            }
                                        }
                                        if (!barangayFound) {
                                            console.warn(
                                                "No matching barangay found for:",
                                                permanentBarangay
                                            );
                                        }
                                    } else {
                                        console.error(
                                            "Permanent Barangay Select element not found."
                                        );
                                    }
                                }, 500); // 500ms delay after setting city
                            }
                        }, 500); // 500ms delay after setting province
                    }
                }, 1500); // Initial 500ms delay
            }

            // Set Zip Code without delay
            document.getElementById("permanentZipCode").value =
                data.permanent_address.zip_code || "";
        } else {
            // Clear permanent address fields if no data
            document.getElementById("permanentHouseNo").value = "";
            document.getElementById("permanentStreet").value = "";
            document.getElementById("permanentSubdivision").value = "";
            document.getElementById("permanentBarangay").value = "";
            document.getElementById("permanentCity").value = "";
            document.getElementById("permanentProvince").value = "";
            document.getElementById("permanentZipCode").value = "";
        }

        // Family Background
        if (data.family_background) {
            document.getElementById("spouseSurname").value =
                data.family_background.spouse_surname || "";
            document.getElementById("spouseFirstName").value =
                data.family_background.spouse_first_name || "";
            document.getElementById("spouseMiddleName").value =
                data.family_background.spouse_middle_name || "";
            document.getElementById("spouseNameExtension").value =
                data.family_background.spouse_name_extension || "";
            document.getElementById("spouseOccupation").value =
                data.family_background.spouse_occupation || "";
            document.getElementById("spouseEmployer").value =
                data.family_background.spouse_employer || "";
            document.getElementById("spouseTelephone").value =
                data.family_background.spouse_telephone || "";
            document.getElementById("fatherSurname").value =
                data.family_background.father_surname || "";
            document.getElementById("fatherFirstName").value =
                data.family_background.father_first_name || "";
            document.getElementById("fatherMiddleName").value =
                data.family_background.father_middle_name || "";
            document.getElementById("motherSurname").value =
                data.family_background.mother_surname || "";
            document.getElementById("motherFirstName").value =
                data.family_background.mother_first_name || "";
            document.getElementById("motherMiddleName").value =
                data.family_background.mother_middle_name || "";
        } else {
            // Clear family background fields if no data
            document.getElementById("spouseSurname").value = "";
            document.getElementById("spouseFirstName").value = "";
            document.getElementById("spouseMiddleName").value = "";
            document.getElementById("spouseNameExtension").value = "";
            document.getElementById("spouseOccupation").value = "";
            document.getElementById("spouseEmployer").value = "";
            document.getElementById("spouseTelephone").value = "";
            document.getElementById("fatherSurname").value = "";
            document.getElementById("fatherFirstName").value = "";
            document.getElementById("fatherMiddleName").value = "";
            document.getElementById("motherSurname").value = "";
            document.getElementById("motherFirstName").value = "";
            document.getElementById("motherMiddleName").value = "";
        }

        if (
            data.educational_backgrounds &&
            data.educational_backgrounds.length > 0
        ) {
            data.educational_backgrounds.forEach((education) => {
                // Convert "Graduate Studies" to "graduate" for ID generation
                const level =
                    education.level.toLowerCase() === "graduate studies"
                        ? "graduate"
                        : education.level.toLowerCase();

                const fields = [
                    "School",
                    "Degree",
                    "From",
                    "To",
                    "HighestLevel", // This should be mapped correctly
                    "YearGraduated", // This should be mapped correctly
                    "Honors",
                ];

                fields.forEach((field) => {
                    const fieldId = `${level}${field}`; // ID format will be "graduateYearGraduated", "elementaryYearGraduated", etc.
                    const fieldElement = document.getElementById(fieldId);

                    if (fieldElement) {
                        let value = "";

                        // Adjust field name mapping for snake_case (highest_level, year_graduate)
                        if (field.toLowerCase() === "highestlevel") {
                            value = education.highest_level || ""; // Using snake_case
                        } else if (field.toLowerCase() === "yeargraduated") {
                            value =
                                education.year_graduated !== null
                                    ? education.year_graduated
                                    : ""; // Ensure value is a number, default to empty string if null
                        } else {
                            value = education[field.toLowerCase()] || "";
                        }

                        fieldElement.value = value;
                    } else {
                        console.warn(`Element with id ${fieldId} not found.`);
                    }
                });
            });
        } else {
            console.log(data.educational_backgrounds); // Log the educational backgrounds if no data
            // Clear educational background fields if no data
            const educationLevels = [
                "elementary",
                "secondary",
                "vocational",
                "college",
                "graduate",
            ];
            educationLevels.forEach((level) => {
                const fields = [
                    "School",
                    "Degree",
                    "From",
                    "To",
                    "HighestLevel",
                    "YearGraduated",
                    "Honors",
                ];

                fields.forEach((field) => {
                    const fieldId = `${level}${field}`;
                    const fieldElement = document.getElementById(fieldId);
                    if (fieldElement) {
                        fieldElement.value = "";
                    } else {
                        console.warn(`Element with id ${fieldId} not found.`);
                    }
                });
            });
        }

        // Civil Service Eligibility
        const civilServiceContainer = document.querySelector(
            "#civilServiceContainer"
        );
        civilServiceContainer.innerHTML = ""; // Clear existing rows

        if (
            data.civil_service_eligibilities &&
            data.civil_service_eligibilities.length > 0
        ) {
            data.civil_service_eligibilities.forEach((eligibility, index) => {
                const row = document.createElement("tr");
                row.innerHTML = `
            <td><input type="text" value="${
                eligibility.eligibility_name || ""
            }"></td>
            <td><input type="text" value="${eligibility.rating || ""}"></td>
            <td><input type="date" value="${
                eligibility.date_of_exam || ""
            }"></td>
            <td><input type="text" value="${
                eligibility.place_of_exam || ""
            }"></td>
            <td><input type="text" value="${
                eligibility.license_number || ""
            }"></td>
            <td><input type="date" value="${
                eligibility.license_validity || ""
            }"></td>
            <td><button type="button" class="remove-row">Remove</button></td>
        `;
                civilServiceContainer.appendChild(row);
                // Add functionality to the Remove button
                row.querySelector(".remove-row").addEventListener(
                    "click",
                    function () {
                        row.remove();
                    }
                );
            });
        }

        // ===== Work Experience Section =====
        const workExperienceContainer = document.getElementById(
            "workExperienceContainer"
        );
        workExperienceContainer.innerHTML = ""; // Clear existing content

        // Create table structure
        const table = document.createElement("table");
        table.classList.add("work-experience-table");

        // Table header
        table.innerHTML = `
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
        <tbody id="workExperienceTableBody"></tbody>
    `;

        const tbody = table.querySelector("#workExperienceTableBody");

        if (data.work_experiences && data.work_experiences.length > 0) {
            data.work_experiences.forEach((experience, index) => {
                const entryNumber = index + 1;

                const row = document.createElement("tr");
row.innerHTML = `
    <td><input type="date" id="inclusiveDatesFrom${entryNumber}" name="work_experience[${index}][from]" value="${experience.from || ""}"></td>
    <td><input type="date" id="inclusiveDatesTo${entryNumber}" name="work_experience[${index}][to]" value="${experience.to || ""}"></td>
    <td><input type="text" id="positionTitle${entryNumber}" name="work_experience[${index}][position_title]" value="${experience.position_title || ""}"></td>
    <td><input type="text" id="department${entryNumber}" name="work_experience[${index}][department]" value="${experience.department || ""}"></td>
    <td><input type="text" id="monthlySalary${entryNumber}" name="work_experience[${index}][monthly_salary]" value="${experience.monthly_salary || ""}"></td>
    <td><input type="text" id="salaryGrade${entryNumber}" name="work_experience[${index}][salary_grade]" value="${experience.salary_grade || ""}"></td>
    <td><input type="text" id="stepIncrement${entryNumber}" name="work_experience[${index}][step_increment]" value="${experience.step_increment || ""}"></td>
    <td><input type="text" id="appointmentStatus${entryNumber}" name="work_experience[${index}][appointment_status]" value="${experience.appointment_status || ""}"></td>
    <td>
        <input type="radio" id="govtServiceYes${entryNumber}" name="govtService${entryNumber}" value="Yes" ${experience.govt_service === "Yes" ? "checked" : ""}>
        <label for="govtServiceYes${entryNumber}">Yes</label>
        <input type="radio" id="govtServiceNo${entryNumber}" name="govtService${entryNumber}" value="No" ${experience.govt_service === "No" ? "checked" : ""}>
        <label for="govtServiceNo${entryNumber}">No</label>
    </td>
    <td><button type="button" class="remove-btn">Remove</button></td>
`;

                tbody.appendChild(row);

                // Add event listener for the remove button
                row.querySelector(".remove-btn").addEventListener(
                    "click",
                    function () {
                        row.remove();
                        // Optionally, reindex the remaining rows if needed
                    }
                );
            });
        }

        workExperienceContainer.appendChild(table);
    }

    // Function to open the modal
    function openModal() {
        const modal = document.getElementById("uploadModal");
        modal.style.display = "block";
    }

    // Function to close the modal
    const closeModalButton = document.querySelector(".close");
    closeModalButton.addEventListener("click", function () {
        const modal = document.getElementById("uploadModal");
        modal.style.display = "none"; // Close the modal

        // Reset the flag when the modal is closed
        console.log("Modal closed manually, resetting isEditClicked to false");
        isEditClicked = false;
    });

    // Close modal when clicking outside of it
    window.addEventListener("click", function (event) {
        const modal = document.getElementById("uploadModal");
        if (event.target === modal) {
            modal.style.display = "none"; // Close the modal

            // Reset the flag when the modal is closed
            console.log(
                "Modal closed by clicking outside, resetting isEditClicked to false"
            );
            isEditClicked = false;
        }
    });
});

document.querySelectorAll(".print-btn").forEach((button) => {
    button.addEventListener("click", function () {
        const personalInfoId = button.getAttribute("data-id"); // Get the ID of the clicked row

        // Fetch report data from the server
        fetch(`/generate-report/${personalInfoId}`)
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    const reportData = data.reportData;
                    console.log(reportData);

                    // Generate the HTML for the report (you can modify this to fit your design)
                    const reportHTML = `
                        <html>
                            <head>
                                <title>Personal Information Report</title>
                                <style>
                                    @media print {
                                        body { font-family: Arial, sans-serif; }
                                        .report-container { padding: 20px; }
                                        h2, h3 { margin-bottom: 10px; }
                                        p { margin: 5px 0; }
                                        .no-print { display: none; }
                                    }
                                </style>
                            </head>
                            <body>
                                <div class="report-container">
                                    <h2>Personal Information Report</h2>
                                    <p><strong>Name:</strong> ${
                                        reportData.personalInfo.first_name
                                    } ${reportData.personalInfo.middle_name} ${
                        reportData.personalInfo.last_name
                    }</p>
                                    <p><strong>Birth Date:</strong> ${
                                        reportData.personalInfo.date_of_birth
                                    }</p>
                                    <p><strong>Place of Birth:</strong> ${
                                        reportData.personalInfo.place_of_birth
                                    }</p>
                                    <p><strong>Gender:</strong> ${
                                        reportData.personalInfo.sex
                                    }</p>
                                    <p><strong>Civil Status:</strong> ${
                                        reportData.personalInfo.civil_status
                                    }</p>
                                    <p><strong>Height:</strong> ${
                                        reportData.personalInfo.height
                                    } m</p>
                                    <p><strong>Weight:</strong> ${
                                        reportData.personalInfo.weight
                                    } kg</p>
                                    <p><strong>Blood Type:</strong> ${
                                        reportData.personalInfo.blood_type
                                    }</p>
                                    <p><strong>Telephone No:</strong> ${
                                        reportData.personalInfo.telephone_no
                                    }</p>
                                    <p><strong>Email:</strong> ${
                                        reportData.personalInfo.email
                                    }</p>
                                    <p><strong>Citizenship:</strong> ${
                                        reportData.personalInfo.is_filipino
                                            ? "Filipino"
                                            : "Non-Filipino"
                                    }</p>
                                    <p><strong>Dual Citizenship:</strong> ${
                                        reportData.personalInfo.is_dual_citizen
                                            ? "Yes"
                                            : "No"
                                    }</p>
                                    ${
                                        reportData.personalInfo.is_dual_citizen
                                            ? `
                                        <p><strong>Dual Citizenship Type:</strong> ${reportData.personalInfo.dual_citizen_type}</p>
                                        <p><strong>Dual Citizenship Country:</strong> ${reportData.personalInfo.dual_citizen_country}</p>
                                    `
                                            : ""
                                    }

                                    <h3>Addresses</h3>
                                        ${reportData.addresses
                                            .map(
                                                (address) => `
                                            <p><strong>Type:</strong> ${
                                                address.type === "residential"
                                                    ? "Residential Address"
                                                    : "Permanent Address"
                                            }</p>
                                            <p><strong>House No:</strong> ${
                                                address.house_no
                                            }</p>
                                            <p><strong>Street:</strong> ${
                                                address.street
                                            }</p>
                                            <p><strong>Subdivision:</strong> ${
                                                address.subdivision
                                            }</p>
                                            <p><strong>Barangay:</strong> ${
                                                address.barangay
                                            }</p>
                                            <p><strong>City:</strong> ${
                                                address.city
                                            }</p>
                                            <p><strong>Province:</strong> ${
                                                address.province
                                            }</p>
                                            <p><strong>Zip Code:</strong> ${
                                                address.zip_code
                                            }</p>
                                            <hr>
                                        `
                                            )
                                            .join("")}
                                    <h3>Family Background</h3>
                                    ${reportData.familyBackgrounds
                                        .map(
                                            (family) => `
                                        <p><strong>Spouse:</strong> ${family.spouse_surname} ${family.spouse_first_name} ${family.spouse_middle_name}</p>
                                        <p><strong>Spouse Occupation:</strong> ${family.spouse_occupation}</p>
                                        <p><strong>Spouse Employer:</strong> ${family.spouse_employer}</p>
                                        <p><strong>Spouse Telephone:</strong> ${family.spouse_telephone}</p>
                                        <p><strong>Father:</strong> ${family.father_surname} ${family.father_first_name} ${family.father_middle_name}</p>
                                        <p><strong>Mother:</strong> ${family.mother_surname} ${family.mother_first_name} ${family.mother_middle_name}</p>
                                    `
                                        )
                                        .join("")}

                                    <h3>Educational Background</h3>
                                        ${reportData.education
                                            .map(
                                                (edu) => `
                                            <p><strong>Level:</strong> ${edu.level}</p>
                                            <p><strong>School:</strong> ${edu.school}</p>
                                            <p><strong>Degree:</strong> ${edu.degree}</p>
                                            <p><strong>Period:</strong> ${edu.from} to ${edu.to}</p>
                                            <p><strong>Highest Level:</strong> ${edu.highest_level}</p>
                                            <p><strong>Year Graduated:</strong> ${edu.year_graduated}</p>
                                            <p><strong>Honors:</strong> ${edu.honors}</p>
                                            <hr>
                                        `
                                            )
                                            .join("")}

                                    <h3>Work Experience</h3>
                                    ${reportData.workExperiences
                                        .map(
                                            (work) => `
                                        <p><strong>Position Title:</strong> ${work.positionTitle}</p>
                                    `
                                        )
                                        .join("")}
                                </div>
                            </body>
                        </html>`;

                    // Open a new window and print the report
                    const printWindow = window.open(
                        "",
                        "",
                        "width=800,height=600"
                    );
                    printWindow.document.write(reportHTML);
                    printWindow.document.close();
                    printWindow.print();
                } else {
                    alert("Error: " + data.message);
                }
            })
            .catch((error) => {
                console.error("Error fetching report data:", error);
            });
    });
});
