<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WatchList extends Model
{
    use HasFactory;

    // Table Name
    protected $table = 'watchlist';

    // Primary Key Name
    protected $primaryKey = 'watchid';

    // Fill that must be specified during Insert (whitelist)
    protected $fillable = [
        'authid'
    ];



    // Methods (Relationships)

    /**
     * Get the auth user that own the watchlist
     */
    public function authUser(): BelongsTo
    {
        return $this->belongsTo(AuthenticatedUser::class, 'authid', 'authid');
    }

    /**
     * Get all the liked auctions entries for that watchlist
     */
    protected function likedAuctions(): HasMany
    {
        return $this->hasMany(LikedAuctions::class, 'watchid', 'watchid');
    }



    // Don't add create and update timestamps in database.
    public $timestamps  = false;
}
