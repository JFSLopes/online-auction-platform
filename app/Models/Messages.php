<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Messages extends Model
{
    use HasFactory;

    // Table Name
    protected $table = 'messages';

    // Primary Key Name
    protected $primaryKey = 'messageid';

    // Fill that must be specified during Insert (whitelist)
    protected $fillable = [
        'content',
        'sentdate',
        'senderid',
        'auctionid'
    ];


    // Methods (Relationships)

    /**
     * Get the authUser who wrote the message
     */
    public function authUser(): BelongsTo
    {
        return $this->belongsTo(AuthenticatedUser::class, 'senderid', 'authid');
    }

    /**
     * Get the auction associated with the message
     */
    public function auction(): BelongsTo
    {
        return $this->belongsTo(Auction::class, 'auctionid', 'auctionid');
    }



    // Don't add create and update timestamps in database.
    public $timestamps  = false;
}
