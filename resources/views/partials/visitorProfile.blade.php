<script type="text/javascript" src={{ url('js/realUserProfile.js') }} defer> </script>
<link href="{{ url('css/profilePage.css') }}" rel="stylesheet">

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
        
<div class="profile-header">
    <div class="profile-info">
        <img src="{{ $imageUrl }}" alt="Profile Picture" class="profile-pic">
        <div class="profile-details">
            @if($user->authUser->premium)
                <p class="user-role">{{$user->username}} <i class="fa-solid fa-gavel fa-lg" style="color: #FFD43B;"></i> </p>
            @else
                <p class="user-role">{{$user->username}}</p>
            @endif
            <p class="user-contact">Phone: (+351) {{$user->phonenumber}}</p>
            <p class="user-email">Email: {{$user->email}}</p>

            @if ($reviews->isEmpty())
                No reviews yet
            @else
            @php
                $sum = 0;
                foreach ($reviews as $review) {
                    $sum += $review->rating;
                }
                $avg = floor($sum / $reviews->count());
            @endphp
                @switch($avg)
                    @case(1)
                        <p class="user-role">Rating: ★☆☆☆☆ </p>
                        @break
                    @case(2)
                        <p class="user-role">Rating: ★★☆☆☆ </p>  
                        @break
                    @case(3)
                        <p class="user-role">Rating: ★★★☆☆ </p>         
                        @break
                    @case(4)
                        <p class="user-role">Rating: ★★★★☆ </p>    
                        @break
                    @case(5)     
                        <p class="user-role">Rating: ★★★★★ </p>       
                        @break
                    @default
                        <p class="user-role">Error</p>
                @endswitch
            
            @endif
        </div>
    </div>
    <div class = "buttons-div">
        <button class="profile-edit-button" onclick="window.history.back()">Go Back</button>
    </div>
</div>     

<div class = "profile-nav">
    <button>
        Auctions Active and Future
    </button>
    <button style="display: none">
        Bid History
    </button>
    <button>
        Reviews
    </button>
</div>

<div class = "line"></div>

<div class = "profile-reviews">

    @if ($reviews->isEmpty())
        <p class = "no-reviews">No reviews yet</p>
    @else
        @foreach ($reviews as $review)
            <div class = "review">
                <div class = "review-header">
                    @php
                        $baseDirectory_ = base_path() . '/public/images/users/';

                        $imageFiles_ = glob($baseDirectory_ . $review->userReviewer->uid . '.*');
                        $imagePath_ = "";
                        if (!empty($imageFiles_)) {
                            $imagePath_ = $imageFiles_[0];
                        }

                        $fileExtension_ = pathinfo($imagePath_, PATHINFO_EXTENSION);

                        $imageUrl_ = File::exists($imagePath_) ? asset('images/users/' . $review->userReviewer->uid . '.' . $fileExtension_) : asset('images/svg/user.svg');
                    @endphp
                    <img src="{{ asset($imageUrl_) }}" alt="Profile Picture" class="review-pic">
                    <div class="review-details">
                        <h2 class="review-name">{{$review->userReviewer->uid}}</h2>
                        @switch(floor($review->rating))
                            @case(1)
                                <p class="review-rating">Rating: ★☆☆☆☆ </p>
                                @break
                            @case(2)
                                <p class="review-rating">Rating: ★★☆☆☆ </p>  
                                @break
                            @case(3)
                                <p class="review-rating">Rating: ★★★☆☆ </p>         
                                @break
                            @case(4)
                                <p class="review-rating">Rating: ★★★★☆ </p>    
                                @break
                            @case(5)     
                                <p class="review-rating">Rating: ★★★★★ </p>       
                                @break
                            @default
                                <p class="review-rating">Error</p>
                        @endswitch
                    </div>
                </div>
                <p class="review-text">{{$review->content}}</p>
            </div>
        @endforeach
    @endif
</div>

<div class = "user-auctions">
    @if ($auctions->isNotEmpty())
    @foreach ($auctions as $auction)
        <div class="auction-div" style="width:20%">
            <a href="{{ route('auction', ['id' => $auction['auction']->auctionid]) }}" class="router">
            @php
                $product = App\Models\Product::find($auction['auction']->productid);
                $image = 'images/svg/auction.svg';
                if ($product != null){
                    $images = $product->getImages($product->productid);
                    if (!empty($images)){
                        $image = $images[0];
                    }
                }
            @endphp
            <img src="{{ asset($image) }}" alt="Auction" class="auction-icon">
            <div class="auction-info">
                <h2 class="auction-title">{{ $auction["product"]->title }}</h2>
                <p class="auction-description">{{ $auction["product"]->description }}</p>
                @php
                    $topBid = $auction['topBid'];
                    $max = "No bids.";
                    if ($topBid && $topBid['amount'] != 0){
                        $max = $topBid['amount'] . " €";
                    }
                @endphp
                <p class="auction-price">Current Bid: {{ $max }}</p>
                <p class="auction-time" data-closedate="{{ $auction['auction']->closedate }}">Time Left: <span class="time-left"></span></p>
            </div>
        </div>
    </a>
    @endforeach
    @else
        <p>No auctions yet.</p>
    @endif
</div>
