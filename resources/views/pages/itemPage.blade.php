<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>BidIt Auction</title>
    <link href="{{ url('css/auction.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script type="text/javascript" src={{url('js/auctionPage.js')}} defer></script>
    <script type="text/javascript" src={{ url('js/deleteAuction.js') }} defer> </script>
    <script type="text/javascript" src={{url('js/addWatchlist.js')}} defer></script>
    <script type="text/javascript" src={{url('js/removeWatchlist.js')}} defer></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" defer></script>
</head>
<body>
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

    @include('partials.authTopBar')

    <div class="container">

        <div class="image-section">
            @if (count($images))
                <div id="image-container">
                    
                    <img id="current-image-itemPage" src="{{ asset($images[0]) }}" alt="Item Image">
                </div>
                <div class="arrows">
                    <span id="prev-arrow" onclick="showPreviousImage()">&larr;</span>
                    <span id="next-arrow" onclick="showNextImage()">&rarr;</span>
                </div>
        
                <script >
                    const images = @json($images);
                    let currentIndex = 0;
        
                    function showPreviousImage() {
                        currentIndex = (currentIndex - 1 < 0 ) ? images.length - 1: currentIndex - 1; 
                        updateImage();
                    }
        
                    function showNextImage() {
                        currentIndex = (currentIndex + 1 >= images.length) ? 0 : currentIndex + 1 ; 
                        updateImage();
                    }
        
                    function updateImage() {
                        let auctionId = window.location.pathname.split('/')[3]
                        const endpoint = `/home/bi-api/auctionspic/${auctionId}?photoNum=${currentIndex}`;

                        fetch(endpoint)
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('Failed to fetch image');
                                }
                                return response.blob(); // Retrieve the image as a Blob
                            })
                            .then(blob => {
                                const imageUrl = URL.createObjectURL(blob); // Convert Blob to an object URL
                                document.getElementById('current-image-itemPage').src = imageUrl;
                            })
                            .catch(error => {
                                console.error('Error fetching image:', error);
                                document.getElementById('current-image-itemPage').src = '/images/svg/auction.svg'; // Fallback image
                            });
                    }
                </script>
            @else
                <div id="image-container">
                    <img id="current-image-itemPage" src="{{ asset('placeholder.png') }}" alt="Item Image">
                </div>
            @endif
        </div>
        
        <div class="details-section">
            @if (Auth::check() && $user->userid != Auth::id() && !$auth_user->admin)
                <button class="top-right-button" onclick="showReportForm()"><i class="fa-solid fa-flag"></i></button>
            @endif
            @if($auction_owner->premium != null)
            <h1 id="product_name" data-premium=1>{{$product->title}} <i class="fa-solid fa-gavel" style="color: #FFD43B;"></i> </h1>
            @else 
            <h1 id="product_name" data-premium=0>{{$product->title}}</h1>
            @endif
            <p id="product_short_description">{{$product->description}}</p>
            <div class="time-left">
                <p>Time Left:</p>
                <p></p>
            </div>
            <div class="price-bid-section">              
                <p><span class="label">Closing Date:</span> <span class="value" id="close_date">{{$auction->closedate->format('Y-m-d H:i:s')}}</span></p>
                <p><span class="label">Current Bidding Amount:</span> <span class="value" id="value_auction">{{$auction->bids->max('amount')}}€ ({{$auction->bids->count()}} bids)</span></p>
                <p><span class="label">Shipping:</span> <span class="value">Free</span></p>
            </div>
            @if ($auth_user)
                @if (($auth_user->userid == $auction_owner->uid) || ($auth_user->admin))
                    @if($auction->bids->isEmpty() && $auction->closedate >= now())
                        <div class = "edit-buttons">
                            @if(!$auth_user->admin)
                                @csrf
                                <form class = "bid-section" action="{{route('updateAuction', ['userId' => Auth::id(), 'auctionId' => $auction->auctionid])}}" method="GET">
                                    @csrf
                                    <button type="submit">Edit Auction</button>
                                </form>
                            @endif

                            <form class="bid-section" id="bidForm" action="" method="POST">
                                @csrf
                                @if($auth_user->admin)
                                    <button type="submit" id="delete-auction" data-target={{Auth::id()}} data-admin=1>Close Auction</button>
                                @else
                                    <button type="submit" id="delete-auction" data-target={{Auth::id()}} data-admin=0>Close Auction</button>
                                @endif
                            </form>  
                        </div>
                    @endif
                @else
                        
                    <form class="bid-section" id="bidForm" action="{{ route('placeBid', ['userId' => $auth_user->userid, 'auctionId' => $auction->auctionid]) }}" method="POST">
                        @csrf
                        <input type="number" id="bid" name="value" min={{$auction->bids->max('amount') ? $auction->bids->max('amount') + $auction->initvalue * 0.05 : $auction->initvalue;}} step=0.01 required>
                        <button type="submit" id="submitButton">Submit Bid</button>
                        <button id="help-button">
                            <i class="fa-solid fa-question"></i>
                            <span class="tooltip">Enter a bid higher than the current bid. Minimum increments may apply</span>
                        </button>
                    </form> 
                    @if ($hasAuctionWatchlist)
                        <button type="submit" id="remove-watchlist-button" data-target={{Auth::id()}}><i class="fa-solid fa-heart"></i></button>
                    @else
                        <button type="submit" id="add-watchlist-button" data-target={{Auth::id()}}><i class="fa-regular fa-heart"></i></button>
                    @endif
                @endif
            @endif

            @php
                $sum = 0;
                $num = 0;
                foreach ($reviewsOnUser as $review) {
                    $num++;
                    $sum += $review->rating;
                }
                $averageRating = $num == 0 ? 0 : round((float)$sum / $num, 1);
                $str = $num == 0 ? "No Reviews" : sprintf("%.1f★ (%d reviews)", $averageRating, $num);
            @endphp
            <div class="seller-info" id = "seller-info">
                <img src="{{ $imageUrl }}" alt="Seller Image" id ="seller-img" data-target="{{$user->userid}}">
                <div>
                    <p>Auctioned by: {{$user->username}}</p>
                    <p>{{$str}}</p>
                </div>
            </div>
        </div>
    </div>

