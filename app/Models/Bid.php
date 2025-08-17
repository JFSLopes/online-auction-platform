<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bid extends Model
{
    use HasFactory;

    // Table Name
    protected $table = 'bid';

    // Primary Key Name
    protected $primaryKey = 'bidid';

    // Fill that must be specified during Insert (whitelist)
    protected $fillable = [
        'amount',
        'biddate',
        'authid',
        'auctionid'
    ];



    // Methods (Relationships)

    /**
     * Get the auth user who mad the bid
     */
    public function authUser(): BelongsTo
    {
        return $this->belongsTo(AuthenticatedUser::class, 'authid', 'authid');
    }

    /**
     * Get the auction associated with the bid
     */
    public function auction(): BelongsTo
    {
        return $this->belongsTo(Auction::class, 'auctionid', 'auctionid');
    }



    // Don't add create and update timestamps in database.
    public $timestamps  = false;
}
