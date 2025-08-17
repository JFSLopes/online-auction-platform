<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    // Table Name
    protected $table = 'notification';

    // Primary Key Name
    protected $primaryKey = 'notid';

    // Fill that must be specified during Insert (whitelist)
    protected $fillable = [
        'content',
        'type',
        'sentdate',
        'seen',
        'authid',
        'auctionid'
    ];


    // Methods (Relationship)

    /**
     * Get the authUser that must receive the norification 
     */
    public function authUser(): BelongsTo
    {
        return $this->belongsTo(AuthenticatedUser::class, 'authid', 'authid');
    }

    /**
     * Get the auction associated with the notification 
     */
    public function auction(): BelongsTo
    {
        return $this->belongsTo(Auction::class, 'auctionid', 'auctionid');
    }

    // Don't add create and update timestamps in database.
    public $timestamps  = false;
}
