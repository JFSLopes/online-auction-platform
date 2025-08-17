<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Unblock Request</title>

    <!-- Styles -->
    <link href="{{ url('css/unblock.css') }}" rel="stylesheet">
</head>
<body>
    <main class="unblock-container">
        <div class = "sub-unblock-container">
            <div class="unblock-header">
                <img src="{{ asset('images/blocked/blocked.svg') }}" alt="Blocked" class="blocked-image">
                <p class="unblock-info">If you believe this was a mistake, you can request to be unblocked by filling out the form below.</p>
            </div>

            <div class="unblock-form-container">
                <form action="{{ url('/user/' . Auth::id() . '/unblock') }}" method="POST" class="unblock-form">
                    @csrf

                    <div class="form-group">
                        <label for="content" class="form-label">Reason for Unblocking</label>
                        <textarea name="content" id="content" class="form-textarea" rows="4" maxlength="150" required></textarea>
                    </div>

                    <div class="form-submit">
                        <button type="submit" class="submit-button">Submit Request</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="option">
            <button type='button' onclick="goBackLogin()"><i class="fa-solid fa-house" aria-hidden="true"></i> Go Back To Login</button>
        </div>
    </main>

    @include('partials.footer')

    <script>
        @if (session('message'))
            alert("{{ session('message') }}");
        @endif
    </script>

    <script>
        function clearSessionCookie(cookieName) {
            document.cookie = `${cookieName}=; path=/; expires=Thu, 01 Jan 1970 00:00:00 UTC;`;
        }

        function goBackLogin(){
            clearSessionCookie("bi_session");
            document.location.href = "/login?deleteCookie=1";
        }
    </script>
</body>
</html>
