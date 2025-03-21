@extends('layouts.master')

@section('title', 'Account Management')

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
    </style>
@endsection