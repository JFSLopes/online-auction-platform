<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/usersInfo.css') }}">
    <script type="text/javascript" src={{ url('js/adminActions.js') }} defer> </script>
    <title>{{ config('app.name', 'Admin Page - User List') }}</title>

    @php
        use Illuminate\Support\Facades\File;
    @endphp

</head>
<body>
    <main class="container">
        <button class="go-back" onclick="window.history.back()">Go Back</button>
        <section class="users">
            @foreach($users as $user)
                @if ($user->username != 'anonymous')
                    @php
                        $baseDirectory = base_path() . '/public/images/users/';

                        $imageFiles = glob($baseDirectory . $user->userid . '.*');
                        $imagePath = "";
                        if (!empty($imageFiles)) {
                            $imagePath = $imageFiles[0];
                        }

                        $fileExtension = pathinfo($imagePath, PATHINFO_EXTENSION);

                        $imageUrl = File::exists($imagePath) ? asset('images/users/' . $user->userid . '.' . $fileExtension) : asset('images/svg/user.svg');
                    @endphp
                    <div class="user-card">
                        <img src="{{ asset($imageUrl) }}" alt="Profile Picture" class="user-avatar">
                        <div class="user-name">{{ $user->username }}</div>
                        <div class="user-email">{{ $user->email }}</div>
                        <div class="actions">
                            <a href="{{route('seeUserProfileAdmin',['adminId' => Auth::id(),'userId' => $user->userid])}}" class="btn btn-view">View <i class="fa-solid fa-magnifying-glass"></i></a>
                            <a href="{{route('editProfileAdmin',['adminId' => Auth::id(),'userId' => $user->userid])}}" class="btn btn-edit">Edit <i class="fa-solid fa-pen-to-square"></i></a>
                            <button type="button" class="btn btn-suspend" data-target={{$user->userid}}>{{$user->authUser->isblocked ? 'Unblock' : 'Block'}} <i class="{{$user->authUser->isblocked ? "fa-solid fa-user-slash" : "fa-regular fa-user";}}"></i></button>
                            <button type="button" class="btn btn-delete" data-target={{$user->userid}}>Delete <i class="fa-solid fa-trash"></i></button>
                        </div>
                    </div>
                @endif
            @endforeach
        </section>
    </main>
</body>
</html>
