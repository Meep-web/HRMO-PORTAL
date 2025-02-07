<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <!-- Include Login-Specific CSS -->
    @vite(['resources/css/login.css'])
</head>

<body style="background: url('{{ asset('pagsanjanBackground.png') }}') no-repeat center center/100% auto, linear-gradient(to top, #000000, #ffffff, #000000);">
    <!-- Container for Centering the Form -->
    <div class="login-container">
        <div class="login-box">
            <h2 class="login-title">Login</h2>
            
            <!-- Login Form -->
            <form action="{{ route('login.submit') }}" method="POST">
                @csrf
                <div class="input-group">
                    <input type="text" name="employeeName" id="employeeName" class="input-field" placeholder="Employee Name" value="{{ old('employeeName') }}" required>
                </div>

                <div class="input-group">
                    <input type="password" name="password" id="password" class="input-field" placeholder="Password" required>
                </div>

                <button type="submit" class="submit-button">Login</button>
            </form>

            <!-- Display General Errors -->
            @if ($errors->any())
                <div class="error-message">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Display Throttle Error (Remaining Time) -->
            @if (session('throttle_error'))
                <div class="throttle-error-message">
                    Too many unsuccessful login attempts. Try again after: 
                    <span id="countdown-timer">{{ session('remaining_time') }}</span> seconds.
                </div>
            @endif
        </div>
    </div>

    <!-- JavaScript for Countdown Timer -->
    <script>
        // Get the remaining time from the server
        const remainingTime = {{ session('remaining_time', 0) }};

        // Function to update the countdown timer
        function updateCountdown() {
            const timerElement = document.getElementById('countdown-timer');
            if (timerElement && remainingTime > 0) {
                let timeLeft = remainingTime;

                // Update the timer every second
                const countdownInterval = setInterval(() => {
                    timeLeft--;

                    // Update the timer display
                    timerElement.textContent = timeLeft;

                    // Stop the timer when it reaches 0
                    if (timeLeft <= 0) {
                        clearInterval(countdownInterval);
                        timerElement.textContent = '0';
                        location.reload(); // Reload the page to re-enable the login form
                    }
                }, 1000); // Update every second
            }
        }

        // Start the countdown when the page loads
        window.onload = updateCountdown;
    </script>
</body>

</html>