<div class="product-details-section">
  <h2>About the Product</h2>
    <div class="product-info">
        <div class="product-categories-auction">
            <h3>Category</h3>
            <ul id="categories">
                <li>{{$product_cat->categoryname}}</li>
            </ul>    
        </div>
        <div class="product-subcategories">
            <h3>Subcategories</h3>
            <ul id="sub-categories">
                <li>{{$product_subcat->subcategoryname}}</li>
            </ul>
        </div>
        <div class="product-additional-details">
            <h3>Additional Details</h3>
            <ul>
                <li id="state-product"><span class="product_state">Condition: </span>{{$product->state}}</li>
            </ul>
        </div>
    </div>
    <div class="product-description">
        <h3>Full Description</h3>
        <p id="full-description">{{$product->description}}</p>
    </div>
</div>

<div class="bid-history-section">
    <h2>Bid History</h2>
    <div class="bid-history">
        <ul id="bid-history_ul">
        </ul>
    </div>
</div>

@if(Auth::check() && count($reviews) == 0)
    @php
        $auth = Auth::user()->authUser;
        $id = -1;
        if ($auth != null){
            $id = $auth->authid;
        }
    @endphp
    @if ($auction->closedate < Carbon\Carbon::now() && $topBidderId == $id)
        <div class="review-container">
            <h2>Leave a Review!</h2>
            <div class="bid-history">
                <form id="review-form" action="{{ route('addReview', ['auctionId' => $auction->auctionid , 'userId' => Auth::user()->authUser->authid]) }}" method="POST">
                    @csrf
                    <div class="star-rating">  
                        <span class="star" data-value="1">&#9733;</span>
                        <span class="star" data-value="2">&#9733;</span>
                        <span class="star" data-value="3">&#9733;</span>
                        <span class="star" data-value="4">&#9733;</span>
                        <span class="star" data-value="5">&#9733;</span>
                    </div>
                    <input type="hidden" name="rating" id="rating" value="0">
                    <textarea name="review" id="review" cols="30" rows="10" placeholder="Write your review here..."></textarea>
                    <div id="char-counter" class="char-counter">0/500</div>
                    
                    <input type="hidden" name="reviewcount" value = {{count($reviews)}}>
                    <button type="submit">Submit Review</button>
                </form>
            </div>
        </div>
    @endif
