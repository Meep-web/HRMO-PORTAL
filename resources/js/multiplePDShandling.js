import * as XLSX from "xlsx";

document.addEventListener("DOMContentLoaded", function () {
    // Modal Handling Elements
    const multiplePdsModal = document.getElementById("multiplePDSModal");
    const multiplePdsBtn = document.getElementById("extractMultipleDataButton");
    const multiplePdsClose = document.querySelector(".multiple-pds-close");
    const multiplePdsCancel = document.querySelector(".multiple-pds-cancel");
    const multiplePdsConfirm = document.querySelector(".multiple-pds-confirm");

    // File Handling Elements
    const excelUpload = document.getElementById("excelUpload");

    // Open modal when clicking Extract Multiple Data button
    if (multiplePdsBtn) {
        multiplePdsBtn.addEventListener("click", function () {
            if (!excelUpload.files.length) {
                alert("Please select a file first.");
                return;
            }

            const file = excelUpload.files[0];
            const reader = new FileReader();

            reader.onload = function (e) {
                try {
                    const data = new Uint8Array(e.target.result);
                    const workbook = XLSX.read(data, { type: "array" });

                    // Check for required worksheets
                    const requiredSheets = [
                        "personalInformation",
                        "familyBackground",
                        "educationalBackground",
                        "WorkExperience",
                        "CivilServiceEligibility",
                    ];
                    const missingSheets = requiredSheets.filter(
                        (sheet) => !workbook.SheetNames.includes(sheet)
                    );
                    if (missingSheets.length > 0) {
                        alert(
                            `Please upload the proper format of data, to get the excel sheet with the proper format you can download it using the download button below`
                        );
                        return;
                    }

                    // Extract data from all worksheets
                    const personalData = extractDataFromSheet(
                        workbook.Sheets["personalInformation"],
                        3
                    );
                    const familyData = extractFamilyData(
                        workbook.Sheets["familyBackground"],
                        4
                    );
                    const educationData = extractEducationalData(
                        workbook.Sheets["educationalBackground"],
                        4
                    );
                    const workExperienceData = extractWorkExperienceData(
                        workbook.Sheets["WorkExperience"],
                        4
                    );
                    const civilServiceData = extractCivilServiceData(
                        workbook.Sheets["CivilServiceEligibility"],
                        3
                    );

                    // Combine all data
                    const combinedData = personalData.map(
                        (personal, index) => ({
                            personalInformation: { ...personal },
                            family: familyData[index] || {},
                            education: educationData[index] || {},
                            workExperience: workExperienceData[index] || {},
                            civilServiceEligibility:
                                civilServiceData[index] || [],
                        })
                    );

                    console.log("Final Combined Data:", combinedData);

                    // Render data into the modal tables
                    renderAllTables(combinedData);

                    // Open the modal after rendering the data
                    multiplePdsModal.style.display = "block";
                } catch (error) {
                    console.error("Error processing file:", error);
                    alert("An error occurred. Please check the file format.");
                }
            };

            reader.readAsArrayBuffer(file);
        });
    }

    // Close modal handlers
    function closeMultiplePdsModal() {
        multiplePdsModal.style.display = "none";
    }

    if (multiplePdsClose)
        multiplePdsClose.addEventListener("click", closeMultiplePdsModal);
    if (multiplePdsCancel)
        multiplePdsCancel.addEventListener("click", closeMultiplePdsModal);

    // Confirm button handler
    if (multiplePdsConfirm) {
        multiplePdsConfirm.addEventListener("click", function () {
            // Handle confirmation logic
            alert("Extraction confirmed!");
            closeMultiplePdsModal();
        });
    }

    // Tab switching functionality
    document.querySelectorAll(".tab-button").forEach((button) => {
        button.addEventListener("click", function () {
            const tabId = this.dataset.tab;

            // Remove active class from all buttons and panes
            document
                .querySelectorAll(".tab-button, .tab-pane")
                .forEach((el) => {
                    el.classList.remove("active");
                });

            // Add active class to clicked button and corresponding pane
            this.classList.add("active");
            document.getElementById(`${tabId}-tab`).classList.add("active");
        });
    });
});

// ==================== Helper Functions ====================

