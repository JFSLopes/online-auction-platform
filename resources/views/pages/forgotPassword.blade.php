<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>
    <script src="{{asset('js/forgotPassword.js')}}" defer></script>
    <link rel="stylesheet" href="{{asset('css/forgotPassword.css')}}">
</head>
<body>
    <div id="main-container">
        <h1>Password Reset</h1>
        <form id="passwordResetForm">
            <meta name="csrf-token" content="{{ csrf_token() }}">
            <label for="email">Your Email:</label>
            <input type="text" id="email" name="email" required>
            <br><br>
            <button type="submit">Reset Password</button>
        </form>
    </div>
</body>
</html>