@else
    @if (count($reviews) == 1)
        @php
            $review = $reviews[0];
        @endphp

        <div class="review-container">
            <h2>Review from the Buyer</h2>
            <div class="bid-history">
                <div class="user-image">
                    @php
                        $baseDirectory = base_path() . '/public/images/users/';
                       
                        $imageFiles = glob($baseDirectory . $review->userid . '.*');
                        $imagePath = "";
                        if (!empty($imageFiles)) {
                            $imagePath = $imageFiles[0];
                        } 

                        $fileExtension = pathinfo($imagePath, PATHINFO_EXTENSION);

                        $imageUrl = File::exists($imagePath) ? asset('images/users/' . $review->userid . '.' . $fileExtension) : asset('images/svg/user.svg');

                    @endphp
                    <img src="{{ asset($imageUrl) }}" alt="User Image" class="user-img" id ="review-img" data-target="{{$review->userid}}">
                </div>
                
                <div class="review-content">
                    <p class="review-text">{{ $review->content }}</p>
                    
                    <div class="rating">
                        <span class="rating-text">Rating: </span>
                        @for ($i = 1; $i <= 5; $i++)
                            <span class="star {{ $review->rating >= $i ? 'filled' : '' }}">&#9733;</span>
                        @endfor
                        
                    </div>
                    
                    <p class="review-date">Reviewed on: {{ \Carbon\Carbon::parse($review->reviewdate)->format('Y-m-d H:i:s') }}</p>
                </div>
            </div>
        </div>
    @endif
@endif

<?php
    if (!function_exists('geocodeAddress')) {
        function geocodeAddress($address) {

            $apiKey = getenv('LOCATION_IQ_KEY');

            $url = "https://us1.locationiq.com/v1/search.php?key=" . urlencode($apiKey) . "&q=" . urlencode($address) . "&format=json";

            $ch = curl_init();
        
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
            $response = curl_exec($ch);
        
            if ($response === false) {
                echo "cURL Error: " . curl_error($ch);
                curl_close($ch);
                return null;
            }
        
            curl_close($ch);

            $data = json_decode($response, true);
        
            if (!empty($data) && isset($data[0]['lat']) && isset($data[0]['lon'])) {
                $lat = $data[0]['lat'];
                $lon = $data[0]['lon'];
                return ['lat' => $lat, 'lon' => $lon];
            } else {
                return ['lat' => -82.8628, 'lon' => 135.0000];
            }
        }
    }
?>

<div class="map-container">
    <h2>Location of the Seller</h2>
    @php
        $address = $user->authUser->address;
        $coordinates = geocodeAddress($address);
    @endphp
    <input type="hidden" id="latitudine-hidden" value ="{{$coordinates['lat']}}">
    <input type="hidden" id="longitudine-hidden" value="{{$coordinates['lon']}}">
    <div id="map" style="height: 400px;"></div>
</div>

<div class="class-report hidden" id="report-form">
    <div class="popup-content">
        <h3>Report Reason</h3>
        <label>
            Reason: <input type="text" id="reason-report" placeholder="Enter reason">
        </label>
        <div class="popup-buttons">
            <button id="submit-report" onclick="sendReport({{$auction->auctionid}}, {{$user->userid}}, {{Auth::id()}})">Submit</button>
            <button id="close-report" onclick="hideReportForm()">Close</button>
        </div>
    </div>
</div>
    @include('partials.footer')

</body>
</html>