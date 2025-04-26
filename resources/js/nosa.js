import { PDFDocument, rgb, StandardFonts } from "pdf-lib";

// Function to fetch salary changes and department data
function fetchSalaryChanges(employeeId) {
    return fetch(`/show-salary-changes`, {
        method: 'POST',
        body: new URLSearchParams({
            'employeeId': employeeId
        }),
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .catch(error => {
        console.error("Error fetching salary changes:", error);
        throw error;
    });
}

// Function to render salary change history to the modal
function renderSalaryHistory(data) {
    const modal = document.getElementById("updateHistoryModal");
    const historyContainer = document.getElementById("updateHistoryData");

    if (data.employeeChanges && data.employeeChanges.length > 0) {
        let historyContent = '';

        // Sort the employee changes by timestamp (most recent first)
        data.employeeChanges.sort((a, b) => {
            const timestampA = new Date(a.timestamp);
            const timestampB = new Date(b.timestamp);
            return timestampB - timestampA; // Sort descending
        });

        // Loop through each employee change record
        data.employeeChanges.forEach(changeRecord => {
            const { department_id, dateOfEffectivity, dateReleased, salaryGrade, stepIncrement, position, date_hired, timestamp } = changeRecord.changes;

            // Get department name from the departments data
            let departmentName = '';
            if (data.departments) {
                const department = data.departments.find(department => department.id == department_id.new);
                if (department) {
                    departmentName = department.department_name;
                }
            }

            // Add the specific fields to the table content with a placeholder action button
            historyContent += `
                <tr>
                    <td>${departmentName}</td>
                    <td>${dateOfEffectivity && dateOfEffectivity.new ? new Date(dateOfEffectivity.new).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) : 'N/A'}</td>
                    <td>${dateReleased && dateReleased.new ? new Date(dateReleased.new).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) : 'N/A'}</td>

                    <td>
                        <button class="action-btn"
                            data-department-new="${changeRecord.changes.department_id.new}"
                            data-salaryGrade-old="${changeRecord.changes.salaryGrade.old}"
                            data-salaryGrade-new="${changeRecord.changes.salaryGrade.new}"
                            data-stepIncrement-old="${changeRecord.changes.stepIncrement.old}"
                            data-stepIncrement-new="${changeRecord.changes.stepIncrement.new}"
                            data-position-new="${changeRecord.changes.position.new}"
                            data-dateOfEffectivity-new="${changeRecord.changes.dateOfEffectivity.new}"
                            data-dateReleased-new="${changeRecord.changes.dateReleased.new}"
                            data-employeeId="${changeRecord.employee_id}"
                        >
                            Generate PDF
                        </button>
                    </td>
                </tr>
            `;
        });

        // Inject the table content into the modal
        historyContainer.innerHTML = historyContent;

        // Open the modal
        modal.style.display = "block";

        // Add event listeners to the action buttons
        document.querySelectorAll(".action-btn").forEach(button => {
            button.addEventListener("click", handleGeneratePdfClick);
        });

    } else {
        historyContainer.innerHTML = '<tr><td colspan="4">No salary changes found for this employee.</td></tr>';
    }
}

// Function to handle clicking the "Generate PDF" button
async function handleGeneratePdfClick(event) {
    const button = event.target;

    const departmentNew = button.getAttribute("data-department-new");
    const salaryGradeOld = button.getAttribute("data-salaryGrade-old");
    const salaryGradeNew = button.getAttribute("data-salaryGrade-new");
    const stepIncrementOld = button.getAttribute("data-stepIncrement-old");
    const stepIncrementNew = button.getAttribute("data-stepIncrement-new");
    const positionNew = button.getAttribute("data-position-new");
    const dateOfEffectivityNew = button.getAttribute("data-dateOfEffectivity-new");
    const dateReleasedNew = button.getAttribute("data-dateReleased-new");
    const employeeId = button.getAttribute("data-employeeId");

    const dataToSend = {
        department_id: departmentNew,
        salaryGrade: { old: salaryGradeOld, new: salaryGradeNew },
        stepIncrement: { old: stepIncrementOld, new: stepIncrementNew },
        position: { new: positionNew },
        dateOfEffectivity: { new: dateOfEffectivityNew },
        dateReleased: { new: dateReleasedNew },
        employee_id: employeeId,
    };

    try {
        const response = await fetch("/refactor_data", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify(dataToSend),
        });

        const employeeData = await response.json();

        

        const url = "/docs/NOSA.pdf";
        const existingPdfBytes = await fetch(url).then((res) => res.arrayBuffer());
        const pdfDoc = await PDFDocument.load(existingPdfBytes);
        const page = pdfDoc.getPages()[0];
        const pageHeight = page.getHeight();

        const difference = parseFloat(employeeData.newSalary) - parseFloat(employeeData.oldSalary);

        const textData = [
            { text: new Date(employeeData.dateReleased).toLocaleDateString("en-US", { year: "numeric", month: "long", day: "numeric" }), x: 400, y: 190, size: 10 },
            { text: employeeData.employeeName, x: 114, y: 225, size: 12 },
            { text: employeeData.designation, x: 114, y: 237, size: 12 },
            { text: employeeData.department, x: 114, y: 248, size: 12 },
            { text: `Mr/Mrs: ${employeeData.employeeName}`, x: 114, y: 282, size: 12 },
            { text: "115", x: 327, y: 304, size: 10 },
            { text: new Date("January 3, 2018").toLocaleDateString("en-US", { year: "numeric", month: "long", day: "numeric" }), x: 390, y: 304, size: 10 },
            { text: new Date(employeeData.dateOfEffectivity).toLocaleDateString("en-US", { year: "numeric", month: "long", day: "numeric" }), x: 193, y: 327, size: 9 },
            { text: new Date(employeeData.dateOfEffectivity).toLocaleDateString("en-US", { year: "numeric", month: "long", day: "numeric" }), x: 303, y: 347, size: 8 },
            { text: salaryGradeNew.toString(), x: 268, y: 360, size: 10 },
            { text: stepIncrementNew.toString(), x: 316, y: 360, size: 10 },
            { text: parseFloat(employeeData.newSalary).toLocaleString("en-US", { minimumFractionDigits: 2, maximumFractionDigits: 2 }), x: 443, y: 360, size: 10 },
            { text: "December 31, 2024", x: 279, y: 393, size: 8 },
            { text: salaryGradeOld.toString(), x: 152, y: 407, size: 10 },
            { text: stepIncrementOld.toString(), x: 197, y: 407, size: 10 },
            { text: parseFloat(employeeData.oldSalary).toLocaleString("en-US", { minimumFractionDigits: 2, maximumFractionDigits: 2 }), x: 443, y: 405, size: 10 },
            { text: new Date(employeeData.dateOfEffectivity).toLocaleDateString("en-US", { year: "numeric", month: "long", day: "numeric" }), x: 290, y: 439, size: 8 },
            { text: difference.toLocaleString("en-US", { minimumFractionDigits: 2, maximumFractionDigits: 2 }), x: 443, y: 439, size: 10 },
            { text: employeeData.designation, x: 180, y: 598, size: 10 },
            { text: "CESAR V. AREZA", x: 388, y: 562, size: 10 },
            { text: salaryGradeNew.toString(), x: 180, y: 609, size: 10 }
        ];

        // Invert Y-coordinates
        const adjustedTextData = textData.map(item => ({
            ...item,
            y: pageHeight - item.y
        }));

        for (const { text, x, y, size } of adjustedTextData) {
            page.drawText(text, {
                x,
                y,
                size,
                color: rgb(0, 0, 0),
            });
        }

        const pdfBytes = await pdfDoc.save();
        const blob = new Blob([pdfBytes], { type: "application/pdf" });
        const blobUrl = URL.createObjectURL(blob);

        const modal = document.getElementById("pdfModal");
        const iframe = document.getElementById("pdfIframe");
        iframe.src = blobUrl;
        modal.style.display = "block";

        const closeBtn = document.querySelector(".close-pdf-modal");
        closeBtn.removeEventListener("click", closeModal);
        closeBtn.addEventListener("click", closeModal);

        window.addEventListener("click", function (e) {
            if (e.target === modal) closeModal();
        });

        function closeModal() {
            modal.style.display = "none";
            iframe.src = "";
            URL.revokeObjectURL(blobUrl);
        }
    } catch (error) {
        console.error("Error generating PDF:", error);
    }
}

// Initialize event listeners for the "Show Salary Changes" buttons
document.addEventListener("DOMContentLoaded", function () {
    const modal = document.getElementById("updateHistoryModal");
    const closeBtn = document.getElementById("closeUpdateHistory");

    // When any "Show Salary Changes" button is clicked
    document.querySelectorAll(".show-salary-changes-button").forEach(button => {
        button.addEventListener("click", function () {
            const employeeId = this.getAttribute("data-id");

            // Fetch salary change history and department data
            fetchSalaryChanges(employeeId)
                .then(data => renderSalaryHistory(data))
                .catch(error => {
                    console.error("Error:", error);
                });
        });
    });

    // Close modal
    closeBtn.addEventListener("click", function () {
        modal.style.display = "none";
    });

    // Close if clicked outside the modal
    window.addEventListener("click", function (e) {
        if (e.target === modal) {
            modal.style.display = "none";
        }
    });
});
