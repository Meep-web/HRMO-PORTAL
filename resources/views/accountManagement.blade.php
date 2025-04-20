@extends('layouts.master')

@section('title', 'Management')
<script>
    // Wait for the DOM to be ready
    document.addEventListener('DOMContentLoaded', function() {
        // Get the modal
        const modal = document.getElementById("backupModal");

        // Get all buttons with the class "card-button" (there might be more than one)
        const backupButtons = document.querySelectorAll(".card-button");

        // Get the <span> element that closes the modal
        const closeButton = document.getElementById("closeModal");

        // When the user clicks the "Manage" button, open the modal
        backupButtons.forEach(button => {
            button.addEventListener('click', function() {
                modal.style.display = "block";
            });
        });

        // When the user clicks on <span> (x), close the modal
        closeButton.onclick = function() {
            modal.style.display = "none";
        }

        // When the user clicks anywhere outside the modal, close it
        window.onclick = function(event) {
            if (event.target === modal) {
                modal.style.display = "none";
            }
        }
    });

    function backupData() {
        // Close the modal when the button is clicked
        const modal = document.getElementById("backupModal");
        modal.style.display = "none";

        // Create a new form dynamically
        const form = document.createElement('form');
        form.method = 'GET';
        form.action = "{{ route('backup.database') }}"; // The route to your backup function

        // Append the form to the body and submit it
        document.body.appendChild(form);
        form.submit();
    }

    document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('submitUploadButton').addEventListener('click', function() {
        const databaseFile = document.getElementById('databaseFile').files[0];
        const employmentChangesFile = document.getElementById('employmentChangesFile').files[0];
        const leaveCreditsFile = document.getElementById('leaveCreditsFile').files[0];

        // Check and log the file names if files are selected
        if (databaseFile) {
            console.log("Database SQL File:", databaseFile.name);
        } else {
            console.log("No Database SQL file selected.");
        }

        if (employmentChangesFile) {
            console.log("Employment Changes JSON File:", employmentChangesFile.name);
        } else {
            console.log("No Employment Changes JSON file selected.");
        }

        if (leaveCreditsFile) {
            console.log("Leave Credits JSON File:", leaveCreditsFile.name);
        } else {
            console.log("No Leave Credits JSON file selected.");
        }

        // Create a FormData object to send the files to the server
        const formData = new FormData();
        if (databaseFile) {
            formData.append('databaseFile', databaseFile);
        }
        if (employmentChangesFile) {
            formData.append('employmentChangesFile', employmentChangesFile);
        }
        if (leaveCreditsFile) {
            formData.append('leaveCreditsFile', leaveCreditsFile);
        }

        // Send the data to the server using AJAX
        fetch("{{ route('backup.upload') }}", {
            method: "POST",
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') // Add CSRF token if necessary
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error("Server error: " + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            console.log(data);
            if (data.success) {
                alert("Files uploaded and processed successfully!");
            } else {
                alert("Error: " + data.error);
            }
        })
        .catch(error => {
            console.error('Error uploading files:', error);
            alert("There was an error uploading the files.");
        });
    });
});


</script>

<meta name="csrf-token" content="{{ csrf_token() }}">

