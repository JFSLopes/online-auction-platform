<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/loginsignuppage.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/validator/validator.min.js"></script>                
    <title>Register and Login page</title>
</head>

<body>
<div class="back-img">
    <section class="container" id="container">
        <section class="form-container sign-up">
            <form action="{{ route('register') }}" method="POST">
                @csrf
                <h1>Create Account</h1>
                <section class="social-icons">
                    <a href="{{route('loginGoogle')}}" class="icon"><i class="fa-brands fa-google-plus-g"></i></a>
                </section>
                <span>or Register with:</span>
                <input type="text" name="username" placeholder="Username" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <input type="text" name="address" placeholder="Address">
                <input type="tel" name="phonenumber" placeholder="Phone number">
                <button type="submit">Sign Up</button>
            </form> 
        </section>
        <section class="form-container sign-in">
            @if($errors->any())
                <div class="error-notification hidden" id="errorNotification">
                    @foreach ($errors->all() as $error)
                        {{ $error }}<br>
                    @endforeach
                </div>
            
                <script>
                    function showErrorNotification() {
                        const errorNotification = document.getElementById('errorNotification');
            
                        errorNotification.classList.remove('hidden');
            
                        setTimeout(() => {
                            errorNotification.classList.add('hidden');
                        }, 2000); 
                    }
                    showErrorNotification();
                </script>
            @endif
        
            <form action="{{ route('login') }}" method="POST">
                @csrf
                <h1>Sign In</h1>
                <section class="social-icons">
                    <a href="{{route('loginGoogle')}}" class="icon"><i class="fa-brands fa-google-plus-g"></i></a>
                </section>
                <span>or Sign In with:</span>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <a href="/forgot-password">Forget Your Password?</a>
                <button type="submit">Sign In</button>
            </form>
        </section>
       
        <section class="toggle-container">
            <section class="toggle">
                <section class="toggle-panel toggle-left">
                    <h1>Welcome Back!</h1>
                    <p>Enter your personal details to use all of site features</p>
                    <button class="hidden" id="login">Sign In</button>
                </section>
                <section class="toggle-panel toggle-right">
                    <h1>Hello, Friend!</h1>
                    <p>Register with your personal details to use all of site features</p>
                    <button class="hidden" id="register">Sign Up</button>
                </section>
            </section>
        </section>
    </section>
</div>

    <script src="js/loginsignup.js"></script>
</body>
</html>