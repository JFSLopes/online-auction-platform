<link href = "{{ url('css/editProfile.css')}}" rel = "stylesheet">

<meta name="csrf-token" content="{{ csrf_token() }}">

<section class="edit-profile">
    <h2>Edit Profile</h2>
    <form action="{{ route('editProfileAdminAction' , ['userId' => $user->userid, 'adminId' => Auth::id()])  }}" method="POST" enctype="multipart/form-data"
        classs = "edit-profile-form" 
        id = "edit-profile-form">
        
        @csrf
        <div id="edit-image-profile">
            <img id = "user_photo" src="{{ $authuser->profilepic ? asset('images/users/' . $authuser->profilepic) : asset('images/svg/user.svg') }}"  alt="Profile Image">
            <div id = "buttons">
                <input type="file" id="photo-inputer" name="photos" accept="image/*">
            </div>
        </div>

        <div id="edit-user-details">
            <span>
                <label for="name"> Name:</label>
                <input type="text" id="name" name="username" placeholder="Enter your name" value = "{{$user->username}}">
            </span>


            <div class = "buttons">
                <button id="submitButton1" type="submit"> Save Changes</button>
                <button id ="resetButton"> Reset </button>
            </div>

        </div>
    </form>
</section>