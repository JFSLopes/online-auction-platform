<script type="text/javascript" src={{ url('js/dashboard.js') }} defer> </script>

<section class = "main-information">
    <nav>
        <button class = "users-nav">
            <img src="{{url('images/svg/user.svg')}}" alt="User_Icon" class = "nav-icons">
            <div class = "buttons-text">
                <h2 class = "buttons-text-title">
                    Profile
                </h2>
                <h4 class = "buttons-text-subtitle">
                    User Information
                </h4>
                </div>
        </button>
        <button class = "active-bids">
            <img src="{{url('images/svg/auctionHammer2.svg')}}" alt="User_Icon" class = "nav-icons">
            <div class = "buttons-text">
            <h2 class = "buttons-text-title">
                Active Bids
            </h2>

            </div>
        </button>
        <button class = "items-won-nav">
            <img src="{{url('images/svg/trophy.svg')}}" alt="User_Icon" class = "nav-icons">
            <div class = "buttons-text">
            <h2 class = "buttons-text-title">
                Items won
            </h2>

            </div>
        </button>
    </nav>

    <div class = "information-div">
        <table class="user-information-table">
            <tr>
                <th>Username</th>
                <td>{{$user->username}}</td>
            </tr>
            <tr>
                <th>Email</th>
                <td>{{$user->email}}</td>    
            </tr>
            <tr>
                <th>Phone Number</th>
                <td>{{$user->phonenumber}}</td>
            </tr>
            <tr>
                <th>Address</th>
                <td>{{$auth->address}}</td>
            </tr>
        </table> 
        <!--
        <a href="{{route('editProfile', ['userId' => $user->userid])}}">
            <button class="edit-profile-button">
                <img src="{{url('images/svg/edit.svg')}}" alt="Edit Profile Icon">
            </button>
        </a>
        -->
    </div>

    <div class = "active-bids-div">
        <div class="active-bids-information">
            <table class="active-bids-table">
                @if (count($activeBids) > 0)
                    <thead>
                        <tr>
                            <th>Price</th>
                            <th>Expiration Date</th>
                            <th>Item</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                        @foreach ($activeBids as $activeBid)
                            <tr>
                                <td>{{$activeBid['topBid']}}</td>
                                <td>{{$activeBid['expiration_date']}}</td>
                                <td>{{$activeBid['item_name']}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                @else
                    <h1>No active bids</h1>
                @endif
            </table>
        </div>                    
    </div>

    <div class = "items-won-div">
        <div class="items-won-information">
            <table class="items-won-table">
                @if (count($wonItems) > 0)
                    <thead>
                        <tr>
                            <th>Price</th>
                            <th>Expiration Date</th>
                            <th>Item</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($wonItems as $wonItem)
                            <tr>
                                <td>{{$wonItem['price_paid']}}</td>
                                <td>{{$wonItem['expiration_date']}}</td>
                                <td>{{$wonItem['item_name']}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                @else
                    <h1>No items won</h1>            
                @endif   
            </table>
        </div>                    
    </div>
</section>
