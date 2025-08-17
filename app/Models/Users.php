<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Auctions;
use App\Models\Messages;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

// Added to define Eloquent relationships.
use Illuminate\Database\Eloquent\Relations\HasMany;

class Users extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // Don't add create and update timestamps in database.
    public $timestamps  = false;

    // Table Name
    protected $table = 'users';

    // Primary Key Name
    protected $primaryKey = 'userid';

    // Fill that must be specified during Insert (whitelist)
    protected $fillable = [
        'username',
        'email',
        'password',
        'phonenumber'
    ];

    // Atributtes that must be hidden for serialization
    protected $hidden = [
        'password',
        'phonenumber'
    ];


    // Methods (Relationships)

    /**
     * Get the id of the admin through the user id
     */
    public function admin(): HasOne
    {
        return $this->hasOne(Admin::class, 'uid', 'userid');
    }

    /**
     * Get the id of the authUser through the user id
     */
    public function authUser(): HasOne
    {
        return $this->hasOne(AuthenticatedUser::class, 'uid', 'userid');
    }

    public function unblockRequest(): HasOne
    {
        return $this->hasOne(UnblockRequest::class, 'userid', 'userid');
    }

    /**
     * Get the cards for a user.
     */
    public function cards(): HasMany
    {
        return $this->hasMany(Card::class);
    }

    public function getBalance(): float
    {
        return $this->authUser->balance;
    }


    public function getAvailableBalance(): float
    {
        $currentDate = Carbon::now();
    
        // Step 1: Get all active auctions
        $currentDate = now(); // Or Carbon::now() if you're using Carbon explicitly

        $activeAuctions = Auction::where('initdate', '<=', $currentDate)
            ->where(function ($query) use ($currentDate) {
                $query->where('closedate', '>', $currentDate)
                    ->orWhere('closedate', '>=', $currentDate->subMinutes(5));
            })
            ->pluck('auctionid');

    
        if ($activeAuctions->isEmpty()) {
            // No active auctions
            return (float)$this->authUser->balance;
        }
    
        // Step 2: Retrieve top bids for active auctions
        $topBids = DB::table('bid as b1')
        ->select('b1.auctionid', 'b1.authid', 'b1.amount as max_amount')
        ->whereIn('b1.auctionid', $activeAuctions)
        ->whereRaw('b1.amount = (SELECT MAX(b2.amount) FROM bid as b2 WHERE b2.auctionid = b1.auctionid)')
        ->get();

    
        // Step 3: Sum top bids where the current user is the top bidder
        $userTopBidsSum = $topBids->where('authid', $this->authUser->authid)
            ->sum('max_amount');
    
        // Return the user's balance minus the sum of their top bids
        return (float)$this->authUser->balance - $userTopBidsSum;
    }


    public function getNotifications() 
    {
        return Notification::where('authid', $this->authUser->authid)->get();
    }
    
    public function getAllMessagesSentUser()
    {
        return DB::table('messages')
            ->select('messages.*', 'product.*')
            ->join('auction', 'messages.auctionid', '=', 'auction.auctionid')
            ->join('product', 'auction.productid', '=', 'product.productid')
            ->where(function ($query) {
                $query->where('messages.senderid', $this->authUser->authid)
                      ->orWhere('product.authid', $this->authUser->authid);
            })
            ->whereIn('messages.messageid', function ($subquery) {
                $subquery->select(DB::raw('MAX(messageid)'))
                    ->from('messages')
                    ->groupBy('auctionid');
            })
            ->get();
    }
}