// Render all tables with combined data
function renderAllTables(combinedData) {
    renderPersonalTable(combinedData, "personalPreview");
    renderFamilyTable(combinedData, "familyPreview");
    renderEducationTable(combinedData, "educationPreview");
    renderCivilServiceTable(combinedData, "civilServicePreview");
    renderWorkExperienceTable(combinedData, "workExperiencePreview");
}

// Render Personal Information Table
function renderPersonalTable(data, targetId) {
    const tbody = document.getElementById(targetId);
    tbody.innerHTML = ""; // Clear existing content

    data.forEach((employee) => {
        const personal = employee.personalInformation;
        const row = document.createElement("tr");

        // Combine name components
        const fullName = `${personal.firstName || ""} ${
            personal.middleName || ""
        } ${personal.surname || ""}`
            .replace(/\s+/g, " ")
            .trim();

        row.innerHTML = `
            <td><input type="text" value="${fullName}" readonly></td>
            <td><input type="text" value="${personal.dateOfBirth || ""}"></td>
            <td><input type="text" value="${personal.placeOfBirth || ""}"></td>
            <td><input type="text" value="${personal.sex || ""}"></td>
            <td><input type="text" value="${personal.civilStatus || ""}"></td>
            <td><input type="text" value="${personal.height || ""}"></td>
            <td><input type="text" value="${personal.weight || ""}"></td>
            <td><input type="text" value="${personal.bloodType || ""}"></td>
            <td><input type="text" value="${personal.gsisID || ""}"></td>
            <td><input type="text" value="${personal.pagibigID || ""}"></td>
            <td><input type="text" value="${personal.philHealthNo || ""}"></td>
            <td><input type="text" value="${personal.sssNo || ""}"></td>
            <td><input type="text" value="${personal.tinNo || ""}"></td>
            <td><input type="text" value="${
                personal.agencyEmployeeNo || ""
            }"></td>
            <td><input type="text" value="${personal.filipino || ""}"></td>
            <td><input type="text" value="${
                personal.dualCitizenship || ""
            }"></td>
            <td><input type="text" value="${
                personal.dualCitizenshipCountry || ""
            }"></td>
            <!-- Residential Address -->
            <td><input type="text" value="${
                personal.residentialAddress?.houseBlockLotNo || ""
            }"></td>
            <td><input type="text" value="${
                personal.residentialAddress?.street || ""
            }"></td>
            <td><input type="text" value="${
                personal.residentialAddress?.subdivisionVillage || ""
            }"></td>
            <td><input type="text" value="${
                personal.residentialAddress?.barangay || ""
            }"></td>
            <td><input type="text" value="${
                personal.residentialAddress?.cityMunicipality || ""
            }"></td>
            <td><input type="text" value="${
                personal.residentialAddress?.province || ""
            }"></td>
            <!-- Permanent Address -->
            <td><input type="text" value="${
                personal.permanentAddress?.houseBlockLotNo || ""
            }"></td>
            <td><input type="text" value="${
                personal.permanentAddress?.street || ""
            }"></td>
            <td><input type="text" value="${
                personal.permanentAddress?.subdivisionVillage || ""
            }"></td>
            <td><input type="text" value="${
                personal.permanentAddress?.barangay || ""
            }"></td>
            <td><input type="text" value="${
                personal.permanentAddress?.cityMunicipality || ""
            }"></td>
            <td><input type="text" value="${
                personal.permanentAddress?.province || ""
            }"></td>
            <td><input type="text" value="${personal.telephoneNo || ""}"></td>
            <td><input type="text" value="${personal.mobileNo || ""}"></td>
            <td><input type="text" value="${personal.emailAddress || ""}"></td>
        `;
        tbody.appendChild(row);
    });
}

