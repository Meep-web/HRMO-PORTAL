@extends('layouts.master')

@section('title', 'Employment Status')

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
               
            </tbody>
        </table>
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
.view-button {
    padding: 5px 10px;
    background: #28a745;
    color: white;
    border: none;
    border-radius: 3px;
    cursor: pointer;
}

.view-button:hover {
    background: #218838;
}
</style>
@endsection
