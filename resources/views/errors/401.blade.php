<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unauthorized Access</title>
    <link rel="stylesheet" href="{{url('css/errors/errors.css')}}">
</head>
<body>
    <div class="error-container">
        <img src="{{ asset('images/errors/404.png') }}" alt="Unauthorized Access">
        <h1>401</h1>
        <p>"Stop! You've reached the moonwalk zone without the proper grooves! 🛑🌕"</p>
        <a href="{{ url('/login') }}">Log In to Access</a>
        <p class="mj-quote">"You've been hit by, you've been struck by…an Error 401!"</p>
    </div>
</body>
</html>
