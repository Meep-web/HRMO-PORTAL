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
                                    data-step-increment="{{ $employment->stepIncrement ?? '' }}"
                                    data-position="{{ $employment->designation_id ?? '' }}"
                                    data-date-hired="{{ $employment->date_hired ?? '' }}"
                                    data-date-effectivity="{{ $employment->dateOfEffectivity ?? '' }}"
                                    data-date-released="{{ $employment->dateReleased ?? '' }}">
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
    <div id="assignEmploymentModal" class="modal assign-employment-modal">
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

                <!-- Position Dropdown -->
                <label for="position">Position:</label>
                <select id="position" name="position">
                    <option value="" disabled selected>Select a position</option>
                    <!-- Dynamic positions will be loaded here -->
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

                <!-- Date Effectivity & Date Released Row -->
                <div class="date-container">
                    <div class="date-group">
                        <label for="dateOfEffectivity">Date of Effectivity:</label>
                        <input type="date" id="dateOfEffectivity" name="dateOfEffectivity" required>
                    </div>

                    <div class="date-group">
                        <label for="dateReleased">Date Released:</label>
                        <input type="date" id="dateReleased" name="dateReleased" required>
                    </div>
                </div>

                <!-- Date Hired Picker -->
                <label for="dateHired">Date Hired:</label>
                <input type="date" id="dateHired" name="dateHired" required>

                <!-- Hidden fields for old data -->
                <input type="hidden" id="oldDepartment" value="{{ $employment->department_id ?? '' }}">
                <input type="hidden" id="oldSalaryGrade" value="{{ $employment->salaryGrade ?? '' }}">
                <input type="hidden" id="oldStepIncrement" value="{{ $employment->stepIncrement ?? '' }}">
                <input type="hidden" id="oldPosition" value="{{ $employment->designation_id ?? '' }}">

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

        /* Assign Employment Modal */
        .assign-employment-modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
            /* Adds blur effect to the background */
        }

        .assign-employment-modal .modal-content {
            background-color: #ffffff;
            margin: 5% auto;
            padding: 30px;
            border-radius: 12px;
            width: 50%;
            text-align: left;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            max-width: 90%;
            /* Ensures the modal doesn't overflow on smaller screens */
            box-sizing: border-box;
            height: 90vh;
            /* Limit the modal height to 90% of the viewport height */
            overflow-y: auto;
            /* Adds scrolling if content exceeds the height */
        }

        /* Modal Close Button */
        .assign-employment-modal .close-modal {
            float: right;
            font-size: 28px;
            color: #333;
            cursor: pointer;
        }

        .assign-employment-modal .close-modal:hover {
            color: #f44336;
        }

        /* Form Elements */
        .assign-employment-modal label {
            display: block;
            margin: 10px 0 5px;
            font-weight: 600;
            color: #333;
        }

        .assign-employment-modal select,
        .assign-employment-modal input {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 6px;
            border: 1px solid #ddd;
            font-size: 16px;
        }

        .assign-employment-modal select:focus,
        .assign-employment-modal input:focus {
            outline: none;
            border-color: #007bff;
        }

        .assign-employment-modal .save-assign-btn {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 12px 20px;
            font-size: 16px;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
            margin-top: 10px;
        }

        .assign-employment-modal .save-assign-btn:hover {
            background-color: #218838;
        }

        /* Salary Grade & Step Increment Layout */
        .assign-employment-modal .salary-step-container {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        /* Salary Grade and Step Increment Group */
        .assign-employment-modal .salary-step-group {
            flex: 1;
        }

        /* Date Pickers Layout */
        .assign-employment-modal .date-container {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .assign-employment-modal .date-group {
            flex: 1;
        }

        .assign-employment-modal .date-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #333;
        }

        .assign-employment-modal .date-group input {
            width: 100%;
            padding: 12px;
            border-radius: 6px;
            border: 1px solid #ddd;
            font-size: 16px;
        }

        /* Hover Effects for Date Pickers */
        .assign-employment-modal .date-group input:focus {
            outline: none;
            border-color: #007bff;
        }

        /* Responsiveness for smaller screens */
        @media (max-width: 768px) {
            .assign-employment-modal .modal-content {
                width: 90%;
                padding: 20px;
            }

            .assign-employment-modal .salary-step-container,
            .assign-employment-modal .date-container {
                flex-direction: column;
                gap: 15px;
            }

            .assign-employment-modal .salary-step-group,
            .assign-employment-modal .date-group {
                flex: 1;
            }

            .assign-employment-modal .save-assign-btn {
                padding: 10px 15px;
            }
        }
    </style>

@endsection
