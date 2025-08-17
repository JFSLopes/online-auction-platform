<link href = "{{ url('css/editProfile.css')}}" rel = "stylesheet">
<script src="{{ url('js/editProfile.js') }}" defer></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>

<meta name="csrf-token" content="{{ csrf_token() }}">

@php
    use Illuminate\Support\Facades\File;

    $baseDirectory = base_path() . '/public/images/users/';

    $imageFiles = glob($baseDirectory . $user->userid . '.*');
    $imagePath = "";
    if (!empty($imageFiles)) {
        $imagePath = $imageFiles[0];
    } 

    $fileExtension = pathinfo($imagePath, PATHINFO_EXTENSION);

    $imageUrl = File::exists($imagePath) ? asset('images/users/' . $user->userid . '.' . $fileExtension) : asset('images/svg/user.svg');
@endphp

<section class="edit-profile">
    <h2>Edit Profile</h2>
    <form action="{{ route('editProfile' , ['userId' => Auth::id()])  }}" method="POST" enctype="multipart/form-data"
        classs = "edit-profile-form" 
        id = "edit-profile-form">
        
        @csrf
        <div id="edit-image-profile">
            <img id = "user_photo" src="{{ $imageUrl }}"  alt="Profile Image">
            <div id = "buttons">
                <input type="file" id="photo-inputer" name="photos" accept="image/*">
            </div>
        </div>

        <div id="edit-user-details">
            <span class="group">
                <label for="name"> Name:</label>
                <input type="text" id="name" name="username" placeholder="Enter your name" value = "{{$user->username}}">
                <span class="help-icon">
                    <i class="fa-solid fa-question"></i>
                    <span class="tooltip">Define your new username.</span>
                </span>
            </span>
            <span class="group">
                <label for="phone"> New Phone Number: </label>
                <input type="text" id="phone" name="phonenumber" placeholder="+351 ..." value = "{{$user->phonenumber}}">
                <span class="help-icon">
                    <i class="fa-solid fa-question"></i>
                    <span class="tooltip">Define your phone number, you can include the country code prefix.</span>
                </span>
            </span>
            <span class="group">
                <label for="email"> New Email:</label>
                <input type="email"id="email" name="email" placeholder="youremail@example.com" value = "{{$user->email}}">
                <span class="help-icon">
                    <i class="fa-solid fa-question"></i>
                    <span class="tooltip">Define your new email.</span>
                </span>
            </span>
            <span class="group">
                <label for="password">New password:</label>
                <input type="password" id="password" name="password" placeholder="Password">
                <span class="help-icon">
                    <i class="fa-solid fa-question"></i>
                    <span class="tooltip">Define the new password.</span>
                </span>
            </span>
            <span class="group">
                <label for="password-confirmation">Confirm password:</label>
                <input type="password" id="password-confirmation" name="password-confirmation" placeholder="Confirm password">
                <span class="help-icon">
                    <i class="fa-solid fa-question"></i>
                    <span class="tooltip">Insert the password defined aboved.</span>
                </span>
            </span>
            <span class="group">
                <label for="address">New Address:</label>
                <input type="text" id="address" name="address" placeholder="Enter your address" value = "{{$authuser->address}}">
                <span class="help-icon">
                    <i class="fa-solid fa-question"></i>
                    <span class="tooltip">Define your address. We will show an approximate location based on the address provided.</span>
                </span>
            </span>


            <div class = "buttons">
                <button id="submitButton1"> Save Changes</button>
                <button id ="resetButton"> Reset </button>
            </div>

        </div>
    </form>
</section>