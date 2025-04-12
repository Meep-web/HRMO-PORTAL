@extends('layouts.master')

@section('title', 'Leave Credits')
@vite('resources/css/leaveCredits.css')
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
                        <td>{{ $employee->personalInfo->first_name }} {{ $employee->personalInfo->middle_name }} {{ $employee->personalInfo->last_name }}</td> <!-- Employee full name -->
                        <td>{{ $employee->department->department_name }}</td> <!-- Department name -->
                        <td>{{ $employee->date_hired_formatted ?? 'N/A' }}</td> <!-- Formatted Date Hired -->
                        <td>{{ $employee->leave_balance !== null ? floor($employee->leave_balance) : 'N/A' }}</td> <!-- Leave balance (calculated) -->
                        <td>{{ $employee->sick_leave !== null ? floor($employee->sick_leave) : 'N/A' }}</td> <!-- Sick leave (calculated) -->
                        <td>
                            <button class="view-details-button" data-id="{{ $employee->id }}">
                                View Details
                            </button>
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
