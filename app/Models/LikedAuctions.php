<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LikedAuctions extends Model
{
    use HasFactory;

    // Table Name
    protected $table = 'likedauction';

    // Primary Key Name
    protected $primaryKey = 'likeid';

    // Fill that must be specified during Insert (whitelist)
    protected $fillable = [
        'watchid',
        'auctionid'
    ];


    // Methods (Relationships)

    /**
     * Get the auction in a like auction entry
     */
    public function auction(): BelongsTo
    {
        return $this->belongsTo(Auction::class, 'auctionid', 'auctionid');
    }

    /**
     * Get the watchlist in a Liked auction entry
     */
    public function watchlist(): BelongsTo
    {
        return $this->belongsTo(Watchlist::class, 'watchid', 'watchid');
    }


    // Don't add create and update timestamps in database.
    public $timestamps  = false;
}