// Render Family Background Table
function renderFamilyTable(data, targetId) {
    const tbody = document.getElementById(targetId);
    tbody.innerHTML = "";

    data.forEach((employee) => {
        const family = employee.family;
        const personal = employee.personalInformation;
        const row = document.createElement("tr");
        const fullName = `${personal.firstName || ""} ${
            personal.middleName || ""
        } ${personal.surname || ""}`.trim();

        row.innerHTML = `
            <td><input type="text" value="${fullName}" readonly></td>
            <td><input type="text" value="${family.spouse?.surname || ""}"></td>
            <td><input type="text" value="${
                family.spouse?.firstName || ""
            }"></td>
            <td><input type="text" value="${
                family.spouse?.middleName || ""
            }"></td>
            <td><input type="text" value="${
                family.spouse?.nameExtension || ""
            }"></td>
            <td><input type="text" value="${
                family.spouse?.occupation || ""
            }"></td>
            <td><input type="text" value="${
                family.spouse?.employer || ""
            }"></td>
            <td><input type="text" value="${
                family.spouse?.businessAddress || ""
            }"></td>
            <td><input type="text" value="${
                family.spouse?.telephoneNo || ""
            }"></td>
            <td><input type="text" value="${
                family.children?.map((c) => c.name).join(", ") || ""
            }"></td>
            <td><input type="text" value="${
                family.children?.map((c) => c.birthdate).join(", ") || ""
            }"></td>
            <td><input type="text" value="${family.father?.surname || ""}"></td>
            <td><input type="text" value="${
                family.father?.firstName || ""
            }"></td>
            <td><input type="text" value="${
                family.father?.middleName || ""
            }"></td>
            <td><input type="text" value="${
                family.father?.nameExtension || ""
            }"></td>
            <td><input type="text" value="${family.mother?.surname || ""}"></td>
            <td><input type="text" value="${
                family.mother?.firstName || ""
            }"></td>
            <td><input type="text" value="${
                family.mother?.middleName || ""
            }"></td>
        `;
        tbody.appendChild(row);
    });
}

// Render Education Table (Single Row Per Person)
function renderEducationTable(data, targetId) {
    const tbody = document.getElementById(targetId);
    tbody.innerHTML = "";

    data.forEach((employee) => {
        const education = employee.education;
        const personal = employee.personalInformation;
        const fullName = `${personal.firstName || ""} ${
            personal.middleName || ""
        } ${personal.surname || ""}`.trim();

        // Create a single row for the employee
        const row = document.createElement("tr");
        row.innerHTML = `
            <td><input type="text" value="${fullName}" readonly></td>

            <td><input type="text" value="${
                education.elementary?.nameOfSchool || ""
            }"></td>
            <td><input type="text" value="${
                education.elementary?.degreeCourse || ""
            }"></td>
            <td><input type="text" value="${
                education.elementary?.periodFrom || ""
            }"></td>
            <td><input type="text" value="${
                education.elementary?.periodTo || ""
            }"></td>
            <td><input type="text" value="${
                education.elementary?.highestLevelUnits || ""
            }"></td>
            <td><input type="text" value="${
                education.elementary?.yearGraduated || ""
            }"></td>
            <td><input type="text" value="${
                education.elementary?.scholarshipsHonors || ""
            }"></td>

            <td><input type="text" value="${
                education.secondary?.nameOfSchool || ""
            }"></td>
            <td><input type="text" value="${
                education.secondary?.degreeCourse || ""
            }"></td>
            <td><input type="text" value="${
                education.secondary?.periodFrom || ""
            }"></td>
            <td><input type="text" value="${
                education.secondary?.periodTo || ""
            }"></td>
            <td><input type="text" value="${
                education.secondary?.highestLevelUnits || ""
            }"></td>
            <td><input type="text" value="${
                education.secondary?.yearGraduated || ""
            }"></td>
            <td><input type="text" value="${
                education.secondary?.scholarshipsHonors || ""
            }"></td>

            <td><input type="text" value="${
                education.vocational?.nameOfSchool || ""
            }"></td>
            <td><input type="text" value="${
                education.vocational?.degreeCourse || ""
            }"></td>
            <td><input type="text" value="${
                education.vocational?.periodFrom || ""
            }"></td>
            <td><input type="text" value="${
                education.vocational?.periodTo || ""
            }"></td>
            <td><input type="text" value="${
                education.vocational?.highestLevelUnits || ""
            }"></td>
            <td><input type="text" value="${
                education.vocational?.yearGraduated || ""
            }"></td>
            <td><input type="text" value="${
                education.vocational?.scholarshipsHonors || ""
            }"></td>

            <td><input type="text" value="${
                education.college?.nameOfSchool || ""
            }"></td>
            <td><input type="text" value="${
                education.college?.degreeCourse || ""
            }"></td>
            <td><input type="text" value="${
                education.college?.periodFrom || ""
            }"></td>
            <td><input type="text" value="${
                education.college?.periodTo || ""
            }"></td>
            <td><input type="text" value="${
                education.college?.highestLevelUnits || ""
            }"></td>
            <td><input type="text" value="${
                education.college?.yearGraduated || ""
            }"></td>
            <td><input type="text" value="${
                education.college?.scholarshipsHonors || ""
            }"></td>

            <td><input type="text" value="${
                education.graduateStudies?.nameOfSchool || ""
            }"></td>
            <td><input type="text" value="${
                education.graduateStudies?.degreeCourse || ""
            }"></td>
            <td><input type="text" value="${
                education.graduateStudies?.periodFrom || ""
            }"></td>
            <td><input type="text" value="${
                education.graduateStudies?.periodTo || ""
            }"></td>
            <td><input type="text" value="${
                education.graduateStudies?.highestLevelUnits || ""
            }"></td>
            <td><input type="text" value="${
                education.graduateStudies?.yearGraduated || ""
            }"></td>
            <td><input type="text" value="${
                education.graduateStudies?.scholarshipsHonors || ""
            }"></td>
        `;
        tbody.appendChild(row);
    });
}

