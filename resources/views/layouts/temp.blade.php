<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Profile Page') }}</title>

        <!-- Styles -->
        <link href="{{ url('css/app.css') }}" rel="stylesheet">
        
        <link href="{{ url('css/profilePage.css') }}" rel="stylesheet">
    
        <script type="text/javascript" src={{ url('js/profile.js') }} defer> </script>
        
        
    </head>
    <body>
        <main>
            @include('partials.authTopBar')
            
            @include('partials.navBar')

            <section class = "main-information">
                
                @include('partials.profile')
                
            </section>
    
            @include('partials.footer')
        </main>
    </body>
</html>