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

        @include('partials.visitorProfile')

    </main>

    @include('partials.footer')
    
</body>
</html>
