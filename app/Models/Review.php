<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    // Table Name
    protected $table = 'review';

    // Primary Key Name
    protected $primaryKey = 'reviewid';

    // Fill that must be specified during Insert (whitelist)
    protected $fillable = [
        'content',
        'rating',
        'reviewdate',
        'authidreviewer',
        'auctionid'
    ];


    // Methods (Relationships)

    /**
     * Get user who reviewed from review
     */
    public function userReviewer(): BelongsTo
    {
        return $this->belongsTo(AuthenticatedUser::class, 'authidreviewer', 'authid');
    }

    /**
     * Get auction that the review refers to
     */
    public function auction(): BelongsTo
    {
        return $this->belongsTo(Auction::class, 'auctionid', 'auctionid');
    }


    
    // Methods (Not relationships)

    /**
     * Get the user who was reviewed from the review
     */
    public function userReviewed(): ?AuthenticatedUser
    {
        // Get the auction
        $auction = $this->auction;

        // Get the product from the auction
        if ($auction) {
            $product = $auction->product;

            // Get the user from the product
            if ($product) {
                return $product->authUser;
            }
        }

        return null; // If any step fails, return null
    }


    // Don't add create and update timestamps in database.
    public $timestamps  = false;
}
