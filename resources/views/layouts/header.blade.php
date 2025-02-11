@vite(['resources/css/app.css', 'resources/js/app.js'])
<header class="layout-header flex justify-between items-center px-6 py-4 bg-gray-100 shadow-md">
    <!-- Title section -->
    <h1 id="page-title" class="text-xl font-semibold">@yield('title')</h1> <!-- Default title -->

    <!-- User avatar dropdown aligned to the right with a unique class -->
    <div class="user-dropdown relative ml-auto">
        <!-- User Icon (this will act as a button for dropdown) -->
        <button class="user-dropdown-button flex items-center space-x-2" id="userDropdownButton" aria-haspopup="true"
            aria-expanded="false">
            <!-- Person Icon -->
            <i class="fa-solid fa-user-circle text-xl text-gray-700"></i>
        </button>

        <!-- Dropdown menu -->
        <div class="user-dropdown-menu absolute right-0 hidden mt-2 bg-white shadow-lg rounded-md w-48"
            aria-labelledby="userDropdownButton">
            <!-- Dropdown header -->
            <div class="user-dropdown-header px-4 py-2 text-sm font-semibold text-gray-700">
                {{ session('employeeName', 'User Name') }} <!-- Display employeeName from session -->
            </div>

            <!-- Profile Option (form without a route for now) -->
            <form method="GET" action="#" id="profileForm" class="profile-form">
                <button type="submit" class="user-dropdown-item text-gray-700 px-4 py-2 hover:bg-gray-100">
                    <i class="fa-solid fa-user mr-2"></i> Profile
                </button>
            </form>

            <!-- Logout Option -->
            <form method="POST" action="{{ route('logout') }}" id="logoutForm" class="logout-form">
                @csrf
                <button type="submit" class="user-dropdown-item text-gray-700 px-4 py-2 hover:bg-gray-100">
                    <i class="fa-solid fa-sign-out-alt mr-2"></i> Logout
                </button>
            </form>
        </div>
    </div>
</header>