// Render Civil Service Table
function renderCivilServiceTable(data, targetId) {
    const tbody = document.getElementById(targetId);
    tbody.innerHTML = "";

    data.forEach((employee) => {
        const civilService = employee.civilServiceEligibility;
        const personal = employee.personalInformation;
        const fullName = `${personal.firstName || ""} ${
            personal.middleName || ""
        } ${personal.surname || ""}`.trim();

        civilService.forEach((eligibility) => {
            const row = document.createElement("tr");
            row.innerHTML = `
                <td><input type="text" value="${fullName}" readonly></td>
                <td><input type="text" value="${eligibility.name || ""}"></td>
                <td><input type="text" value="${eligibility.rating || ""}"></td>
                <td><input type="text" value="${
                    eligibility.dateOfExamination || ""
                }"></td>
                <td><input type="text" value="${
                    eligibility.placeOfExamination || ""
                }"></td>
                <td><input type="text" value="${
                    eligibility.licenseNumber || ""
                }"></td>
                <td><input type="text" value="${
                    eligibility.dateOfValidity || ""
                }"></td>
            `;
            tbody.appendChild(row);
        });
    });
}

// Render Work Experience Table
function renderWorkExperienceTable(data, targetId) {
    const tbody = document.getElementById(targetId);
    tbody.innerHTML = ""; // Clear existing content

    data.forEach((employee) => {
        // Ensure work is always an array
        const work = Array.isArray(employee.workExperience)
            ? employee.workExperience
            : [employee.workExperience];
        const personal = employee.personalInformation;
        const fullName = `${personal.firstName || ""} ${
            personal.middleName || ""
        } ${personal.surname || ""}`.trim();

        work.forEach((experience) => {
            const row = document.createElement("tr");
            row.innerHTML = `
                <td><input type="text" value="${fullName}" readonly></td>
                <td><input type="text" value="${
                    experience.inclusiveDates?.from || ""
                }"></td>
                <td><input type="text" value="${
                    experience.inclusiveDates?.to || ""
                }"></td>
                <td><input type="text" value="${
                    experience.positionTitle || ""
                }"></td>
                <td><input type="text" value="${
                    experience.departmentAgency || ""
                }"></td>
                <td><input type="text" value="${
                    experience.monthlySalary || ""
                }"></td>
                <td><input type="text" value="${
                    experience.salaryJobPayGrade || ""
                }"></td>
                <td><input type="text" value="${
                    experience.stepIncrement || ""
                }"></td>
                <td><input type="text" value="${
                    experience.statusOfAppointment || ""
                }"></td>
                <td><input type="text" value="${
                    experience.govtService || ""
                }"></td>
            `;
            tbody.appendChild(row);
        });
    });
}

// Extract Personal Information Data
function extractDataFromSheet(sheet, startRow) {
    const extractedData = [];
    let row = startRow;

    while (true) {
        const firstName = sheet[`C${row}`] ? sheet[`C${row}`].v : "";
        if (!firstName) break;

        extractedData.push(extractRowData(sheet, row));
        row++;
    }
    return extractedData;
}

