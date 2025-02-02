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
        // Add other fields from Step 1 as needed
    };

    // Step 2: Family Background
    formData.familyBackground = {
        spouseName: document.getElementById("spouseFirstName").value,
        children: [],
        fatherName: document.getElementById("fatherFirstName").value,
        motherName: document.getElementById("motherFirstName").value,
        // Add other fields from Step 2 as needed
    };

    // Collect children's data
    const childrenContainer = document.getElementById("childrenContainer");
    const childrenEntries = childrenContainer.querySelectorAll(".child-entry");
    childrenEntries.forEach((entry, index) => {
        formData.familyBackground.children.push({
            childName: entry.querySelector(`#childName${index + 1}`).value,
            childDOB: entry.querySelector(`#childDOB${index + 1}`).value,
        });
    });

    // Step 3: Educational Background
    formData.educationalBackground = {
        elementary: {
            school: document.getElementById("elementarySchool").value,
            degree: document.getElementById("elementaryDegree").value,
            from: document.getElementById("elementaryFrom").value,
            to: document.getElementById("elementaryTo").value,
            highestLevel: document.getElementById("elementaryHighestLevel").value,
            yearGraduated: document.getElementById("elementaryYearGraduated").value,
            honors: document.getElementById("elementaryHonors").value,
        },
        secondary: {
            school: document.getElementById("secondarySchool").value,
            degree: document.getElementById("secondaryDegree").value,
            from: document.getElementById("secondaryFrom").value,
            to: document.getElementById("secondaryTo").value,
            highestLevel: document.getElementById("secondaryHighestLevel").value,
            yearGraduated: document.getElementById("secondaryYearGraduated").value,
            honors: document.getElementById("secondaryHonors").value,
        },
        // Add other education levels (vocational, college, graduate studies) as needed
    };

    // Step 4: Civil Service Eligibility
    formData.civilServiceEligibility = [];
    const civilServiceContainer = document.getElementById("civilServiceContainer");
    const civilServiceEntries = civilServiceContainer.querySelectorAll(".civil-service-entry");
    civilServiceEntries.forEach((entry, index) => {
        formData.civilServiceEligibility.push({
            eligibilityName: entry.querySelector(`#eligibilityName${index + 1}`).value,
            rating: entry.querySelector(`#rating${index + 1}`).value,
            dateOfExam: entry.querySelector(`#dateOfExam${index + 1}`).value,
            placeOfExam: entry.querySelector(`#placeOfExam${index + 1}`).value,
            licenseNumber: entry.querySelector(`#licenseNumber${index + 1}`).value,
            licenseValidity: entry.querySelector(`#licenseValidity${index + 1}`).value,
        });
    });

    // Step 5: Work Experience
    formData.workExperience = [];
    const workExperienceContainer = document.getElementById("workExperienceContainer");
    const workExperienceEntries = workExperienceContainer.querySelectorAll(".work-experience-entry");
    workExperienceEntries.forEach((entry, index) => {
        formData.workExperience.push({
            companyName: entry.querySelector(`#companyName${index + 1}`).value,
            position: entry.querySelector(`#position${index + 1}`).value,
            startDate: entry.querySelector(`#startDate${index + 1}`).value,
            endDate: entry.querySelector(`#endDate${index + 1}`).value,
        });
    });

    return formData;
}

// Reset form to initial state
function resetForm() {
    currentStep = 1;
    showStep(currentStep);
    document.getElementById("pdsForm").reset();
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
        <div class="form-group">
            <label for="companyName${entryCount}">Company Name:</label>
            <input type="text" id="companyName${entryCount}" name="companyName${entryCount}">
        </div>
        <div class="form-group">
            <label for="position${entryCount}">Position:</label>
            <input type="text" id="position${entryCount}" name="position${entryCount}">
        </div>
        <div class="form-group">
            <label for="startDate${entryCount}">Start Date:</label>
            <input type="date" id="startDate${entryCount}" name="startDate${entryCount}">
        </div>
        <div class="form-group">
            <label for="endDate${entryCount}">End Date:</label>
            <input type="date" id="endDate${entryCount}" name="endDate${entryCount}">
        </div>
        <hr class="separator"> <!-- Add a class to the <hr> -->
    `;

        workExperienceContainer.appendChild(workExperienceEntry);
    });
