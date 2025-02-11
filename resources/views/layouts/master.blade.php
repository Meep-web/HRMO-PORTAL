<!-- resources/views/layouts/master.blade.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'PH-R:Portal')</title>
    
    
    <!-- Vite-managed assets (CSS and JS) -->
    
    <!-- Font Awesome 6.7.2 CSS -->
    <link href="{{ asset('vendor/fontawesome/css/all.min.css') }}" rel="stylesheet">

</head>

<body>
    <div class="layout-container">
        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- Main content area -->
        <div class="layout-main-wrapper">
            <!-- Include the header here -->
            @include('layouts.header')

            <main class="layout-main">
                @yield('content') <!-- Main content will be injected here -->
            </main>

            <footer class="layout-footer">
                @include('layouts.footer') <!-- Include the footer -->
            </footer>
        </div>
    </div>
</body>

</html>
