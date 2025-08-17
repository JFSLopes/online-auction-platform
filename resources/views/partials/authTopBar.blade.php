<link href="{{ url('css/authTopBar.css') }}" rel="stylesheet">
<script type="text/javascript" src={{ url('js/authTopBar.js') }} defer></script>

<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="navbar">
    <div class="line-top-bar">
        <a href="/home" class="home-link">
            <div class="logo-container">
                <span class="logoLetters">
                    Bid It
                </span>
                <img src="{{ asset('images/svg/logo.svg') }}" alt="Logo" class="logo">
            </div>
        </a>

        <div class="search-container">
            <input type="text" placeholder="Search..." class="search-input" id="search-auth-bar" autocomplete="off">
            <button class="search-submit" onclick="performSearch()">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </div>

        <div class="nav-links">
            <a href="/home" class="nav-link">Home</a>
            <a href="/home/search" class="nav-link">Auctions</a>

            @if (Auth::check() == true && Auth::user()->admin == NULL)
                @if (request()->is('*/createAuction'))
                    <a href="#" class="nav-link">Create Auction</a>
                @else
                    <a href="{{ url('user/' . Auth::id() . '/createAuction') }}" class="nav-link">Create Auction</a>
                @endif
            @endif

            @if (Auth::check() == true && Auth::user()->admin != NULL)
                <a href="{{ url('admin/' . Auth::id()) }}" class="nav-link">Admin Page</a>
            @endif
        </div>

        @if (Auth::check() == true)
            @if(Auth::user()->admin == NULL)
                <div class="icons">
                    @php
                        // Fetch all notifications
                        $notifications = Auth::user()->getNotifications();
                        // Filter unseen notifications
                        $unseenNotifications = $notifications->filter(function ($notification) {
                            return !$notification->seen;
                        });
                    @endphp

                    <div class="notifications-container">
                        <a href="#" class="icon-link" id="notification-icon">
                            @if ($unseenNotifications->isEmpty())
                                <img src="{{ asset('images/svg/notification.svg') }}" alt="Notifications" class="icon">
                            @else
                                <img src="{{ asset('images/svg/notNotEmpty.svg') }}" alt="Notifications" class="icon">
                            @endif
                        </a>

                        <div class="notification-tooltip">
                            @if ($unseenNotifications->isEmpty())
                                <p>No notifications</p>
                            @else
                                <ul class="notification-list">
                                    @foreach ($unseenNotifications as $notification)
                                        <li class="notification">
                                            @if ($notification->auctionid != null)
                                                <a href="{{ route('auction', ['id' => $notification->auctionid]) }}" class="notification-link" data-target = "{{$notification->notid}}">
                                            @else
                                                @if ($notification->type == 'Message')
                                                    <a href="{{ route('showMessages', ['userId' => Auth::id()]) }}" class="notification-link" data-target = "{{$notification->notid}}">
                                                @else
                                                    <a href="/home/profile/{{Auth::id()}}">
                                                @endif
                                            @endif
                                                <div class="notification-box {{ $notification->seen ? 'clicked' : '' }}">
                                                    
                                                    <p class="notification-content">{{ $notification->content }}</p>
                                                    <p class="notification-date">{{ $notification->sentdate }}</p>

                                                    <form class = "seen-form" action="{{route('updateNotificationSeenStatus' , ['notificationid' => $notification->notid , 'userid' => Auth::id()]) }}" method="POST" >
                                                        @csrf
                                                        <button class = "seen-button" >Mark as seen</button>
                                                    </form>
                                                </div>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>

                    <div class="balance-container">
                        <a href="{{ route('showAddFunds', ['userId' => Auth::id()]) }}" class="icon-link">
                            <img src="{{ asset('images/svg/coin.svg') }}" alt="Coins" class="icon">
                        </a>
                        <div class="balance-tooltip">
                            @if (Auth::check())
                                @php
                                    //fetch the balance of the user
                                    $balance = Auth::user()->getBalance();
                                    $availableBalance = Auth::user()->getAvailableBalance();
                                @endphp
                                <p>Total balance: {{ $balance }}$</p>
                                <p>Current available balance: {{ $availableBalance }}$</p>
                            @else
                                <p>Please log in to view your balance.</p>
                            @endif

                            <div class="buttons">
                                <a href="{{ route('showAddFunds', ['userId' => Auth::id()]) }}" class="icon-link">
                                    <button class="deposit">Deposit</button>
                                </a>

                                <a href="{{ route('showWithdrawFunds', ['userId' => Auth::id()]) }}" class="icon-link">
                                    <button class="withdraw">Withdraw</button>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="message" id="message-icon">
                        <a href="{{ route('showMessages', ['userId' => Auth::id()]) }}" class="icon-link">
                            <img src="{{ asset('images/svg/message.svg') }}" alt="Messages" class="icon">
                        </a>
                    </div>
                </div>

        @endif
            
            @if (Auth::check() == true && Auth::user()->admin != NULL)
                <form action="{{ url('user/' . Auth::id() . '/logout') }}" method="POST">
                    @csrf
                    <button type="submit" id = "logout-button-topbar"> Logout </button>
                </form>
            @else
                <a href="{{ '/home/profile/' . Auth::id() }}" class="icon-link" id="last-icon">
                    <img src="{{ asset('images/svg/user.svg') }}" alt="User Profile" class="icon">
                </a>
            @endif
        @else
            <div class="icons">
                <a href="/login" class="sign-up-link">
                    <button class="login-button" id = "login-button">
                        Login
                    </button>
                </a>
            </div>
        @endif
    </div>
</div>
