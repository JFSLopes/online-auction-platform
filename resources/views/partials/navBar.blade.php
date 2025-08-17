<link href="{{ url('css/navBar.css') }}" rel="stylesheet">
       
<section id="content">
    <nav id="nav-bar">
        <input id="nav-toggle" type="checkbox">
        <div id="nav-header">
            <a id="nav-title" href="#" target="_blank">
                HomePage
            </a>
            <label for="nav-toggle">
                <span id="nav-toggle-burger"></span>
            </label>
            <hr>
        </div>
        <div id="nav-content">
            <div class="nav-button" data-target="dashboard">
                <img class="menu-icon" src="{{ url('images/svg/home.svg') }}" alt="Dashboard Icon">
                <span>Dashboard</span>
            </div>
            <div class="nav-button" data-target="profile">
                <img class="menu-icon" src="{{ url('images/svg/user.svg') }}" alt="Profile Icon">
                <span>Personal Profile</span>
            </div>
            <hr>
            
            <div class="nav-button" data-target="watchlist">
                <img class="menu-icon" src="{{ url('images/svg/heart.svg') }}" alt="Watchlist Icon">
                <span>Watchlist</span>
            </div>

            <div class="nav-button" data-target="premium">
                <img class="menu-icon" src="{{ url('images/svg/premium-badge.svg') }}" alt="Information Icon">
                <span>Premium</span>
            </div>
            <hr>
            <form id="logout-form" action="/user/{{$user->userid}}/logout"  method="post">    
                @csrf
                <div class="nav-button" id = "logout-button">
                    <img class="menu-icon" src="{{ url('images/svg/logout.svg') }}" alt="Logout Icon">
                    <span>Logout</span>
                </div>
            </form>
            
            <div id="nav-content-highlight"></div>
            
        </div>
        <input id="nav-footer-toggle" type="checkbox">
    </nav>
</section>
