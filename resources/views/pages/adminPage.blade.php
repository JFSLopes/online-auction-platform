<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/adminPage.css') }}">
    <script src="{{asset('js/adminPage.js')}}" defer></script>
    <title>{{ config('app.name', 'Admin Page') }}</title>
</head>
<body>

    @include('partials.authTopBar')

    <main class="container-admin">
        <header>
            <h1 class="heading">Administrator Dashboard</h1>
        </header>
        <section class="options">
            <a class="option" id="toggle-search-bar">
                <i class="fas fa-search" aria-hidden="true"></i>
                <span>Search User Accounts</span>
            </a>

            <form class="search-bar option" id="searchBar" method="GET" action="{{route('searchUserAccounts',['adminId' => Auth::id()])}}">
                <input type="text" name="searchQuery" placeholder="Search users..." />
                <button type="submit">Search</button>
            </form>
        
            <a href="{{route('searchUserAccounts', ['adminId' => Auth::id()])}}" class="option">
                <i class="fas fa-users" aria-hidden="true"></i>
                <span>View All Users</span>
            </a>

            <a class="option" id="toggle-register-user-form">
                <i class="fas fa-user-plus" aria-hidden="true"></i>
                <span>Register New User</span>
            </a>
            <div id="register-user-form" style="display: none;">
                <h2>Register New Account</h2>
                <form class="admin-form" method="POST" action="{{ route('adminRegisterUser' , ['adminid' => Auth::user()->admin ]) }}">
                    @csrf
                    <label for="name" class="form-label">Name</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus class="form-input">
                    @if ($errors->has('name'))
                        <span class="error">{{ $errors->first('name') }}</span>
                    @endif

                    <label for="email" class="form-label">E-Mail Address</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required class="form-input">
                    @if ($errors->has('email'))
                        <span class="error">{{ $errors->first('email') }}</span>
                    @endif

                    <label for="password" class="form-label">Password</label>
                    <input id="password" type="password" name="password" required class="form-input">
                    @if ($errors->has('password'))
                        <span class="error">{{ $errors->first('password') }}</span>
                    @endif

                    <label for="password-confirm" class="form-label">Confirm Password</label>
                    <input id="password-confirm" type="password" name="password_confirmation" required class="form-input">

                    <label for="address" class="form-label">address</label>
                    <input id="address" type="text" name="address" value="{{ old('address') }}" required class="form-input">
                    @if ($errors->has('address'))
                        <span class="error">{{ $errors->first('address') }}</span>
                    @endif

                    <label for="phone" class="form-label">Phone</label>
                    <input id="phone" type="text" name="phone" value="{{ old('phone') }}" required class="form-input">
                    @if ($errors->has('phone'))
                        <span class="error">{{ $errors->first('phone') }}</span>
                    @endif

                    <button type="submit" class="form-submit">Register</button>
                </form>
            </div>

            <!-- Register New Admin -->
            <a class="option" id="toggle-register-admin-form">
                <i class="fas fa-user-shield" aria-hidden="true"></i>
                <span>Register New Admin</span>
            </a>
            <div id="register-admin-form" style="display: none;">
                <h2>Register New Admin</h2>   
                <form class="admin-form" method="POST" action="{{ route('adminRegisterAdmin' , ['adminid' => Auth::user()->admin ]) }}">
                    @csrf
                    <label for="name" class="form-label">Name</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus class="form-input">
                    @if ($errors->has('name'))
                        <span class="error">{{ $errors->first('name') }}</span>
                    @endif

                    <label for="email" class="form-label">E-Mail Address</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required class="form-input">
                    @if ($errors->has('email'))
                        <span class="error">{{ $errors->first('email') }}</span>
                    @endif

                    <label for="password" class="form-label">Password</label>
                    <input id="password" type="password" name="password" required class="form-input">
                    @if ($errors->has('password'))
                        <span class="error">{{ $errors->first('password') }}</span>
                    @endif

                    <label for="password-confirm" class="form-label">Confirm Password</label>
                    <input id="password-confirm" type="password" name="password_confirmation" required class="form-input">
                    
                    <label for="phone" class="form-label">Phone</label>
                    <input id="phone" type="text" name="phone" value="{{ old('phone') }}" required class="form-input">
                    @if ($errors->has('phone'))
                        <span class="error">{{ $errors->first('phone') }}</span>
                    @endif

                    <button type="submit" class="form-submit">Register</button>
                </form>
            </div>

            <a href="{{route('seeUnblockRequests', ['adminId' => Auth::id()])}}" class="option">
                <i class="fa-solid fa-bell-concierge" aria-hidden="true"></i>
                <span>See all Unblock Requests and Reports</span>
            </a>

            <a href="{{route('seeCategories', ['adminId' => Auth::id()])}}" class="option">
                <i class="fa-solid fa-list" aria-hidden="true"></i>
                <span>Manage Categories</span>
            </a>

            <a href="/home" class="option">
                <i class="fa-solid fa-house" aria-hidden="true"></i>
                <span>Go Back to Homepage</span>
            </a>
        </section>
    </main>

    @include('partials.footer')
    
</body>
</html>
