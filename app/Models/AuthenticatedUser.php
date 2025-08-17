<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Collection;

class AuthenticatedUser extends Model
{
    use HasFactory;

    // Table Name
    protected $table = 'authenticateduser';

    // Primary Key Name
    protected $primaryKey = 'authid';

    // Fill that must be specified during Insert (whitelist)
    protected $fillable = [
        'address',
        'registerdate',
        'uid'
    ];

    // Atributtes that must be hidden for serialization
    protected $hidden = [
        'balance',
        'address'
    ];

    protected $casts = [
        'isblocked' => 'boolean', // Cast 'isBlocked' to a boolean
    ];

    // Methods (Relationships)

    /**
     * Get the user id through the authUser id
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(Users::class, 'uid', 'userid');
    }

    /**
     * Get the user id through the authUser id
     */
    public function premium(): HasOne
    {
        return $this->hasOne(Premium::class, 'authid', 'authid');
    }

    /**
     * Get all reviews from a User
     */
    public function reviewsFrom(): HasMany
    {
        return $this->hasMany(Review::class, 'authidreviewer', 'authid');
    }

    /**
     * Get all products from a User
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'authid', 'authid');
    }

    /**
     * Get all user notifications
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'authid', 'authid');
    }

    /**
     * Get all user messages
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Messages::class, 'senderid', 'authid');
    }

    /**
     * Get the watchlist for a auth user
     */
    public function watchlist(): HasOne
    {
        return $this->hasOne(WatchList::class, 'authid', 'authid');
    }

    /**
     * Get all user bids
     */
    public function bids(): HasMany
    {
        return $this->hasMany(Bid::class, 'authid', 'authid');
    }


    // ------------------------ Methods (Not Relationships) ---------------------------------------- //
    /**
     * Get all reviews that review a user
     */
    public function reviewsTo(): Collection
    {
        $products = $this->products;

        $reviews = new Collection();

        foreach ($products as $product){
            $auction = $product->auction;

            if ($auction){
                $review = $auction->review;
                if ($review){
                    $authReview = $review->authidreviewer;
                    $authUser = AuthenticatedUser::find($authReview);
                    $review->uid = $authUser->uid;
                    $reviews->push($review);
                }

            }
        }
        return $reviews;
    }

    public function getReviewsOnUserAndReviewer() : Collection
    {
        $reviews = $this->reviewsTo();
        
        $reviewsOnUserAndReviewer = new Collection();



        foreach ($reviews as $review){

            $reviewerId = $review->authidreviewer;

            $reviewer = AuthenticatedUser::find($reviewerId);

            $matriz = [
                'review' => $review,
                'reviewer' => $reviewer
            ];
            
            $reviewsOnUserAndReviewer->push($matriz);
        }

        return $reviewsOnUserAndReviewer;
    }

    /**
     * Get all the auction the user is following
     */
    public function likedAuctions(){
        $watchlist = $this->watchlist;
        $auctions = new Collection();
        
        if ($watchlist){
            $likedAuctions = $watchlist->likedAuctions;

            foreach ($likedAuctions as $likedAuction){
                $auction = $likedAuction->auction;

                if ($auction){
                    $auctions->push($auction);
                }
            }
        }
        // unique() to remove duplicates
        return $auctions->unique('auctionid');
    }

    public function personalAuctions(){
        $products = $this->products;
        $auctions = new Collection();

        foreach ($products as $product){
            $auction = $product->auction;
            
            if ($auction){
                $bid = $auction->getTopBid();

                $product = Product::find($auction->productid);


                $matriz = [
                    'topBid' => $bid,
                    'timeToClose' => $auction->closedate,
                    'auction' => $auction,
                    'product' => $product,
                    'mainImage' => $product->getMainImage($product->productid)
                ];
                $auctions->push($matriz);
            }
        }

        return $auctions;       
    }

    public function getActiveTopBids(){
        $bids = $this->bids;

        $topBids = new Collection();

        foreach ($bids as $bid){
            $auction = $bid->auction;
            if ($auction && $auction->isActive() && $auction->isTopBid($bid)){
                $matriz = [
                    'topBid' => $bid,
                    'auction' => $auction,
                ];
                $topBids->push($matriz);
            }
        }

        return $topBids;
    }

    //valor , data de expiração e nome do produto
    public function getDashboardBids(){
        $bids = $this->bids;
        $topBids = new Collection();
        
        foreach ($bids as $bid){
            $auctionid = $bid->auctionid;

            $auction = Auction::find($auctionid);
            $product = Product::find($auction->productid);

            if ($auction && $auction->isActive() && $auction->isTopBid($bid)){

                $matriz = [
                    'expiration_date' => $auction->closedate,
                    'topBid' => $bid->amount,
                    'item_name' => $product->title,
                ];
                                
                $topBids->push($matriz);
            }
        }

        return $topBids;
    }

    public function getNotifications(){
        $notifications = $this->notifications;
        $auctions = new Collection();

        foreach ($notifications as $notification){
            $auction = $notification->auction;

            if ($auction){
                $auctions->push($auction);
            }
        }

        return $auctions;
    }

    public function getDashboardWonItems(){
        $bids = $this->bids;
        $auctions = new Collection();

        foreach ($bids as $bid){
            $auction = $bid->auction;

            $auction = Auction::find($bid->auctionid);   
            $product = Product::find($auction->productid);

            if ($auction && $auction->isClosed() && $auction->isTopBid($bid)){
                $matriz = [
                    'expiration_date' => $auction->closedate,
                    'price_paid' => $bid->amount,
                    'item_name' => $product->title,
                ];
                $auctions->push($matriz);
            }
        }
        return $auctions;
    }

    public function getAuctionsWhereUserIsTopBidder(){
        $bids = $this->bids;
        $auctions = new Collection();

        foreach ($bids as $bid){
            $auction = $bid->auction;

            if ($auction && $auction->isTopBid($bid)){

                $product = Product::find($auction->productid);

                $matriz = [
                    'topBid' => $bid,
                    'auction' => $auction,
                    'product' => $product
                ];

                $auctions->push($matriz);
            }
        }
        return $auctions;
    }

    // Don't add create and update timestamps in database.
    public $timestamps  = false;
}