// Extract Row Data for Personal Information
function extractRowData(sheet, row) {
    return {
        surname: sheet[`B${row}`]?.v || "",
        firstName: sheet[`C${row}`]?.v || "",
        middleName: sheet[`D${row}`]?.v || "",
        dateOfBirth: sheet[`E${row}`]?.v || "",
        placeOfBirth: sheet[`F${row}`]?.v || "",
        sex: sheet[`G${row}`]?.v || "",
        civilStatus: sheet[`H${row}`]?.v || "",
        height: sheet[`I${row}`]?.v || "",
        weight: sheet[`J${row}`]?.v || "",
        bloodType: sheet[`K${row}`]?.v || "",
        gsisID: sheet[`L${row}`]?.v || "",
        pagibigID: sheet[`M${row}`]?.v || "",
        philHealthNo: sheet[`N${row}`]?.v || "",
        sssNo: sheet[`O${row}`]?.v || "",
        tinNo: sheet[`P${row}`]?.v || "",
        agencyEmployeeNo: sheet[`Q${row}`]?.v || "",
        filipino: sheet[`R${row}`]?.v || "",
        dualCitizenship: sheet[`S${row}`]?.v || "",
        dualCitizenshipCountry: sheet[`T${row}`]?.v || "",
        residentialAddress: {
            houseBlockLotNo: sheet[`W${row}`]?.v || "",
            street: sheet[`X${row}`]?.v || "",
            subdivisionVillage: sheet[`Y${row}`]?.v || "",
            barangay: sheet[`Z${row}`]?.v || "",
            cityMunicipality: sheet[`AA${row}`]?.v || "",
            province: sheet[`AB${row}`]?.v || "",
        },
        permanentAddress: {
            houseBlockLotNo: sheet[`AD${row}`]?.v || "",
            street: sheet[`AE${row}`]?.v || "",
            subdivisionVillage: sheet[`AF${row}`]?.v || "",
            barangay: sheet[`AG${row}`]?.v || "",
            cityMunicipality: sheet[`AH${row}`]?.v || "",
            province: sheet[`AI${row}`]?.v || "",
        },
        telephoneNo: sheet[`AJ${row}`]?.v || "",
        mobileNo: sheet[`AK${row}`]?.v || "",
        emailAddress: sheet[`AL${row}`]?.v || "",
    };
}

// Extract Family Background Data
function extractFamilyData(sheet, startRow) {
    const extractedData = [];
    let row = startRow;

    while (true) {
        const spouseSurname = sheet[`B${row}`]?.v || "";
        if (!spouseSurname) break;

        extractedData.push(extractFamilyRowData(sheet, row));
        row++;
    }
    return extractedData;
}

// Extract Row Data for Family Background
function extractFamilyRowData(sheet, row) {
    return {
        spouse: {
            surname: sheet[`B${row}`]?.v || "",
            firstName: sheet[`C${row}`]?.v || "",
            middleName: sheet[`D${row}`]?.v || "",
            nameExtension: sheet[`E${row}`]?.v || "",
            occupation: sheet[`F${row}`]?.v || "",
            employerBusinessName: sheet[`G${row}`]?.v || "",
            businessAddress: sheet[`H${row}`]?.v || "",
            telephoneNo: sheet[`I${row}`]?.v || "",
        },
        children: parseChildrenData(sheet[`J${row}`]?.v, sheet[`K${row}`]?.v), // Ensure children is an array
        father: {
            surname: sheet[`L${row}`]?.v || "",
            firstName: sheet[`M${row}`]?.v || "",
            middleName: sheet[`N${row}`]?.v || "",
            nameExtension: sheet[`O${row}`]?.v || "",
        },
        mother: {
            surname: sheet[`P${row}`]?.v || "",
            firstName: sheet[`Q${row}`]?.v || "",
            middleName: sheet[`R${row}`]?.v || "",
        },
    };
}

// Helper function to parse children data
function parseChildrenData(childrenNames, childrenBirthdates) {
    if (!childrenNames || !childrenBirthdates) return []; // Return empty array if no data

    const names = childrenNames.split(",").map((name) => name.trim());
    const birthdates = childrenBirthdates.split(",").map((date) => date.trim());

    return names.map((name, index) => ({
        name: name || "",
        birthdate: birthdates[index] || "",
    }));
}

