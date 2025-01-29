import "./bootstrap";

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

    // Generate Button functionality
    document.addEventListener("click", function (event) {
        if (
            event.target &&
            event.target.classList.contains("generate-button")
        ) {
            const employeeId = event.target.getAttribute("data-employee-id");

            // Construct the URL to the NOSA.pdf file
            const pdfUrl = `/docs/NOSA.pdf`; // Adjust the path if necessary

            // Set the iframe source to the PDF URL
            const pdfIframe = document.getElementById("pdfIframe");
            pdfIframe.setAttribute("src", pdfUrl);

            // Open the PDF modal
            const pdfModal = document.getElementById("pdfModal");
            pdfModal.style.display = "block";
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
});
