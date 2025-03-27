@extends('layouts.master')

@section('title', 'User Accounts')

@vite(['resources/css/accountManagement.css', 'resources/css/editAccount.css', 'resources/js/editAccount.js'])


<meta name="csrf-token" content="{{ csrf_token() }}">

@section('content')
    <div class="user-accounts-container">
        <div class="card-grid">
            @foreach ($employees as $employee)
                <div class="user-card">
                    <div class="user-icon">
                        <img src="{{ asset($employee->imagePath ?: 'employeeImage/default-user.png') }}" alt="User Avatar">
                    </div>

                    <div class="user-info">
                        <div class="user-name">{{ $employee->employeeName }}</div>
                        <div class="user-type">{{ ucfirst($employee->role) }}</div>
                    </div>

                    <!-- Manage Button -->
                    <button class="manage-button" data-user-id="{{ $employee->id }}""
                        data-name="{{ $employee->employeeName }}" data-role="{{ ucfirst($employee->role) }}"
                        data-image="{{ asset($employee->imagePath ?: 'employeeImage/default-user.png') }}">
                        Manage
                    </button>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Custom Modal -->
    <div id="customManageModal" class="custom-modal hidden">
        <div class="custom-modal-content">
            <span class="close-modal">&times;</span>
            <h2 class="modal-title">Manage Account</h2>

            <div class="modal-body">
                <p class="user-name-text" id="modalUserName"></p>
                <div class="image-upload-container">
                    <div class="image-preview">
                        <img id="modalUserImage" src="" alt="User Avatar" class="user-modal-img">
                    </div>
                    <div class="upload-section">
                        <p class="change-image-text">Change Image</p>
                        <input type="file" id="imageUpload" accept="image/*">
                    </div>
                </div>


                <div class="modal-input-group">
                    <label for="modalUserRole">Role:</label>
                    <select id="modalUserRole" class="modal-dropdown">
                        <option value="Encoder">Encoder</option>
                        <option value="Employee">Employee</option>
                    </select>
                </div>
                <div class="modal-buttons">
                    <button id="resetPasswordBtn" class="modal-btn reset-btn" data-user-id="{{ $employee->id }}">Reset
                        Password</button>

                    <button id="saveChangesBtn" class="modal-btn save-btn">Save</button>
                </div>

            </div>
        </div>
    </div>




@endsection
