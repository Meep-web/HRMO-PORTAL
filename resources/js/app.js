import "./bootstrap";
import { PDFDocument, rgb } from "pdf-lib";

document.addEventListener("DOMContentLoaded", function () {
    // Get all sidebar buttons
    const sidebarButtons = document.querySelectorAll(
        ".nosa-button, .service-records-button, .leave-credits-button, .account-management-button"
    );

    // Loop through each button and add an event listener
    sidebarButtons.forEach(function (button) {
        button.addEventListener("click", function () {
            // Get the title from the data-title attribute
            const title = button.getAttribute("data-title");

            // Update the page title
            document.getElementById("page-title").innerText = title;

            // Optionally, you could update the <title> tag as well for the browser tab
            document.title = title;
        });
    });

    // Modal functionality
    const modal = document.getElementById("uploadModal");
    const uploadButton = document.getElementById("uploadButton");
    const closeButton = document.querySelector(".close");

    // Open the modal when the upload button is clicked
    if (uploadButton) {
        uploadButton.addEventListener("click", () => {
            modal.style.display = "block";
        });
    }

    // Close the modal when the close button is clicked
    if (closeButton) {
        closeButton.addEventListener("click", () => {
            modal.style.display = "none";
        });
    }

    // Close the modal when clicking outside the modal content
    window.addEventListener("click", (event) => {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    });

    // Add Another Row functionality
    const addAnotherRowButton = document.getElementById("addAnotherRow");
    if (addAnotherRowButton) {
        addAnotherRowButton.addEventListener("click", function (event) {
            event.preventDefault(); // Prevent form submission

            // Get the table body
            const tableBody = document.querySelector(
                "#salaryAdjustmentTable tbody"
            );

            // Create a new row
            const newRow = document.createElement("tr");

            // Add cells with input fields to the new row
            newRow.innerHTML = `
                <td><input type="text" name="employee_name[]" placeholder="Employee Name" /></td>
                <td><input type="text" name="position[]" placeholder="Position" /></td>
                <td><input type="text" name="department[]" placeholder="Department" /></td>
                <td><input type="number" name="previous_salary[]" placeholder="Previous Salary" /></td>
                <td><input type="number" name="new_salary[]" placeholder="New Salary" /></td>
                <td><input type="date" name="date_of_effectivity[]" /></td>
                <td><input type="date" name="date_released[]" /></td>
                <td><input type="number" name="salary_grade[]" placeholder="Salary Grade" /></td>
                <td><input type="number" name="step_increment[]" placeholder="Step Increment" /></td>
            `;

            // Append the new row to the table body
            tableBody.appendChild(newRow);
        });
    }

    // Save Button functionality
    const saveButton = document.getElementById("saveButton");
    if (saveButton) {
        saveButton.addEventListener("click", function (event) {
            event.preventDefault(); // Prevent form submission

            // Get all rows from the table
            const rows = document.querySelectorAll(
                "#salaryAdjustmentTable tbody tr"
            );

            // Create an array to store the data
            const tableData = [];

            // Validate data and collect non-empty rows
            let isValid = true;
            rows.forEach((row) => {
                const inputs = row.querySelectorAll("input");
                let isRowEmpty = true; // Initialize as true

                inputs.forEach((input) => {
                    if (!input.value.trim()) {
                        isValid = false;
                        input.style.border = "1px solid red"; // Highlight empty fields
                    } else {
                        input.style.border = ""; // Reset border
                        isRowEmpty = false; // If any input has a value, the row is not empty
                    }
                });

                // Only add the row data if the row is not empty
                if (!isRowEmpty) {
                    const rowData = {
                        employeeName: inputs[0].value,
                        position: inputs[1].value,
                        department: inputs[2].value,
                        previousSalary: inputs[3].value,
                        newSalary: inputs[4].value,
                        dateOfEffectivity: inputs[5].value,
                        dateReleased: inputs[6].value,
                        salaryGrade: inputs[7].value,
                        stepIncrement: inputs[8].value,
                    };
                    tableData.push(rowData);
                }
            });

            if (!isValid) {
                alert("Please fill out all fields before saving."); // Simple alert for validation
                return; // Stop the function if validation fails
            }

            // Get the current employeeName from the session (passed from Laravel)
            const currentEmployeeName = document.getElementById(
                "currentEmployeeName"
            ).value;

            // Get the route URL from the hidden input
            const saveNosaRoute =
                document.getElementById("saveNosaRoute").value;

            // Get the CSRF token from the meta tag
            const csrfToken = document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content");

            // Prepare the data to send
            const dataToSave = {
                currentEmployeeName: currentEmployeeName,
                salaryAdjustments: tableData,
            };

            // Disable the save button to prevent multiple submissions
            saveButton.disabled = true;

            // Send the data to the backend using AJAX
            fetch(saveNosaRoute, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken, // Include the CSRF token
                },
                body: JSON.stringify(dataToSave),
            })
                .then((response) => {
                    if (!response.ok) {
                        throw new Error(
                            "Network response was not ok: " +
                                response.statusText
                        );
                    }
                    return response.json();
                })
                .then((data) => {
                    if (data.success) {
                        alert("Data saved successfully!"); // Simple success message
                        window.location.reload(); // Reload the page
                    } else {
                        alert("Failed to save data: " + data.message); // Simple error message
                    }
                })
                .catch((error) => {
                    console.error("Error:", error);
                    alert("An error occurred while saving data."); // Simple error message
                })
                .finally(() => {
                    // Re-enable the save button
                    saveButton.disabled = false;
                });
        });
    }
    // View Button functionality
    const viewButtons = document.querySelectorAll(".view-button");
    const departmentSpecificModal = document.getElementById(
        "DepartmentSpecificModal"
    );
    const closeDepartmentSpecificModalButton = document.querySelector(
        ".close-department-specific-modal"
    );

    // Open the department-specific modal and fetch data when a view button is clicked
    viewButtons.forEach((button) => {
        button.addEventListener("click", function () {
            const department = button.getAttribute("data-department");

            // Fetch data from the backend
            fetch(`/get-nosa-data?department=${department}`)
                .then((response) => response.json())
                .then((data) => {
                    // Clear the table body
                    const tableBody = document.querySelector(
                        "#employeeDataTable tbody"
                    );
                    tableBody.innerHTML = "";

                    // Populate the table with fetched data
                    data.forEach((item) => {
                        const row = document.createElement("tr");
                        row.innerHTML = `
                             <td>${item.employeeName}</td>
                             <td>${item.position}</td>
                             <td>${item.dateReleased}</td>
                             <td>${item.userName}</td>
                             <td>
                                 <button class="generate-button" data-employee-id="${item.id}">Generate</button>
                             </td>
                         `;
                        tableBody.appendChild(row);
                    });

                    // Open the modal
                    departmentSpecificModal.style.display = "block";
                })
                .catch((error) => {
                    console.error("Error fetching data:", error);
                    alert("Failed to fetch data. Please try again.");
                });
        });
    });

    // Close the department-specific modal when the close button is clicked
    if (closeDepartmentSpecificModalButton) {
        closeDepartmentSpecificModalButton.addEventListener("click", () => {
            departmentSpecificModal.style.display = "none";
        });
    }

    // Close the department-specific modal when clicking outside the modal content
    window.addEventListener("click", (event) => {
        if (event.target === departmentSpecificModal) {
            departmentSpecificModal.style.display = "none";
        }
    });

    // Close the PDF modal when the close button is clicked
    const closePdfModalButton = document.querySelector(".close-pdf-modal");
    if (closePdfModalButton) {
        closePdfModalButton.addEventListener("click", () => {
            const pdfModal = document.getElementById("pdfModal");
            pdfModal.style.display = "none";
        });
    }

    // Close the PDF modal when clicking outside the modal content
    window.addEventListener("click", (event) => {
        const pdfModal = document.getElementById("pdfModal");
        if (event.target === pdfModal) {
            pdfModal.style.display = "none";
        }
    });

    async function addTextToPDF(pdfUrl, textData, pageIndex = 0) {
        // Fetch the existing PDF
        const existingPdfBytes = await fetch(pdfUrl).then((res) =>
            res.arrayBuffer()
        );

        // Load the PDF document
        const pdfDoc = await PDFDocument.load(existingPdfBytes);

        // Get the first page (or specify the page index)
        const pages = pdfDoc.getPages();
        const page = pages[pageIndex];

        // Get the page dimensions
        const { width, height } = page.getSize();

        // Loop through the textData array and add each text to the page
        textData.forEach(({ text, x, y, size = 12, color = rgb(0, 0, 0) }) => {
            page.drawText(text, {
                x: x, // X coordinate (from the left)
                y: height - y, // Y coordinate (from the bottom)
                size: size, // Font size
                color: color, // Text color
            });
        });

        // Save the modified PDF
        const modifiedPdfBytes = await pdfDoc.save();

        // Create a Blob and URL for the modified PDF
        const blob = new Blob([modifiedPdfBytes], { type: "application/pdf" });
        const url = URL.createObjectURL(blob);

        return url;
    }

    // Attach the functionality to the Generate button
    document.addEventListener("click", async function (event) {
        if (
            event.target &&
            event.target.classList.contains("generate-button")
        ) {
            const employeeName='Employee Name';
            const salaryGrade='1';
            const stepIncrement='1';
            // Example text data to add to the PDF
            const textData = [
                {
                    text: "Date",
                    x: 400,
                    y: 190,
                    size: 12,
                    color: rgb(0, 0, 0),
                }, 
                {
                    text: employeeName,
                    x: 114,
                    y: 225,
                    size: 12,
                    color: rgb(0, 0, 0),
                }, 
                {
                    text: "Position",
                    x: 114,
                    y: 237,
                    size: 12,
                    color: rgb(0, 0, 0),
                }, 
                {
                    text: "Department",
                    x: 114,
                    y: 248,
                    size: 12,
                    color: rgb(0, 0, 0),
                }, 
                {
                    text: 'Mr/Mrs:'+employeeName,
                    x: 114,
                    y: 282,
                    size: 12,
                    color: rgb(0, 0, 0),
                }, 
                {
                    text: '115',
                    x: 327,
                    y: 304,
                    size: 10,
                    color: rgb(0, 0, 0),
                }, 
                {
                    text: 'January 3, 2018',
                    x: 390,
                    y: 304,
                    size: 10,
                    color: rgb(0, 0, 0),
                }, 
                {
                    text: 'Date Effective',
                    x: 193,
                    y: 327,
                    size: 10,
                    color: rgb(0, 0, 0),
                },
                {
                    text: 'Date Effective',
                    x: 303,
                    y: 347,
                    size: 10,
                    color: rgb(0, 0, 0),
                }, 
                {
                    text: salaryGrade,
                    x: 268,
                    y: 360,
                    size: 10,
                    color: rgb(0, 0, 0),
                },  
                {
                    text: stepIncrement,
                    x: 316,
                    y: 360,
                    size: 10,
                    color: rgb(0, 0, 0),
                }, 
                {
                    text: 'New Salary',
                    x: 443,
                    y: 360,
                    size: 10,
                    color: rgb(0, 0, 0),
                },
                {
                    text: 'Previous date',
                    x: 279,
                    y: 393,
                    size: 10,
                    color: rgb(0, 0, 0),
                },
                {
                    text: salaryGrade,
                    x: 152,
                    y: 407,
                    size: 10,
                    color: rgb(0, 0, 0),
                }, 
                {
                    text: stepIncrement,
                    x: 197,
                    y: 407,
                    size: 10,
                    color: rgb(0, 0, 0),
                }, 
                {
                    text: 'Current Salary',
                    x: 443,
                    y: 405,
                    size: 10,
                    color: rgb(0, 0, 0),
                },
                {
                    text: 'Date Effective',
                    x: 290,
                    y: 439,
                    size: 10,
                    color: rgb(0, 0, 0),
                }, 
                {
                    text: 'Difference',
                    x: 443,
                    y: 439,
                    size: 10,
                    color: rgb(0, 0, 0),
                },
                {
                    text: 'Position',
                    x: 180,
                    y: 598,
                    size: 10,
                    color: rgb(0, 0, 0),
                },
                {
                    text: salaryGrade,
                    x: 180,
                    y: 609,
                    size: 10,
                    color: rgb(0, 0, 0),
                },

            ];

            // Path to the original PDF
            const pdfUrl = "/docs/NOSA.pdf";

            // Add text to the PDF and display it
            addTextToPDF(pdfUrl, textData)
                .then((modifiedPdfUrl) => {
                    // Display the modified PDF in the iframe
                    const pdfIframe = document.getElementById("pdfIframe");
                    pdfIframe.setAttribute("src", modifiedPdfUrl);

                    // Open the PDF modal
                    const pdfModal = document.getElementById("pdfModal");
                    pdfModal.style.display = "block";
                })
                .catch((error) => {
                    console.error("Error modifying PDF:", error);
                    alert("Failed to generate the PDF. Please try again.");
                });
        }
    });
});