// Extract Educational Background Data
function extractEducationalData(sheet, startRow) {
    const extractedData = [];
    let row = startRow;

    while (true) {
        const elementarySchool = sheet[`B${row}`]?.v || "";
        if (!elementarySchool) break;

        extractedData.push({
            elementary: extractEducationLevel(sheet, row, "B"),
            secondary: extractEducationLevel(sheet, row, "I"),
            vocational: extractEducationLevel(sheet, row, "P"),
            college: extractEducationLevel(sheet, row, "W"),
            graduateStudies: extractEducationLevel(sheet, row, "AD"),
        });
        row++;
    }
    return extractedData;
}

// Extract Education Level Data
function extractEducationLevel(sheet, row, startColumn) {
    const colOffset = XLSX.utils.decode_col(startColumn);

    return {
        nameOfSchool: sheet[XLSX.utils.encode_col(colOffset) + row]?.v || "",
        degreeCourse:
            sheet[XLSX.utils.encode_col(colOffset + 1) + row]?.v || "",
        periodFrom: sheet[XLSX.utils.encode_col(colOffset + 2) + row]?.v || "",
        periodTo: sheet[XLSX.utils.encode_col(colOffset + 3) + row]?.v || "",
        highestLevelUnits:
            sheet[XLSX.utils.encode_col(colOffset + 4) + row]?.v || "",
        yearGraduated:
            sheet[XLSX.utils.encode_col(colOffset + 5) + row]?.v || "",
        scholarshipsHonors:
            sheet[XLSX.utils.encode_col(colOffset + 6) + row]?.v || "",
    };
}

// Extract Work Experience Data
function extractWorkExperienceData(sheet, startRow) {
    const extractedData = [];
    let row = startRow;

    while (true) {
        const fromDate = sheet[`B${row}`]?.v || "";
        if (!fromDate) break;

        extractedData.push({
            inclusiveDates: {
                from: fromDate,
                to: sheet[`C${row}`]?.v || "",
            },
            positionTitle: sheet[`D${row}`]?.v || "",
            departmentAgency: sheet[`E${row}`]?.v || "",
            monthlySalary: sheet[`F${row}`]?.v || "",
            salaryJobPayGrade: sheet[`G${row}`]?.v || "",
            stepIncrement: sheet[`H${row}`]?.v || "",
            statusOfAppointment: sheet[`I${row}`]?.v || "",
            govtService: sheet[`J${row}`]?.v || "",
        });
        row++;
    }

    return extractedData; // Always returns an array
}

// Extract Civil Service Eligibility Data
function extractCivilServiceData(sheet, startRow) {
    const extractedData = [];
    let row = startRow;

    while (true) {
        const eligibilityCell = sheet[`B${row}`];
        if (!eligibilityCell || !eligibilityCell.v) break;

        const eligibilityNames = String(eligibilityCell.v)
            .split(",")
            .map((name) => name.trim());
        const ratings = sheet[`E${row}`]?.v
            ? String(sheet[`E${row}`].v)
                  .split(",")
                  .map((r) => r.trim())
            : [];
        const examDates = sheet[`F${row}`]?.v
            ? String(sheet[`F${row}`].v)
                  .split(",")
                  .map((d) => d.trim())
            : [];
        const examPlaces = sheet[`G${row}`]?.v
            ? String(sheet[`G${row}`].v)
                  .split(",")
                  .map((p) => p.trim())
            : [];
        const licenseNumbers = sheet[`H${row}`]?.v
            ? String(sheet[`H${row}`].v)
                  .split(",")
                  .map((n) => n.trim())
            : [];
        const validityDates = sheet[`I${row}`]?.v
            ? String(sheet[`I${row}`].v)
                  .split(",")
                  .map((d) => d.trim())
            : [];

        const eligibilities = eligibilityNames.map((name, index) => ({
            name: name || "",
            rating: ratings[index] || "",
            dateOfExamination: examDates[index] || "",
            placeOfExamination: examPlaces[index] || "",
            licenseNumber: licenseNumbers[index] || "",
            dateOfValidity: validityDates[index] || "",
        }));

        extractedData.push(eligibilities);
        row++;
    }

    return extractedData;
}
