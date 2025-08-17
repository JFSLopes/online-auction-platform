<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
        <title>{{ config('app.name', 'Profile Page') }}</title>

        <!-- Styles -->
        <link href="{{ url('css/app.css') }}" rel="stylesheet">
        <link href="{{ url('css/profilePage.css') }}" rel="stylesheet">
        <link href="{{ url('css/searchPage.css') }}" rel="stylesheet">
        <link href="{{ url('css/partials/productCategories.css') }}" rel="stylesheet">
    
        <script type="text/javascript" src={{ url('js/profile.js') }} defer> </script>
        <script type="text/javascript" src={{ url('js/searchPage.js') }} defer> </script>
        
        
    </head>

    @include('partials.authTopBar')

<div id="search-grid">
    <div id="search-left">
    <label><input type="search" placeholder="Search for ..." value="" id="search-bar" autocomplete="off"></label>
    <span class="help-icon">
        <i class="fa-solid fa-question"></i>
        <span class="tooltip">
        Full text search queries:<br>
        - <strong>!</strong>: Search term does not exist<br>
        - <strong>&</strong>: Logic AND<br>
        - <strong>|</strong>: Logic OR<br>
        Example: <code>!bike & mountain</code> -> Searches all items with 'mountain' in title<br>
        and description, that do not contain 'bike'.<br>
        Search is first made in titles, then in descriptions.
        </span>
    </span>
    <div id="message-search"></div>

    <div id="search-suggestions">
        <ul id="search-list-suggestions">

        </ul>
    </div>

    <!-- Categories partial view !-->
    @include('partials.productCategories', ['categories' => $categories, 'showNone' => true])

    <div id="search-filters">
        <form action="">
            <!-- Min value filed !-->
            <div class="filter-group">
                <label for="min_value">From:</label>
                <input type="number" id="min_value" min="0" max="10000000" value="0.00" step="0.01">
            </div>
            <!-- Max value field !-->
            <div class="filter-group">
                <label for="max_value">To:</label>
                <input type="number" id="max_value" min="0" max="10000000" value="10000000.00" step="0.01">
            </div>

            <!-- Load all the conditions !-->
            <div class="filter-group conditions-group">
                <span>Product Conditions:</span>
                @foreach ($conditions as $condition)
                    <label class="condition-product">
                        <input type="checkbox" name="condition[]" value="{{ $condition }}"> {{ $condition }}
                    </label>
                @endforeach
            </div>
            
            <!-- Start date !-->
            <div class="filter-group">
                <label for="start_datetime">Start Date & Time:</label>
                <input type="datetime-local" id="start_datetime" name="start_datetime" value="{{ old('start_datetime', now()->format('Y-m-d\TH:i')) }}" required>
            </div>
            
            <!-- End date !-->
            <div class="filter-group">
                <label for="end_datetime">End Date & Time:</label>
                <input type="datetime-local" id="end_datetime" name="end_datetime" value="{{ old('end_datetime', now()->addHours(1)->format('Y-m-d\TH:i')) }}" required>
            </div>
            
            <div class="filter-actions">
                <button type="submit" class="apply-filters">Apply Filters</button>
            </div>
        </form>
    </div>
</div>
    

<div id="search-right">
    <div id="search-auctions">
        @foreach ($auctions as $auction)
            <a href="{{ route('auction', ['id' => $auction->auctionid]) }}" class="text-decoration-a">
                <div class="auction-div">
                    @php
                        $baseDirectory = base_path() . '/public/images/items/';
                        $imageFiles = glob($baseDirectory . $auction->auctionid . '-*');
                        $imagePath = "";
                        if (!empty($imageFiles)) {
                            $imagePath = $imageFiles[0];
                        }
                        
                        $fileExtension = pathinfo($imagePath, PATHINFO_EXTENSION);
                        $imageUrl = File::exists($imagePath) ? asset('/images/items/' . $auction->auctionid . '-1.' . $fileExtension) : asset('images/svg/auction.svg');
                    @endphp
                    <img src="{{ asset($imageUrl) }}" alt="Auction" class="auction-icon">
                    <div class="auction-info">
                        <h2 class="auction-title">{{ $auction->title}}</h2>
                        <p class="auction-description">{{ $auction->description}}</p>
                        <p class="auction-price">Price: ${{ number_format($auction->initvalue, 2) }}</p>
                        <p class="auction-time">Start Date: {{ $auction->initdate }}</p>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
    

    <div id="search-pagination">
    </div>
</div>
</div>

@include('partials.footer')