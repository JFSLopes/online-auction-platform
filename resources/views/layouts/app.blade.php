<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Profile Page') }}</title>

    <!-- Styles -->
    <link href="{{ url('css/app.css') }}" rel="stylesheet">
    <link href="{{ url('css/partials/productCategories.css')}}" rel="stylesheet">

    <!-- Scripts -->
    <script type="text/javascript" src="{{ url('js/index.js') }}" defer></script>
    <script type="text/javascript" src="{{ url('js/profile.js') }}" defer></script>
    <script type="text/javascript" src="{{ url('js/navBar.js') }}" defer></script>
    <script type="text/javascript" src="{{ url('js/app.js') }}" defer></script>
</head>
<body>
    <main>
        @include('partials.authTopBar')

        <section class="main-information-app" id="main-information-app">
    
            <!-- Auction Table -->
            <table class="auction-table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th colspan="2" style="text-align: center; font-size: 1.5rem;">Featured</th>
                        <th colspan="3" style="text-align: center; font-size: 1.5rem;">Trending Auctions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <!-- Featured Auctions -->
                        <td colspan="2" style="vertical-align: top; text-align: center;">
                            <div class="premium-auctions-container">
                                @foreach ($premiumAuctions as $premiumAuction)
                                <a href="{{ route('auction', ['id' => $premiumAuction['auction']->auctionid]) }}" class="text-decoration-a">
                                    <div class="auction-div-app premium-hammer">
                                        @php
                                            $baseDirectory = base_path() . '/public/images/items/';
                                            $imageFiles = glob($baseDirectory . $premiumAuction['auction']->auctionid . '-*');
                                            $imagePath = "";
                                            if (!empty($imageFiles)) {
                                                $imagePath = $imageFiles[0];
                                            }
                                            
                                            $fileExtension = pathinfo($imagePath, PATHINFO_EXTENSION);
                                            $imageUrl = File::exists($imagePath) ? asset('/images/items/' . $premiumAuction['auction']->auctionid . '-1.' . $fileExtension) : asset('images/svg/auction.svg');
                                        @endphp
                                        <img src="{{ asset($imageUrl) }}" 
                                            alt="Item Image" class="current-image">
                                        <div class="auction-info-app">
                                            <h3>{{ $premiumAuction['product']->title }}</h3>
                                            <p>{{ $premiumAuction['product']->description }}</p>
                                            <p>Price: ${{ number_format($premiumAuction['auction']->initvalue, 2) }}</p>
                                            <p>Start Date: {{ $premiumAuction['auction']->initdate }}</p>
                                        </div>
                                    </div>
                                </a>
                                @endforeach
                            </div>
                        </td>

                        <!-- Trending Auctions -->
                        <td colspan="3" style="vertical-align: top; text-align: center;">
                            <div class="trending-auctions-container" style="display: flex; flex-wrap: wrap; gap: 20px; justify-content: center;">
                                @foreach ($featuredAuctions as $featuredAuction)
                                <a href="{{ route('auction', ['id' => $featuredAuction['auction']->auctionid]) }}" class="text-decoration-a">
                                    <div class="auction-div-app">
                                        @php
                                            $baseDirectory = base_path() . '/public/images/items/';
                                            $imageFiles = glob($baseDirectory . $featuredAuction['auction']->auctionid . '-*');
                                            $imagePath = "";
                                            if (!empty($imageFiles)) {
                                                $imagePath = $imageFiles[0];
                                            }
                                            
                                            $fileExtension = pathinfo($imagePath, PATHINFO_EXTENSION);
                                            $imageUrl = File::exists($imagePath) ? asset('/images/items/' . $featuredAuction['auction']->auctionid . '-1.' . $fileExtension) : asset('images/svg/auction.svg');
                                        @endphp
                                        <img src="{{ asset($imageUrl) }}" 
                                             alt="Item Image" class="current-image">
                                        <div class="auction-info-app">
                                            <h3>{{ $featuredAuction->title }}</h3>
                                            <p>{{ $featuredAuction->description }}</p>
                                            <p>Price: ${{ number_format($featuredAuction->initvalue, 2) }}</p>
                                            <p>Start Date: {{ $featuredAuction->initdate }}</p>
                                        </div>
                                    </div>
                                </a>
                                @endforeach
                            </div>
                        </td>
                    </tr>
                    <!-- Pagination
                    <tr>
                        <td colspan="5" style="text-align: center;">
                            <div id="premium-pagination" style="text-align: center; margin-top: 20px;">
                                <span onclick="applyPagePremium(1)">&laquo;</span>
                                @for ($i = 1; $i <= $numPagesPremiumAuctions; $i++)
                                    <span class="{{ $i == 1 ? 'active' : '' }}" onclick="applyPagePremium({{ $i }})">{{ $i }}</span>
                                @endfor
                                <span onclick="applyPagePremium({{ $numPagesPremiumAuctions > 1 ? 2 : 1 }})">&raquo;</span>
                            </div>
                        </td>

                        <td>
                            <div id="featured-pagination" style="text-align: center; margin-top: 20px;">
                                <span onclick="applyPageFeatured(1)">&laquo;</span>
                                @for ($i = 1; $i <= $numPagesFeaturedAuctions; $i++)
                                    <span class="{{ $i == 1 ? 'active' : '' }}" onclick="applyPageFeatured({{ $i }})">{{ $i }}</span>
                                @endfor
                                <span onclick="applyPageFeatured({{ $numPagesFeaturedAuctions > 1 ? 2 : 1 }})">&raquo;</span>
                            </div>
                        </td>
                    </tr>
                     -->
                </tbody>

            </table>

        </section>

        @include('partials.footer')
    </main>
    <input id="premium-page" type="hidden" value="2">
    <input id="featured-page" type="hidden" value="2">
</body>
</html>
