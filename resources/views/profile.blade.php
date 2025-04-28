@extends('layouts.master')

@vite(['resources/js/profile.js'])

@section('title', 'Profile')

<meta name="csrf-token" content="{{ csrf_token() }}" />

<style>
    .profile-container {
        max-width: 500px;
        margin: 40px auto;
        background: #fff;
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        border-radius: 15px;
        padding: 15px;
        text-align: center;
    }

    .profile-header {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 30px;
        margin-bottom: 25px;
    }

    .profile-image {
        width: 180px;
        height: 180px;
        border-radius: 50%;
        object-fit: cover;
        border: 5px solid #ddd;
    }

    .upload-btn {
        background: #4CAF50;
        color: white;
        padding: 12px 18px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 16px;
        font-weight: bold;
    }

    .upload-btn:hover {
        background: #45a049;
    }

    .profile-info {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 15px;
    }

    .profile-info label {
        font-weight: bold;
        font-size: 18px;
        color: #444;
    }

    .profile-input {
        width: 100%;
        max-width: 400px;
        padding: 12px;
        border: 2px solid #bbb;
        border-radius: 8px;
        font-size: 18px;
        text-align: center;
    }

    .button-container {
        display: flex;
        justify-content: center;
        gap: 15px;
        /* Adjust spacing between buttons */
        margin-top: 20px;
    }

    .save-btn,
    .modal-open-btn {
        padding: 12px 20px;
        /* Ensures equal padding */
        font-size: 18px;
        /* Ensures equal font size */
        font-weight: bold;
        width: 160px;
        /* Set a fixed width */
        height: 70px;
        /* Set a fixed height */
        text-align: center;
        border: none;
        border-radius: 8px;
        cursor: pointer;
    }

    .save-btn {
        background: #007bff;
        color: white;
    }

    .save-btn:hover {
        background: #0056b3;
    }

    .modal-open-btn {
        background: #ff9800;
        color: white;
    }

    .modal-open-btn:hover {
        background: #e68900;
    }

    .hidden-input {
        display: none;
    }

    .role-container {
        text-align: center;
    }

    .role-label {
        font-weight: bold;
        font-size: 18px;
        color: #444;
        margin-bottom: 5px;
    }

    .role-text {
        font-size: 20px;
        font-weight: bold;
        color: #555;
        margin-top: 5px;
    }

    /* Modal Styling */
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
        background: white;
        padding: 20px;
        width: 50%;
        margin: 10% auto;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        text-align: center;
    }

    .close-btn {
        float: right;
        font-size: 20px;
        cursor: pointer;
    }

    /* Modal Styling */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.4);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-content {
        background: white;
        padding: 20px;
        border-radius: 10px;
        width: 350px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
        text-align: center;
        animation: fadeIn 0.3s ease-in-out;
    }

    .close-btn {
        float: right;
        font-size: 20px;
        font-weight: bold;
        cursor: pointer;
    }

    .close-btn:hover {
        color: red;
    }

    /* Form Styling */
    #changePasswordForm {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    #changePasswordForm label {
        text-align: left;
        font-weight: 500;
        font-size: 14px;
    }

    #changePasswordForm input {
        width: 100%;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 14px;
    }

    #changePasswordForm input:focus {
        border-color: #007bff;
        outline: none;
    }

    /* Button Styling */
    .save-btn {
        background-color: #007bff;
        color: white;
        padding: 10px;
        border: none;
        border-radius: 5px;
        font-size: 14px;
        cursor: pointer;

    }

    .save-btn:hover {
        background-color: #0056b3;
    }

    /* Input Group Styling */
    .input-group {
        display: flex;
        flex-direction: column;
        gap: 5px;
        margin-bottom: 15px;
    }

    /* Password Container */
    .password-container {
        position: relative;
        display: flex;
        align-items: center;
    }

    /* Password Input Field */
    .password-input {
        width: 100%;
        padding: 8px;
        padding-right: 35px;
        /* Space for eye icon */
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 14px;
    }

    /* Eye Icon */
    .toggle-password {
        position: absolute;
        right: 10px;
        cursor: pointer;
        color: #777;
        font-size: 16px;
    }

    .toggle-password:hover {
        color: #333;
    }
    .swal2-container {
    z-index: 9999 !important; /* Ensure it appears above everything */
}

</style>


@section('content')
    <div class="profile-container">
        <div class="profile-header">
            <img id="profileImage" src="{{ asset($employee->imagePath ?? 'employeeImage/default-user.png') }}"
                alt="Profile Picture" class="profile-image">

            <form id="uploadForm" enctype="multipart/form-data">
                <input type="hidden" id="employeeId" name="employeeId" value="{{ $employee->id }}">
                <input type="file" name="profileImage" id="profileImageInput" class="hidden-input" accept="image/*">
                <button type="button" class="upload-btn" onclick="document.getElementById('profileImageInput').click();">
                    Upload Image
                </button>
            </form>
        </div>

        <div class="profile-info">
            <label for="employeeName">Name:</label>
            <input type="text" id="employeeName" name="employeeName" class="profile-input"
                value="{{ $employee->employeeName ?? '' }}">

            <div class="role-container">
                <label class="role-label">Role:</label>
                <p class="role-text">{{ $employee->role ?? 'N/A' }}</p>
            </div>

            <!-- Button Container for proper alignment -->
            <div class="button-container">
                <button type="button" class="save-btn">Save Changes</button>
                <button type="button" id="openModalBtn" class="modal-open-btn">Change Password</button>
            </div>

        </div>

        <!-- Modal Structure -->
        <div id="profileModal" class="modal">
            <div class="modal-content">
                <span class="close-btn">&times;</span>
                <h2>Change Password</h2>

                <form id="changePasswordForm">
                    <!-- Password Field -->
                    <div class="input-group">
                        <label for="password">New Password</label>
                        <div class="password-container">
                            <input type="password" id="password" class="password-input">
                            <i class="fa-solid fa-eye toggle-password" data-target="password"></i>
                        </div>
                    </div>

                    <!-- Confirm Password Field -->
                    <div class="input-group">
                        <label for="confirmPassword">Confirm Password</label>
                        <div class="password-container">
                            <input type="password" id="confirmPassword" class="password-input">
                            <i class="fa-solid fa-eye toggle-password" data-target="confirmPassword"></i>
                        </div>
                    </div>

                    <button type="submit" class="save-btn">Save Changes</button>
                </form>
            </div>
        </div>



    </div>


@endsection
