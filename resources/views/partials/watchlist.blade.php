<script type="text/javascript" src={{url('js/removeWatchlist.js')}} defer></script>
<link href = "{{ url('css/watchlist.css')}}" rel = "stylesheet">
@php 
use Carbon\Carbon;
@endphp
<h2 id="watchlist-header">WatchList</h2>
<section id="watchlist-auctions">
    @if (!$likedAuctions->isEmpty())
        @foreach($likedAuctions as $auction)
            @php 
            $bids = $auction['auction']->bids;
            $amount = !$bids->isEmpty() ? sprintf("%d €", $bids->max('amount')) : "No bids";
            @endphp
                <div class = "watchlist-auction">
                        <a href="/home/auction/{{$auction['auction']->auctionid}}">
                            <div id = "auction-image-watchlist">
                                @php
                                    $baseDirectory = base_path() . '/public/images/items/';
                                    $imageFiles = glob($baseDirectory . $auction['auction']->auctionid . '-*');
                                    $imagePath = "";
                                    if (!empty($imageFiles)) {
                                        $imagePath = $imageFiles[0];
                                    }
                                    
                                    $fileExtension = pathinfo($imagePath, PATHINFO_EXTENSION);
                                    $imageUrl = File::exists($imagePath) ? asset('/images/items/' . $auction['auction']->auctionid . '-1.' . $fileExtension) : asset('images/svg/auction.svg');
                                @endphp
                                <img src="{{$imageUrl}}" alt="Item Image" width="100px">
                            </div>
                            <div id = "auction-details-watchlist">
                                <p><span class= "label">Product:</span>{{$auction['auction']->product->title}}</p>
                                <p><span class= "label">Current Bid:</span>{{$amount}} </p>
                                @if ($auction['auction']->initdate > Carbon::now())
                                    <p><span class= "label">Auction Starting:</span>{{$auction['auction']->initdate}}</p>
                                @else
                                    <p><span class= "label">Closing Date:</span>{{$auction['auction']->closedate}}</p>
                                @endif
                            </div>
                        </a>
                        <button type="submit" id="remove-watchlist-button" data-target={{Auth::id()}} data-auction={{$auction['auction']->auctionid}}><i class="fa-solid fa-trash"></i></button>
                </div>

        @endforeach
    @else
    <p>No auctions added.</p>
    @endif
</section>