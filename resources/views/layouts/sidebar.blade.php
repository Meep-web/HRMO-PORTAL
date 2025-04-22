@vite(['resources/css/app.css', 'resources/js/app.js'])

<aside class="layout-sidebar">
    <!-- Logo Button -->
    <a href="{{ route('dashboard') }}" class="logo-button" data-title="Dashboard">
        <img src="{{ asset('logo.png') }}" alt="Logo" class="sidebar-logo" id="logo-clickable">
    </a>

    <!-- Nosa Button -->
    <a href="{{ route('noticeOfSalaryAdjustment') }}" class="nosa-button" data-title="Notice of Salary Adjustment">
        <img src="{{ asset('nosa.png') }}" alt="Nosa" class="sidebar-nosa">
    </a>

    <a href="{{ route('service.records') }}" class="service-records-button" data-title="Service Records">
        <img src="{{ asset('Service_Records.png') }}" alt="Service Records" class="sidebar-service-records">
    </a>



    <!-- Personal Data Sheet Button -->
    <a href="{{ route('personalDataSheet') }}" class="personalDataSheet-button" data-title="Personal Data Sheet">
        <img src="{{ asset('personalDataSheet.png') }}" alt="Personal Data Sheet" class="sidebar-personalDataSheet">
    </a>

    <!-- Leave Credits Button -->
    <a href="/leave-credits" class="leave-credits-button" data-title="Leave Credits">
        <img src="{{ asset('leaveCredits.png') }}" alt="Leave Credits" class="sidebar-leave-credits">
    </a>


    <!-- Account Management Button -->
    <a href="{{ session('usertype') === 'Admin' ? route('account.management') : url('/employment-status') }}"
        class="account-management-button tooltip-top" data-title="Management">
        <img src="{{ asset('Account_Management.png') }}" alt="Management" class="sidebar-account-management">
    </a>



</aside>
