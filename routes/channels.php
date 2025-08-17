<?php

use Illuminate\Support\Facades\Broadcast;

use App\Models\Auction;
use App\Models\Users;
use App\Models\AuthenticatedUser;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('messages.{receiverId}', function ($user, $receiverId, $auctionId) {
    $auction = Auction::find($auctionId);

    if($auction){
        return (int) $user->authUser->uid === (int) $receiverId || (int) $user->authUser->uid === $auction->authid;
    }else{
        return false;
    }
});
