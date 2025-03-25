@extends('layouts.master')

@section('title', 'Employment Status')
@vite(['resources/js/employmentControl.js'])

<meta name="csrf-token" content="{{ csrf_token() }}">

@section('content')
    <div class="main-content">
        <!-- Search & Upload Container -->
        <div class="search-container">
            <div class="search-bar-container">
                <input type="text" class="search-bar" placeholder="🔍 Search..." />
            </div>
            <button class="upload-button" id="uploadButton">Upload</button>
        </div>

        <!-- Table Content -->
        <div class="table-container">
            <table class="employment-status-table">
                <thead>
                    <tr>
                        <th>Full Name</th>
                        <th>Department</th>
                        <th>Pay Grade</th>
                        <th>Step Increment</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($employmentData as $employment)
                        <tr>
                            <td>{{ $employment->first_name }} {{ $employment->last_name }}</td>
                            <td>
                                @if ($employment->department_id)
                                    {{ $departments->firstWhere('id', $employment->department_id)->department_name ?? 'Unknown' }}
                                @else
                                    Not Assigned
                                @endif
                            </td>
                            <td>{{ $employment->salaryGrade ?? 'N/A' }}</td>
                            <td>{{ $employment->stepIncrement ?? 'N/A' }}</td>
                            <td>
                                <button class="assign-employment-button" data-id="{{ $employment->id }}"
                                    data-name="{{ $employment->first_name }} {{ $employment->last_name }}"
                                    data-department="{{ $employment->department_id ?? '' }}"
                                    data-salary-grade="{{ $employment->salaryGrade ?? '' }}"
                                    data-step-increment="{{ $employment->stepIncrement ?? '' }}">
                                    Assign
                                </button>

                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Assign Employment Modal -->
    <div id="assignEmploymentModal" class="modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <h2>Assign Employment</h2>
            <form id="assignEmploymentForm">
                <input type="hidden" id="employeeId">

                <label for="employeeName">Employee:</label>
                <input type="text" id="employeeName" readonly>

                <label for="department">Department:</label>
                <select id="department" name="department">
                    <option value="" disabled selected>Select a department</option>
                    @foreach ($departments as $department)
                        <option value="{{ $department->id }}">{{ $department->department_name }}</option>
                    @endforeach
                </select>

                <!-- Salary Grade & Step Increment Row -->
                <div class="salary-step-container">
                    <div class="salary-step-group">
                        <label for="salaryGrade">Pay Grade:</label>
                        <select id="salaryGrade" name="salaryGrade">
                            <option value="" disabled selected>Select Salary Grade</option>
                            @for ($i = 1; $i <= 33; $i++)
                                <option value="{{ $i }}">Grade {{ $i }}</option>
                            @endfor
                        </select>
                    </div>

                    <div class="salary-step-group">
                        <label for="stepIncrement">Step Increment:</label>
                        <select id="stepIncrement" name="stepIncrement">
                            <option value="" disabled selected>Select Step</option>
                            @for ($i = 1; $i <= 8; $i++)
                                <option value="{{ $i }}">Step {{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </div>

                <button type="submit" class="save-assign-btn">Save</button>
            </form>
        </div>
    </div>


    <style>
        .main-content {
            padding: 20px;
        }

        /* Search and Upload Container */
        .search-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            width: 100%;
        }

        .search-bar-container {
            display: flex;
            align-items: center;
            flex-grow: 0;
            width: 250px;
            margin-right: 10px;
        }

        .search-bar {
            padding: 8px;
            font-size: 14px;
            width: 100%;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        /* Upload Button */
        .upload-button {
            padding: 8px 15px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .upload-button:hover {
            background: #0056b3;
        }

        /* Employment Status Table */
        .employment-status-table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            overflow-x: auto;
        }

        .employment-status-table th,
        .employment-status-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .employment-status-table th {
            background-color: black;
            color: white;
            font-weight: bold;
        }

        .employment-status-table td {
            background-color: white;
            color: black;
        }

        .employment-status-table tr {
            height: 50px;
        }

        .employment-status-table tr:hover {
            background-color: #f1f1f1;
        }

        /* Action Button */
        .assign-employment-button {
            padding: 5px 10px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        .assign-employment-button:hover {
            background: #218838;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: white;
            margin: 10% auto;
            padding: 20px;
            border-radius: 10px;
            width: 40%;
            text-align: center;
        }

        .close-modal {
            float: right;
            font-size: 28px;
            cursor: pointer;
        }

        label {
            display: block;
            margin: 10px 0 5px;
        }

        select,
        input {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
        }

        .save-assign-btn {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
        }

        .save-assign-btn:hover {
            background-color: #218838;
        }

        .salary-step-container {
            display: flex;
            gap: 20px;
            /* Adjust spacing between Salary Grade and Step Increment */
        }

        .salary-step-group {
            flex: 1;
            /* Makes both fields equal in width */
            display: flex;
            flex-direction: column;
        }
    </style>
@endsection
