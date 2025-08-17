<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <link rel="stylesheet" href="{{ asset('css/forgotPassword.css') }}">
</head>
<body>
    <div id="main-container">
        <h1>Reset Password</h1>
        <p>Please enter your new password below.</p>
        <form method="POST" action="{{ url('/password-reset') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ request('email') }}">
            <div>
                <label for="password">New Password</label>
                <input type="password" id="password" name="password" placeholder="Enter new password" required>
            </div>
            <div>
                <label for="password_confirmation">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm new password" required>
            </div>
            <button type="submit">Reset Password</button>
        </form>
    </div>
</body>
</html>