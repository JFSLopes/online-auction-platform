<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class Auction extends Model
{
    use HasFactory;

    // Table Name
    protected $table = 'auction';

    // Primary Key Name
    protected $primaryKey = 'auctionid';

    // Fill that must be specified during Insert (whitelist)
    protected $fillable = [
        'initdate',
        'closedate',
        'initvalue',
        'state',
        'authid',
        'productid'
    ];

    protected $casts = [
        'initdate' => 'datetime',
        'closedate' => 'datetime',
    ];


    // Methods (Relationships)

    /**
     * Get auction that the review refers to
     */
    public function review(): HasOne
    {
        return $this->hasOne(Review::class, 'auctionid', 'auctionid');
    }

    /**
     * Get product associated with auction
     */
    public function product(): HasOne
    {
        return $this->hasOne(Product::class, 'productid', 'productid');
    }

    /**
     * Get all watchlist tracking the auction
     */
    public function watchList(): HasMany
    {
        return $this->hasMany(LikedAuctions::class, 'watchid', 'watchid');
    }

    /**
     * Get all the liked auctions entries for that auction
     */
    protected function likedAuctions(): HasMany
    {
        return $this->hasMany(LikedAuctions::class, 'auctionid', 'auctionid');
    }

    /**
     * Get all messages from a auction
     */
    protected function messages(): HasMany
    {
        return $this->hasMany(Messages::class, 'auctionid', 'auctionid');
    }

    /**
     * Get all messages from a auction
     */
    public function bids(): HasMany
    {
        return $this->hasMany(Bid::class, 'auctionid', 'auctionid');
    }

    /**
     * Get all notifications associated with a auction
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'auctionid', 'auctionid');
    }

    // Methods (Not Relationships)

    /**
     * Get all the auth users following the auction
     */
    public function userFollowingAuction(){
        $likedAuctions = $this->likedAuctions;
        $authUsers = new Collection();
        
        foreach ($likedAuctions as $likedAuction){
            $watchlist = $likedAuction->watchList;

            if ($watchlist){
                $authUser = $watchlist->authUser;

                if ($authUser){
                    $authUsers->push($authUser);
                }
            }
        }
        
        // unique() to remove duplicates
        return $authUsers->unique('authid');
    }

    public function isActive(){
        $now = now();
        return $now->between($this->initdate, $this->closedate);
    }

    public function isClosed(){
        $now = now();
        return $now->gt($this->closedate);
    }

    public function isTopBid($bid){
        $topBid = $this->getTopBid();
        return $topBid['amount'] == $bid->amount;
    }

    public function getTopBid(){
        $bids = DB::select('SELECT * FROM bid WHERE auctionid = ? ORdER BY bid.amount DESC LIMIT 1' , [$this->auctionid]);
        // If there are no bids, return null
        if (empty($bids)) {
            return null;
        }

        // Get the highest bid details
        $bid = $bids[0];

        // Return a collection with 'amount' and 'authid'
        return collect([
            'amount' => $bid->amount,
            'authid' => $bid->authid,
        ]);
    }

    public function getTopBidder(){
        $topBid = $this->getTopBid();

        if(!$topBid || $topBid == $this->initvalue){
            return null;
        }

        $userid = $topBid['authid']; 

        return $userid;
    }

    public function getReviewOnAuction(){
        $review = DB::select('SELECT review.*, users.userid
                             FROM review 
                             JOIN authenticateduser ON review.authidreviewer = authenticateduser.authid
                             JOIN users ON authenticateduser.uid = users.userid
                             WHERE auctionid = ?', [$this->auctionid]);

        return $review;
    }

    // Don't add create and update timestamps in database.
    public $timestamps  = false;
}
