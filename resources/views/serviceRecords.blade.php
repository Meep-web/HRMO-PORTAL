@extends('layouts.master')

@section('title', 'Service Records')
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
@section('content')
    @vite(['resources/css/serviceRecords.css', 'resources/js/serviceRecords.js'])

    <div class="main-content">
        <input type="hidden" id="currentEmployeeName" value="{{ session('employeeName') }}" />

        <!-- Search Bar -->
        <div class="search-container">
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
                        <th>Position</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($personalInfos as $personalInfo)
                        <tr>
                            <td>{{ $personalInfo->full_name }}</td> <!-- Display the combined full name -->
                            <td>{{ $personalInfo->department_name }}</td>
                            <td>{{ $personalInfo->designation_name }}</td>
                            <td>
                                <form action="{{ route('generate.service.record', ['id' => $personalInfo->id]) }}"
                                    method="GET" target="_blank">
                                    <button type="submit" class="show-service-record-button">
                                        Show Service Record
                                    </button>
                                </form>

                            </td>
                        </tr>
                    @endforeach
                    @if ($personalInfos->isEmpty())
                        <tr>
                            <td colspan="4" style="text-align: center;">No service records available.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
@endsection