@section('content')
    <div class="account-management-container">
        <div class="card-grid">

            <!-- User Accounts Card -->
            <div class="card">
                <div class="card-icon">
                    <img src="{{ asset('encoding.png') }}" alt="User Accounts">
                </div>
                <div class="card-content">
                    <div class="card-title">User Accounts</div>
                    <a href="{{ route('user.accounts') }}">
                        <button class="card-button">Manage</button>
                    </a>
                </div>
            </div>

            <!-- Employment Status Card -->
            <div class="card">
                <div class="card-icon">
                    <img src="{{ asset('recruitment.png') }}" alt="Employment Status">
                </div>
                <div class="card-content">
                    <div class="card-title">Employment Status</div>
                    <a href="/employment-status" class="card-button">Manage</a>
                </div>
            </div>

            <!-- Backup Data Card -->
            <div class="card">
                <div class="card-icon">
                    <img src="{{ asset('Backup.png') }}" alt="Backup Data">
                </div>
                <div class="card-content">
                    <div class="card-title">Backup Data</div>
                    <a href="javascript:void(0);" class="card-button">Manage</a>
                </div>
            </div>
        </div>
    </div>

    <style>
        .account-management-container {
            display: flex;
            justify-content: flex-start;
            /* Align to the left */
            align-items: flex-start;
            /* Align to the top */
            padding: 20px;
            /* Add some spacing from the edges */
        }

        .card-grid {
            display: flex;
            flex-direction: column;
            /* Stack cards vertically */
            gap: 20px;
            /* Space between cards */
        }

        .card {
            display: flex;
            align-items: center;
            width: 350px;
            height: 150px;
            background: #f8f9fa;
            border-radius: 10px;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .card-icon img {
            width: 70px;
            height: 70px;
            margin-right: 15px;
        }

        .card-content {
            display: flex;
            flex-direction: column;
            justify-content: center;
            width: 100%;
            /* Ensures button aligns properly */
        }

        .card-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .card-button {
            width: 120px;
            /* Set fixed width */
            height: 40px;
            /* Set fixed height */
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 16px;
            padding: 8px 15px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .card-button:hover {
            background: #0056b3;
        }

        /* Modal Style */
        .modal {
            display: none;
            /* Hidden by default */
            position: fixed;
            z-index: 1;
            /* Sit on top */
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            /* Enable scroll if the content is too large */
            background-color: rgba(0, 0, 0, 0.4);
            /* Background color with opacity */
        }

        /* Modal Content */
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            /* Adjust to fit the screen */
            max-width: 800px;
            /* Max width of modal */
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        /* Close Button */
        .close-button {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            position: absolute;
            top: 10px;
            right: 20px;
            cursor: pointer;
        }

        .close-button:hover,
        .close-button:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        /* Modal Body */
        .modal-body {
            padding: 20px;
            text-align: center;
        }

        /* Button */
        button.btn {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            text-align: center;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        button.btn:hover {
            background-color: #0056b3;
        }

        /* Form and Input Fields */
        .form-group {
            margin-bottom: 20px;
            text-align: left;
            padding: 10px;
        }

        .form-group label {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 5px;
            display: block;
            color: #333;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .form-control:focus {
            border-color: #007bff;
            outline: none;
        }

        hr {
            margin: 30px 0;
            border: 1px solid #ddd;
        }

        /* Mobile Adjustments */
        @media screen and (max-width: 768px) {
            .modal-content {
                width: 90%;
                margin-top: 10%;
            }

            .form-group label {
                font-size: 16px;
            }

            button.btn {
                width: 100%;
            }
        }
    </style>

    <!-- Backup Data Modal -->
<div id="backupModal" class="modal">
    <div class="modal-content">
        <span class="close-button" id="closeModal">&times;</span>
        <div class="modal-body">
            <h2>Backup Your Data</h2>
            <p>Please click "Backup Data" to create a backup, or upload your own data files below.</p>

            <!-- Button to trigger backup -->
            <button class="btn btn-primary" id="backupDataButton" onclick="backupData()">Backup Data</button>

            <hr>

            <!-- Form to upload files -->
            <form id="fileUploadForm" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="databaseFile">Database (SQL file)</label>
                    <input type="file" id="databaseFile" name="databaseFile" class="form-control">
                </div>

                <div class="form-group">
                    <label for="employmentChangesFile">Employment Changes (employment_changes.json)</label>
                    <input type="file" id="employmentChangesFile" name="employmentChangesFile" class="form-control">
                </div>

                <div class="form-group">
                    <label for="leaveCreditsFile">Leave Credits Usage (leave_usage_all.json)</label>
                    <input type="file" id="leaveCreditsFile" name="leaveCreditsFile" class="form-control">
                </div>

                <!-- Submit Button (Different from Backup Data button) -->
                <div class="form-group">
                    <button type="button" class="btn btn-success" id="submitUploadButton">Submit Files</button>
                </div>
            </form>
        </div>
    </div>
</div>


@endsection
