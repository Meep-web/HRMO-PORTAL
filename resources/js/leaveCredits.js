import Swal from 'sweetalert2';

// Format date to readable string
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', options);
}

// Modal functions
function openUseBalanceModal(employeeId, vacationLeave, sickLeave, fullName, dateHired) {
    const modal = document.getElementById('useBalanceModal');
    document.getElementById('employeeIdText').innerText = employeeId;
    document.getElementById('employeeNameText').innerText = fullName;
    document.getElementById('vacationLeaveText').innerText = vacationLeave.toFixed(2);
    document.getElementById('sickLeaveText').innerText = sickLeave.toFixed(2);

    const formattedDate = formatDate(dateHired);
    document.getElementById('dateHiredText').innerText = formattedDate;

    const yearSelect = document.getElementById('leaveYear');
    yearSelect.innerHTML = '';
    const hireYear = new Date(dateHired).getFullYear();
    const currentYear = new Date().getFullYear();

    for (let year = hireYear; year <= currentYear; year++) {
        const option = document.createElement('option');
        option.value = year;
        option.textContent = year;
        yearSelect.appendChild(option);
    }

    modal.style.display = 'flex';
    window.addEventListener('click', outsideClickHandler);
}

function closeUseBalanceModal() {
    const modal = document.getElementById('useBalanceModal');
    modal.style.display = 'none';
    window.removeEventListener('click', outsideClickHandler);
}

function outsideClickHandler(event) {
    const modal = document.getElementById('useBalanceModal');
    if (event.target === modal) {
        closeUseBalanceModal();
    }
}

// Submit handler
function submitLeaveUsage() {
    const leaveMonth = document.getElementById('leaveMonth').value;
    const leaveYear = document.getElementById('leaveYear').value;
    const leaveType = document.getElementById('leaveType').value;
    const creditsUsed = document.getElementById('creditsUsed').value;
    const employeeId = document.getElementById('employeeIdText').innerText;
    const dateHired = document.getElementById('dateHiredText').innerText;

    if (!leaveMonth || !leaveYear || !leaveType || !creditsUsed) {
        alert("Please fill out all fields.");
        return;
    }

    const hireDate = new Date(dateHired);
    const selectedLeaveDate = new Date(leaveYear, new Date(`${leaveMonth} 1`).getMonth(), 1);

    if (selectedLeaveDate < hireDate || selectedLeaveDate > new Date()) {
        Swal.fire({
            icon: 'error',
            title: 'Invalid Date',
            text: 'The selected date falls outside of the employee\'s employment period.',
        });
        return;
    }

    const data = {
        employeeId: employeeId,
        leaveMonth: leaveMonth,
        leaveYear: leaveYear,
        leaveType: leaveType,
        creditsUsed: parseFloat(creditsUsed)
    };

    fetch('/save-leave-usage', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        closeUseBalanceModal();
        alert('Data has been saved successfully!');
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while saving the data.');
    });
}

// Make functions available globally
window.openUseBalanceModal = openUseBalanceModal;
window.closeUseBalanceModal = closeUseBalanceModal;
window.submitLeaveUsage = submitLeaveUsage;