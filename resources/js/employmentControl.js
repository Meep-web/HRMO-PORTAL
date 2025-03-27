import Swal from "sweetalert2";
document.addEventListener("DOMContentLoaded", function () {
    const modal = document.getElementById("assignEmploymentModal");
    const closeModal = document.querySelector(".close-modal");
    const assignButtons = document.querySelectorAll(".assign-employment-button");
    const assignForm = document.getElementById("assignEmploymentForm");

    

    // Open modal when "Assign" button is clicked
assignButtons.forEach(button => {
    button.addEventListener("click", function () {
        const employeeId = this.getAttribute("data-id");
        const employeeName = this.getAttribute("data-name");
        const department = this.getAttribute("data-department") || "";
        const salaryGrade = this.getAttribute("data-salary-grade") || "";
        const stepIncrement = this.getAttribute("data-step-increment") || "";

        // Populate fields in modal
        document.getElementById("employeeId").value = employeeId;
        document.getElementById("employeeName").value = employeeName;
        
        // Set dropdown values (ensure they exist)
        document.getElementById("department").value = department;
        document.getElementById("salaryGrade").value = salaryGrade;
        document.getElementById("stepIncrement").value = stepIncrement;

        modal.style.display = "block";
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
    
        const formData = new FormData(assignForm);
        formData.append("employeeId", document.getElementById("employeeId").value);
        formData.append("department", document.getElementById("department").value);
        formData.append("salaryGrade", document.getElementById("salaryGrade").value);
        formData.append("stepIncrement", document.getElementById("stepIncrement").value);
    
        fetch("/assign-employment", {
            method: "POST",
            body: formData,
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire("Success", "Employment assigned successfully!", "success");
                modal.style.display = "none";
                location.reload();
            } else {
                Swal.fire("Error", data.message, "error");
            }
        })
        .catch(error => console.error("Error:", error));
    });
    

    
});
