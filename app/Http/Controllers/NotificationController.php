<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Users;
use Illuminate\Support\Facades\Auth;
use App\Models\Auction;
use App\Models\SubCategory;
use App\Models\Product;
use App\Models\Image;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class NotificationController extends Controller
{
    public function updateNotificationSeenStatus($userid , $notificationId){
        $notification = Notification::find($notificationId);
       
        if ($notification && $userid == Auth::id() && $notification->authUser->uid == $userid) {
            $notification->seen = true;
            
            $notification->save();
            return redirect()->back();
        } else {
            return abort(404);
        }
    }

    public static function createNotification($content , $type , $sentDate , $seen , $authid , $auctionid){
        Notification::create([
            'content' => $content,
            'type' => $type, 
            'sentdate' => $sentDate , 
            'seen' => $seen , 
            'authid' => $authid , 
            'auctionid' => $auctionid
        ]);
    }
}

?>