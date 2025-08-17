<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found</title>
    <link rel="stylesheet" href="{{url('css/errors/errors.css')}}">
</head>
<body>    
    <div class="error-container">
        <img src="{{ asset('images/errors/404.png') }}" alt="Page Not Found">
        <h1>404</h1>
        <p>"Smooth criminal detected: Page does not exist!" 🌕🕺</p>
        <a href="{{ url('/home') }}">Return to Home</a>
        <p class="mj-quote">"You've been hit by, you've been struck by…an Error 404!"</p>
    </div>
</body>
</html>
