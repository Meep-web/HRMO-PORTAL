import Swal from "sweetalert2";

// Format date to readable string
function formatDate(dateString) {
    const options = { year: "numeric", month: "long", day: "numeric" };
    const date = new Date(dateString);
    return date.toLocaleDateString("en-US", options);
}

// Modal functions
function openUseBalanceModal(
    employeeId,
    vacationLeave,
    sickLeave,
    fullName,
    dateHired
) {
    const modal = document.getElementById("useBalanceModal");
    document.getElementById("employeeIdText").innerText = employeeId;
    document.getElementById("employeeNameText").innerText = fullName;
    document.getElementById("vacationLeaveText").innerText =
        Math.trunc(vacationLeave);
    document.getElementById("sickLeaveText").innerText =
        Math.trunc(sickLeave);

    const formattedDate = formatDate(dateHired);
    document.getElementById("dateHiredText").innerText = formattedDate;

    const yearSelect = document.getElementById("leaveYear");
    yearSelect.innerHTML = "";
    const hireYear = new Date(dateHired).getFullYear();
    const currentYear = new Date().getFullYear();

    for (let year = hireYear; year <= currentYear; year++) {
        const option = document.createElement("option");
        option.value = year;
        option.textContent = year;
        yearSelect.appendChild(option);
    }

    // Set current year as default selected
    yearSelect.value = currentYear;

    modal.style.display = "flex";
    window.addEventListener("click", outsideClickHandler);
}


function closeUseBalanceModal() {
    const modal = document.getElementById("useBalanceModal");
    modal.style.display = "none";
    window.removeEventListener("click", outsideClickHandler);
}

function outsideClickHandler(event) {
    const modal = document.getElementById("useBalanceModal");
    if (event.target === modal) {
        closeUseBalanceModal();
    }
}

function submitLeaveUsage() {
    const leaveMonth = document.getElementById("leaveMonth").value;
    const leaveYear = document.getElementById("leaveYear").value;
    const leaveType = document.getElementById("leaveType").value;
    const creditsUsed = document.getElementById("creditsUsed").value;
    const payType = document.getElementById("payType").value;
    const employeeId = document.getElementById("employeeIdText").innerText;
    const dateHired = document.getElementById("dateHiredText").innerText;

    if (!leaveMonth || !leaveYear || !leaveType || !creditsUsed || !payType) {
        alert("Please fill out all fields.");
        return;
    }

    const hireDate = new Date(dateHired);
    const selectedLeaveDate = new Date(
        leaveYear,
        new Date(`${leaveMonth} 1`).getMonth(),
        1
    );

    if (selectedLeaveDate < hireDate || selectedLeaveDate > new Date()) {
        Swal.fire({
            icon: "error",
            title: "Invalid Date",
            text: "The selected date falls outside of the employee's employment period.",
        });
        return;
    }

    // 👇 Check if selected month/year is same as the date hired
    if (
        hireDate.getFullYear() === selectedLeaveDate.getFullYear() &&
        hireDate.getMonth() === selectedLeaveDate.getMonth()
    ) {
        Swal.fire({
            icon: "error",
            title: "Leave Not Allowed",
            text: "You cannot request leave during the month of hiring.",
        });
        return;
    }

    const data = {
        employeeId: employeeId,
        leaveMonth: leaveMonth,
        leaveYear: leaveYear,
        leaveType: leaveType,
        creditsUsed: parseFloat(creditsUsed),
        payType: payType,
    };

    fetch("/save-leave-usage", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
        },
        body: JSON.stringify(data),
    })
        .then((response) => response.json())
        .then((data) => {
            closeUseBalanceModal();
            Swal.fire({
                icon: "success",
                title: "Success",
                text: "Leave usage has been saved successfully!",
                confirmButtonText: "OK",
            }).then(() => {
                // Redirect after user clicks "OK"
                window.location.href = "/leave-credits";
            });
        })

        .catch((error) => {
            console.error("Error:", error);
            alert("An error occurred while saving the data.");
        });
}

// Make functions available globally
window.openUseBalanceModal = openUseBalanceModal;
window.closeUseBalanceModal = closeUseBalanceModal;
window.submitLeaveUsage = submitLeaveUsage;
