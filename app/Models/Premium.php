<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Collection;

class Premium extends Model
{
    use HasFactory;

    // Table Name
    protected $table = 'premium';

    // Primary Key Name
    protected $primaryKey = 'premid';

    // Fill that must be specified during Insert (whitelist)
    protected $fillable = [
        'expirydate',
        'authid'
    ];

    // Casts for attributes
    protected $casts = [
        'expirydate' => 'date', // Automatically cast to a DateTime instance
    ];


    // Methods (Relationships)

    /**
     * Get the user id through the authUser id
     */
    public function authUser() : BelongsTo
    {
        return $this->belongsTo(AuthenticatedUser::class, 'authid', 'authid');
    }



    // Methods (Not Relationships)
    /**
     * Get all the ongoing auctions that are premium
     */
    public static function getAllPremiumAuctions(){
        $auctions = new Collection();
        $premiumUsers = Premium::all();

        foreach ($premiumUsers as $premiumUser){
            if ($premiumUser->expirydate < now()){ // No longer a premium user
                $premiumUser->delete();
            } else {
                $auth = $premiumUser->authUser;

                $userAuctions = $auth->personalAuctions();

                // Merge auctions with products into the collection
                foreach($userAuctions as $userAuction){
                    $auctions->push($userAuction);
                } 
            }
        }

        return $auctions;
    }

    public static function getAllUnfinishedPremiumAuctions(){
        $auctions = new Collection();
        $premiumUsers = Premium::all();

        foreach ($premiumUsers as $premiumUser){
            if ($premiumUser->expirydate < now()){ // No longer a premium user
                $premiumUser->delete();
            } else {
                $auth = $premiumUser->authUser;

                $userAuctions = $auth->personalAuctions();
                // Merge auctions with products into the collection
                foreach($userAuctions as $userAuction){
                    if($userAuction['timeToClose'] > now()){
                        $auctions->push($userAuction);
                    }
                } 
            }
        }

        return $auctions;
    }
    

    // Don't add create and update timestamps in database.
    public $timestamps  = false;
}