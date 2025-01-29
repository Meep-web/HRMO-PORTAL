@extends('layouts.master')

@section('title', 'NOTICE OF SALARY ADJUSTMENT')

@section('content')
    @vite(['resources/css/nosa.css', 'resources/js/app.js']) <!-- Include your CSS and JS -->
    <input type="hidden" id="saveNosaRoute" value="{{ route('save.nosa') }}" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="main-content">
        <!-- Hidden input to store the current employeeName -->
        <input type="hidden" id="currentEmployeeName" value="{{ session('employeeName') }}" />
        <div class="search-container">
            <!-- Search Bar -->
            <div class="search-bar-container">
                <input type="text" class="search-bar" placeholder="🔍 Search..." />
            </div>

            <!-- Upload Button -->
            <button class="upload-button" id="uploadButton">Upload</button>
        </div>

        <!-- Table Content -->
        <div class="table-container">
            <table class="salary-adjustment-table">
                <thead>
                    <tr>
                        <th>Department</th>
                        <th>Last Updated</th>
                        <th>Action</th>
                        <th>Updated By</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($nosaRecords as $record)
                        <tr>
                            <td>{{ $record->department }}</td>
                            <td>{{ $record->updated_at->format('Y-m-d h:i A') }}</td>
                            <td>
                                <button class="view-button" data-department="{{ $record->department }}">View</button>
                            </td>
                            <td>{{ $record->userName }}</td>
                        </tr>
                    @endforeach
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

    <!-- Department Specific Modal -->
<div id="DepartmentSpecificModal" class="department-specific-modal">
    <div class="department-specific-modal-content">
        <span class="close-department-specific-modal">&times;</span>
        <h2>Employee Salary Adjustment Details</h2>
        <table id="employeeDataTable">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Position</th>
                    <th>Date Released</th>
                    <th>Updated By</th>
                    <th>Action</th> <!-- New Action Column -->
                </tr>
            </thead>
            <tbody>
                <!-- Data will be dynamically inserted here -->
            </tbody>
        </table>
    </div>
</div>

<!-- PDF Display Modal -->
<div id="pdfModal" class="modal">
    <div class="modal-content">
        <span class="close-pdf-modal">&times;</span>
        <h2>NOSA Document</h2>
        <iframe id="pdfIframe" src="" width="100%" height="300px"></iframe>
    </div>
</div>

@endsection
