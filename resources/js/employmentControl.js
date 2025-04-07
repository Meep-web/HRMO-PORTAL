import Swal from "sweetalert2";
document.addEventListener("DOMContentLoaded", function () {
    const modal = document.getElementById("assignEmploymentModal");
    const closeModal = document.querySelector(".close-modal");
    const assignButtons = document.querySelectorAll(
        ".assign-employment-button"
    );
    const assignForm = document.getElementById("assignEmploymentForm");

   // Open modal when "Assign" button is clicked
assignButtons.forEach(button => {
    button.addEventListener("click", function () {
        const employeeId = this.getAttribute("data-id");
        const employeeName = this.getAttribute("data-name");
        const department = this.getAttribute("data-department") || "";
        const salaryGrade = this.getAttribute("data-salary-grade") || "";
        const stepIncrement = this.getAttribute("data-step-increment") || "";
        const position = this.getAttribute("data-position") || ""; // Add position
        const dateHired = this.getAttribute("data-date-hired") || ""; // Add date_hired
        const dateOfEffectivity = this.getAttribute("data-date-effectivity") || ""; // Add dateOfEffectivity
        const dateReleased = this.getAttribute("data-date-released") || ""; // Add dateReleased

        // Populate fields in modal
        document.getElementById("employeeId").value = employeeId;
        document.getElementById("employeeName").value = employeeName;
        
        // Set department value
        document.getElementById("department").value = department;

        // Trigger change event to load the corresponding positions
        const departmentSelect = document.getElementById("department");
        departmentSelect.dispatchEvent(new Event("change"));

        // Wait for 2 seconds before setting the position field value
        setTimeout(function () {
            // Set other values after delay
            document.getElementById("salaryGrade").value = salaryGrade;
            document.getElementById("stepIncrement").value = stepIncrement;
            document.getElementById("position").value = position; // Set position dropdown value
            document.getElementById("dateHired").value = dateHired;
            document.getElementById("dateOfEffectivity").value = dateOfEffectivity;
            document.getElementById("dateReleased").value = dateReleased;

            modal.style.display = "block";
        }, 500); // 2 seconds delay (2000 milliseconds)
    });
});

    // Close modal when 'X' is clicked
    closeModal.addEventListener("click", function () {
        modal.style.display = "none";
    });

    // Close modal when clicking outside the content
    window.addEventListener("click", function (event) {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    });

    assignForm.addEventListener("submit", function (e) {
        e.preventDefault();
    
        // Gather data from hidden fields (old data from button)
        const oldDepartment = document.getElementById("oldDepartment").value;
        const oldSalaryGrade = document.getElementById("oldSalaryGrade").value;
        const oldStepIncrement = document.getElementById("oldStepIncrement").value;
        const oldPosition = document.getElementById("oldPosition").value;
    
        // Gather data from the form (current modal values)
        const departmentFromForm = document.getElementById("department").value;
        const salaryGradeFromForm = document.getElementById("salaryGrade").value;
        const stepIncrementFromForm = document.getElementById("stepIncrement").value;
        const positionFromForm = document.getElementById("position").value;
    
        // Check if any field has changed (we compare each field individually)
        let hasChanges = false;
    
        if (
            oldDepartment !== departmentFromForm ||
            oldSalaryGrade !== salaryGradeFromForm ||
            oldStepIncrement !== stepIncrementFromForm ||
            oldPosition !== positionFromForm
        ) {
            hasChanges = true;
        }
    
        // If no changes detected, show a message and prevent form submission
        if (!hasChanges) {
            modal.style.display = "none"; // Close the modal
            Swal.fire({
                icon: 'info',
                title: 'No Changes Detected',
                text: 'The data you are trying to update is the same as the existing data. No update will be made.',
            });
            return; // Prevent form submission
        }
    
        // Proceed with form data submission if any changes are detected
        const formData = new FormData(assignForm);
        formData.append("employeeId", document.getElementById("employeeId").value);
        formData.append("department", document.getElementById("department").value);
        formData.append("salaryGrade", document.getElementById("salaryGrade").value);
        formData.append("stepIncrement", document.getElementById("stepIncrement").value);
        formData.append("position", document.getElementById("position").value); // Append position (designation)
    
        // Get date values
        const dateHired = document.getElementById("dateHired").value;
        const dateOfEffectivity = document.getElementById("dateOfEffectivity").value;
        const dateReleased = document.getElementById("dateReleased").value;
    
        // Append date fields (nullable)
        formData.append("dateHired", dateHired || null);
        formData.append("dateOfEffectivity", dateOfEffectivity || null);
        formData.append("dateReleased", dateReleased || null);
    
        // Log form data being sent
        for (let [key, value] of formData.entries()) {
            console.log(`${key}: ${value}`);
        }
    
        // Submit the form data to the server
        fetch("/assign-employment", {
            method: "POST",
            body: formData,
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
            },
        })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                Swal.fire("Success", "Employment assigned successfully!", "success");
                modal.style.display = "none";
                location.reload();
            } else {
                Swal.fire("Error", data.message, "error");
            }
        })
        .catch((error) => console.error("Error:", error));
    });
    
});

// Wait until the document is ready
document.addEventListener("DOMContentLoaded", function () {
    // Listen for change on the department dropdown
    document
        .getElementById("department")
        .addEventListener("change", function () {
            let departmentId = this.value; // Get selected department ID

            if (departmentId) {
                // Make an AJAX request to fetch designations for this department
                fetch(`/get-designations/${departmentId}`)
                    .then((response) => response.json())
                    .then((data) => {
                        // Get the position dropdown
                        const positionDropdown =
                            document.getElementById("position");

                        // Clear existing options
                        positionDropdown.innerHTML =
                            '<option value="" disabled selected>Select a position</option>';

                        // Loop through the fetched designations and add them to the dropdown
                        data.forEach((designation) => {
                            const option = document.createElement("option");
                            option.value = designation.id; // The designation ID
                            option.textContent = designation.designation; // The designation name
                            positionDropdown.appendChild(option);
                        });
                    })
                    .catch((error) =>
                        console.error("Error fetching designations:", error)
                    );
            } else {
                // If no department is selected, reset the position dropdown
                document.getElementById("position").innerHTML =
                    '<option value="" disabled selected>Select a position</option>';
            }
        });
});
