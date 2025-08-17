<script type="text/javascript" src={{ url('js/profile.js') }} defer> </script>
<script type="text/javascript" src={{ url('js/realUserProfile.js') }} defer> </script>
<script type="text/javascript" src={{ url('js/deleteAuction.js') }} defer> </script>

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
            <h2 class="user-name">{{$user->username}}  <i class="fa-solid fa-gavel fa-lg" style="color: #FFD43B;"></i> </h2>
            @else
            <h2 class="user-name">{{$user->username}}</h2>
            @endif
            <p class="user-role">Balance: {{$auth->balance}}</p>
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
            
            @endif
        </div>
    </div>
    <div class = "buttons-div">
        <button class="profile-edit-button" data-target = {{{$user->userid}}}>Edit Profile</button>
        <button class="balance-button" data-target = {{{$user->userid}}}>Add Balance</button>
        <button class="withdraw-balance-button" data-target = {{{$user->userid}}}>Withdraw</button>
    </div>
</div>     

<div class = "profile-nav">
    <button>
        Auctions Active and Future
    </button>
    <button>
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

                @php

                $baseDirectory = base_path() . '/public/images/users/';

                $imageFiles = glob($baseDirectory . $review->uid . '.*');
                $imagePath = "";
                if (!empty($imageFiles)) {
                    $imagePath = $imageFiles[0];
                } 
            
                $fileExtension = pathinfo($imagePath, PATHINFO_EXTENSION);
            
                $imageUrl = File::exists($imagePath) ? asset('images/users/' . $review->uid . '.' . $fileExtension) : asset('images/svg/user.svg');
            
                @endphp

            <div class = "review">
                <div class = "review-header">
                    <img src="{{ asset($imageUrl) }}" alt="Profile Picture" class="review-pic">
                    <div class="review-details">
                        <h2 class="review-name">{{$review->username}}</h2>
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

<div class = "user-bids">

    @if ($auctionsWhereUserIsTopBidder->isEmpty())
        <p class="no-bids">No active bids</p>
    @else 
        @foreach ($auctionsWhereUserIsTopBidder as $auction_bid)
            <div class="bid-div" style="width:20%">
                <a href="{{ route('auction', ['id' => $auction_bid['auction']->auctionid]) }}" class="router">
                    @php
                        $product = App\Models\Product::find($auction_bid['auction']->productid);
                        $image = 'images/svg/auction.svg';
                        if ($product != null){
                            $images = $product->getImages($product->productid);
                            if (!empty($images)){
                                $image = $images[0];
                            }
                        }
                    @endphp
                    <img src="{{ asset($image) }}" alt="Auction" class="auction-icon">
                    <div class="bid-info">
                        <h2 class="bid-title">{{ $auction_bid['product']->title }}</h2>
                        <p class="bid-description">{{ $auction_bid['product']->description }}</p>
                        <p class="bid-price">Current Bid: ${{ $auction_bid['topBid']->amount }}</p>
                        <p class="auction-time" data-closedate="{{ $auction_bid['auction']->closedate }}">Time Left: <span class="time-left"></span></p>
                    </div>
                </a>
            </div>
        @endforeach
    @endif
</div>

<div class = "user-auctions">
    @if ($auctions->isNotEmpty())
    @foreach ($auctions as $auction)
        <div class="auction-div">
            <a href="{{ route('auction', ['id' => $auction['auction']->auctionid]) }}" class="router">
                @php
                    $product_ = App\Models\Product::find($auction['auction']->productid);
                    $image_ = 'images/svg/auction.svg';
                    if ($product_ != null){
                        $images_ = $product_->getImages($product_->productid);
                        if (!empty($images_)){
                            $image_ = $images_[0];
                        }
                    }
                    $topBid = $auction['topBid'];
                    $max = "No bids.";
                    if ($topBid && $topBid['amount'] != 0){
                        $max = $topBid['amount'] . " €";
                    }
                @endphp
                <img src="{{ asset( $image_ ) }}" alt="Auction" class="auction-image">
                <div class="auction-info">
                    <h2 class="auction-title">{{ $auction['product']->title }}</h2>
                    <p class="auction-description">{{ $auction['product']->description }}</p>
                    <p class="auction-price">Current Bid: {{ $max}} </p>
                    <p class="auction-time" data-closedate="{{ $auction['auction']->closedate }}">Time Left: <span class="time-left"></span></p>
                    </p>
                </div>
            </a>
            @if (!$auction['auction']->bids)
                <div class = "forms">
                    <form id = "bid-section-id" class = "bid-section" action="{{route('updateAuction', ['userId' => Auth::id(), 'auctionId' => $auction['auction']->auctionid])}}" method="GET">
                        @csrf
                        <button type="submit">Edit Auction</button>
                    </form>

                    <form id = "bid-section-id" class="bid-section" id="bidForm" action="" method="POST">
                        @csrf
                        <button type="submit" id="delete-auction" data-target={{Auth::id()}} data-auction={{$auction['auction']->auctionid}}>Close Auction</button>
                    </form>  
                </div>
            @endif
        </div>
    @endforeach
    @else
        <p>No auctions yet.</p>
    @endif
</div>
