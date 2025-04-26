@extends('layouts.master')

@section('title', 'Notice of Salary Adjustment')

@section('content')
    @vite(['resources/css/nosa.css','resources/css/nosaModal.css', 'resources/js/app.js', 'resources/js/nosa.js']) <!-- Include your CSS and JS -->
   
    <meta name="csrf-token" content="{{ csrf_token() }}">

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
                        <th>Last Updated</th>
                        <th>Updated By</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($employees as $employee)
                        <tr>
                            <td>{{ $employee['name'] }}</td>
                            <td>{{ $employee['department'] }}</td>
                            <td>{{ \Carbon\Carbon::parse($employee['updated_at'])->format('F d, Y h:i A') }}</td>
                            <td>{{ $employee['updatedBy'] }}</td>
                            
                                <td>
                                    <button class="show-salary-changes-button" data-id="{{ $employee['id'] }}">
                                        Show Salary Changes
                                    </button>
                                </td>
                                
                            
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center;">No employee data available.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

    <!-- Modal -->
    <div id="uploadModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Salary Adjustment Details</h2>

            <!-- Table -->
            <table id="salaryAdjustmentTable">
                <thead>
                    <tr>
                        <th>Employee Name</th>
                        <th>Position</th>
                        <th>Department</th>
                        <th>Previous Salary</th>
                        <th>New Salary</th>
                        <th>Date of Effectivity</th>
                        <th>Date Released</th>
                        <th>Salary Grade</th>
                        <th>Step Increment</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Initial Row -->
                    <tr>
                        <td><input type="text" name="employee_name[]" placeholder="Employee Name" /></td>
                        <td><input type="text" name="position[]" placeholder="Position" /></td>
                        <td><input type="text" name="department[]" placeholder="Department" /></td>
                        <td><input type="number" name="previous_salary[]" placeholder="Previous Salary" /></td>
                        <td><input type="number" name="new_salary[]" placeholder="New Salary" /></td>
                        <td><input type="date" name="date_of_effectivity[]" /></td>
                        <td><input type="date" name="date_released[]" /></td>
                        <td><input type="number" name="salary_grade[]" placeholder="Salary Grade" /></td>
                        <td><input type="number" name="step_increment[]" placeholder="Step Increment" /></td>
                    </tr>
                </tbody>
            </table>

            <!-- Add Another Button -->
            <button id="addAnotherRow" class="add-row-button">Add Another</button>

            <!-- Save Button -->
            <button id="saveButton" class="save-button">Save</button>
        </div>
    </div>

   <!-- Modal to show salary changes -->
<div id="updateHistoryModal" class="modal">
    <div class="modal-content">
        <span id="closeUpdateHistory" class="close">&times;</span>
        <h2>Salary Change History</h2>

        <!-- Table for displaying salary changes -->
        <table id="salaryChangesTable" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th>Department</th>
                    <th>Date of Effectivity</th>
                    <th>Date Released</th>
                    <th>Action</th> <!-- Action column for the button -->
                </tr>
            </thead>
            <tbody id="updateHistoryData">
                <!-- Data will be injected here -->
            </tbody>
        </table>

        <!-- Button to generate PDF (for later) -->
        <!-- Will be added later -->
    </div>
</div>


    <!-- PDF Display Modal -->
    <div id="pdfModal" class="modal">
        <div class="modal-content">
            <span class="close-pdf-modal">&times;</span>
            <h2>NOSA Document</h2>
            <iframe id="pdfIframe" src="" width="100%" height="500px"></iframe>
        </div>
    </div>





@endsection
