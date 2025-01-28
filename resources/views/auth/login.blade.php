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
            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="input-group">
                    <input type="text" name="employeeName" id="employeeName" class="input-field" placeholder="Employee Name" required>
                </div>

                <div class="input-group">
                    <input type="password" name="password" id="password" class="input-field" placeholder="Password" required>
                </div>

                <button type="submit" class="submit-button">Login</button>
            </form>

            <!-- Error handling -->
            @if ($errors->any())
                <div class="error-message">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>
</body>

</html>
