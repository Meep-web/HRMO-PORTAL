@extends('layouts.master')

@section('title', 'Leave Credits')
@vite(['resources/css/leaveCredits.css', 'resources/js/leaveCredits.js'])
<meta name="csrf-token" content="{{ csrf_token() }}">

@section('content')
    <div class="main-content">
        <!-- Hidden input to store the current employeeName -->
        <input type="hidden" id="currentEmployeeName" value="{{ session('employeeName') }}" />

        <div class="search-container">
            <!-- Search Bar -->
            <div class="search-bar-container">
                <input type="text" class="search-bar" placeholder="🔍 Search..." />
            </div>
        </div>

        <!-- Table Content -->
        <div class="table-container">
            <table class="salary-adjustment-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Department</th>
                        <th>Date Hired</th>
                        <th>Leave Balance</th>
                        <th>Sick Leave</th>
                        <th>Action</th>
                    </tr>

                </thead>
                <tbody>
                    @forelse ($employees as $employee)
                        <tr>
                            <td>{{ $employee->personalInfo->first_name }} {{ $employee->personalInfo->middle_name }}
                                {{ $employee->personalInfo->last_name }}</td> <!-- Employee full name -->
                            <td>{{ $employee->department->department_name }}</td> <!-- Department name -->
                            <td>{{ $employee->date_hired_formatted ?? 'N/A' }}</td> <!-- Formatted Date Hired -->
                            <td>{{ $employee->leave_balance !== null ? floor($employee->leave_balance) : 'N/A' }}</td>
                            <!-- Leave balance (calculated) -->
                            <td>{{ $employee->sick_leave !== null ? floor($employee->sick_leave) : 'N/A' }}</td>
                            <!-- Sick leave (calculated) -->
                            <td>
                                @if (session('usertype') !== 'Employee')
                                    <button class="btn-nice"
                                        onclick="openUseBalanceModal({{ $employee->id }}, {{ $employee->leave_balance ?? 0 }}, {{ $employee->sick_leave ?? 0 }}, '{{ $employee->full_name }}', '{{ $employee->date_hired }}')">
                                        Use Leave Credits
                                    </button>
                                @endif

                                <a href="{{ route('getEmployeeData', ['id' => $employee->id]) }}" target="_blank">
                                    <button class="btn-nice btn-green">
                                        Leave Credit File
                                    </button>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center;">No employee data available.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.querySelector('.search-bar');
        const tableRows = document.querySelectorAll('.salary-adjustment-table tbody tr');

        searchInput.addEventListener('input', function() {
            const searchTerm = searchInput.value.toLowerCase();

            tableRows.forEach(function(row) {
                const rowText = row.textContent.toLowerCase();
                if (rowText.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });
</script>

<div id="useBalanceModal" class="modal">
    <div class="modal-content">
        <span onclick="closeUseBalanceModal()" class="close">&times;</span>

        <p id="employeeIdText" style="display: none;"></p>
        <p><strong>Employee Name:</strong> <span id="employeeNameText"></span></p>
        <p><strong>Date Hired:</strong> <span id="dateHiredText"></span></p>

        <p>
            <strong>Vacation Leave:</strong> <span id="vacationLeaveText"></span> &nbsp;&nbsp;
            <strong>Sick Leave:</strong> <span id="sickLeaveText"></span>
        </p>

        <div class="inline-form">
            <label for="leaveMonth">Month:</label>
            <select id="leaveMonth">
                <option value="">-- Select Month --</option>
                <option value="January">January</option>
                <option value="February">February</option>
                <option value="March">March</option>
                <option value="April">April</option>
                <option value="May">May</option>
                <option value="June">June</option>
                <option value="July">July</option>
                <option value="August">August</option>
                <option value="September">September</option>
                <option value="October">October</option>
                <option value="November">November</option>
                <option value="December">December</option>
            </select>

            <label for="leaveYear">Year:</label>
            <select id="leaveYear">
                <option value="">-- Select Year --</option>
                <!-- Remove inline <script>, year options will be handled via JS -->
            </select>

            <label for="leaveType">Type:</label>
            <select id="leaveType">
                <option value="">-- Select Type --</option>
                <option value="VL">Vacation Leave</option>
                <option value="SL">Sick Leave</option>
            </select>

            <label for="creditsUsed">Credits Used:</label>
            <input type="number" id="creditsUsed" placeholder="0.0" min="0" step="0.5">

            <!-- 👇 New Dropdown for With Pay / Without Pay -->
            <label for="payType">Pay Type:</label>
            <select id="payType">
                <option value="">-- Select Pay Type --</option>
                <option value="With Pay">With Pay</option>
                <option value="Without Pay">Without Pay</option>
            </select>
        </div>

        <div class="modal-footer">
            <button onclick="submitLeaveUsage()">Submit</button>
        </div>
    </div>
</div>
