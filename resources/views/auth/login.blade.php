<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>


    @vite(['resources/css/login.css'])
</head>

<body style="background: url('{{ asset('pagsanjanBackground.png') }}') no-repeat center center/100% auto, linear-gradient(to top, #000000, #ffffff, #000000);">

    <div class="login-container">
        <div class="login-box">
            <h2 class="login-title">Login</h2>
            

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


            @if ($errors->any())
                <div class="error-message">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif


            @if (session('throttle_error'))
                <div class="throttle-error-message">
                    Too many unsuccessful login attempts. Try again after: 
                    <span id="countdown-timer">{{ session('remaining_time') }}</span> seconds.
                </div>
            @endif
        </div>
    </div>


    <script>

        const remainingTime = {{ session('remaining_time', 0) }};


        function updateCountdown() {
            const timerElement = document.getElementById('countdown-timer');
            if (timerElement && remainingTime > 0) {
                let timeLeft = remainingTime;


                const countdownInterval = setInterval(() => {
                    timeLeft--;


                    timerElement.textContent = timeLeft;


                    if (timeLeft <= 0) {
                        clearInterval(countdownInterval);
                        timerElement.textContent = '0';
                        location.reload(); 
                    }
                }, 1000);
            }
        }

        window.onload = updateCountdown;
    </script>
</body>

</html>