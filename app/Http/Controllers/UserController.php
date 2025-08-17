<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Users;
use Illuminate\Support\Facades\Auth;
use App\Models\Auction;
use App\Models\SubCategory;
use App\Models\Product;
use App\Models\Watchlist;
use App\Models\LikedAuctions;
use App\Models\Notification;
use App\Models\Image;
use App\Models\Premium;
use App\Models\Report;
use App\Models\UnblockRequest;
use App\Models\Messages;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use App\Mail\PasswordResetMail;
use Carbon\Carbon;

class UserController extends Controller
{
    private function validateUser($userId) :?Users{
        if (!is_numeric($userId)) return NULL;
        $user = Users::find($userId);
        
        // User must be AuthUser
        if (!$user || $user->authUser === NULL) return NULL;

        // Check if the user is authenticated in the current session
        if (!Auth::check() || Auth::id() !== $user->userid) {
            return null; // Not authenticated or doesn't match the session user
        }

        return $user;
    }

    private function validateUserNotAuth($userId) : ?Users{
        if (!is_numeric($userId)) return NULL;
        $user = Users::find($userId);
        
        // User must be AuthUser
        if (!$user || $user->authUser === NULL) return NULL;

        return $user;
    }

    private function validateAdmin($adminId): ?Users{
        if (!is_numeric($adminId)) return NULL;
        $user = Users::find($adminId);

        if (!$user || $user->admin === NULL) return NULL;

        // Verify the session and cookie belong to this user
        if (!Auth::check() || Auth::id() !== $user->userid) {
            return null;
        }
        return $user;
    }


    private function validateAuction($id) : ?Auction {
        if (is_numeric($id)) return Auction::find($id);
        return NULL;
    }

    private function userOwnAuction($user, $auction){
        if ($user->userid === $auction->product->authUser->uid){
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function deleteAuction($userId, $auctionId){
        $user = $this->validateUser($userId);
        $auction = $this->validateAuction($auctionId);
        if($user){
            if($auction){
                if($this->userOwnAuction($user, $auction)){
                    if($auction->bids->isEmpty()){
                        // Notify users auction was closed
                        $authIds = DB::table('watchlist as w')
                            ->join('likedauction as la', 'w.watchid', '=', 'la.watchid')
                            ->where('la.auctionid', $auctionId)
                            ->pluck('w.authid');


                        foreach ($authIds as $authId) {
                            Notification::create([
                                'content' => 'An auction you are following has closed.',
                                'type' => 'Auction Closed',
                                'sentdate' => now(),
                                'seen' => false,
                                'authid' => $authId,
                                'auctionid' => null,
                            ]);
                        }

                        $auction->delete();
                        return response()->json(['success' => 'true']);
                    }
                    else{
                        return response()->json(['message' => 'Auction already has bids'], 404);
                    }
                }
                else{
                    return response()->json(['message' => 'User do not own the auction'], 404); 
                }
            }else{
                return response()->json(['message' => 'Auction not found'], 404); 
            }
        }else{
            return response()->json(['message' => 'User not found'], 404); 
        }
    }
    public static function userWatchlistHasAuction($user, $auctionId){
        if ($user == null) return FALSE;
        $auth = $user->authUser;
        if($auth){
            $likedAuctions = $user->authUser->watchlist?->likedAuctions;
            $auction = Auction::find($auctionId);
            if($likedAuctions && !$likedAuctions->isEmpty()){
                foreach ($likedAuctions as $likeAuction){
                    if ($likeAuction->auctionid === $auction->auctionid){
                        return TRUE;
                    }
                }
            }
        }
        return FALSE;
    }

    public function createAuction($userId, Request $request)
    {
        $user = $this->validateUser($userId);

        if ($user && $userId == Auth::id()) {

            try {
                // Perform the validation
                $validatedData = $request->validate([
                    'title' => 'required|string|max:50',
                    'description' => 'required|string|max:500',
                    'state' => 'required|string',
                    'subCategory' => 'required|string',
                    'initValue' => 'required|numeric',
                    'end_datetime' => 'required|date|after:today',
                    'start_datetime' => 'required|date',
                    'photos' => 'required|array|max:8', // Allow up to 8 photos
                    'photos.*' => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:10240', // Validate each photo
                ]);
                // Process the valid data...
        
            } catch (ValidationException $e) {
                return abort(404);
            }

            $product = new Product();
            $product->title = $validatedData['title'];
            $product->description = $validatedData['description'];
            $product->state = $validatedData['state'];
            $product->authid = Users::find(Auth::id())->authUser->authid;

            $subCatId = SubCategory::findByName($validatedData['subCategory'])->subcatid;
            $product->subcatid = $subCatId;
            $product->save();

            $auction = new Auction();
            $auction->initdate = $validatedData['start_datetime'];
            $auction->closedate = $validatedData['end_datetime'];
            $auction->initvalue = $validatedData['initValue'];
            $auction->productid = $product->productid;
            $auction->save();

            $photos = $validatedData['photos']; 
            
            if ($photos && is_array($photos)) {
                $i = 0;
                foreach ($photos as $photo) {
                    $i++; // Increment the counter for each photo

                    // Get file extension and create the filename
                    $fileExtension = $photo->getClientOriginalExtension();
                    $fileName = $product->productid . '-' . $i . '.' . $fileExtension;
                    $destinationPath = public_path('images/items');
                        
                    if (!File::exists($destinationPath)) {
                        File::makeDirectory($destinationPath, 0755, true);
                    }

                    $photo->move($destinationPath, $fileName);

                    $photoPath = 'images/items/' . $fileName;

                    Image::create([
                        'image' => $photoPath,
                        'productid' => $product->productid,
                    ]);
                }
            }else{
                return abort(400);
            }

            return redirect()->route('home');
        } else {
            return abort(401);
        }
    }

    public function showCreateAuction($userId){
        $user = $this->validateUser($userId);
        $subCategories = DB::table('subcategory')->get();
        if ($user) {
            return view('pages.createAuction' , compact('userId' , 'subCategories'));
        } else {
            return abort(401);  
        }
    }

    public function showMessages($userId){
        $user = $this->validateUser($userId);
        if ($user) {
            $messages = $user->getAllMessagesSentUser();
            return view('pages.messages', compact('messages'));
        } else {
            return abort(401);
        }
    }

    public function logout($userId) {
        $user = $this->validateUser($userId);
        $admin = $this->validateAdmin($userId);
        if ($admin){
            if ($userId == Auth::id()){
                Auth::logout();
                return redirect()->route('home');
            }
        }
        
        if ($user) { 
            if($userId == Auth::id()){
                Auth::logout();  
                return redirect()->route('home');
            }
        } else {
            return abort(401);
        }
    }
    
    public function addAuctionWatchlist(Request $request){
        $user = $this->validateUser($request->userid); //validate the user from the request
        $auction = $this->validateAuction($request->auctionid); //validate the auction from the request
        if ($user && $auction){
            if($user->userid == Auth::id()){ //makes sure that the authenticated is the same as the user that made the request
                if ($this->userWatchlistHasAuction($user, $auction->auctionid)){
                    return response()->json(['message' => 'Already added to watchlist']);
                } else {
                    $watchlist = $user->authUser->watchlist;
                    if($watchlist){
                        $likedAuction = new LikedAuctions();
                        $likedAuction->watchid = $watchlist->watchid;
                        $likedAuction->auctionid = $auction->auctionid;
                        $likedAuction->save();

                    }else{
                        $watchlist = Watchlist::create([
                            "authid" => $user->authUser->authid
                        ]);

                        $likedAuction = new LikedAuctions();
                        $likedAuction->watchid = $watchlist->watchid;
                        $likedAuction->auctionid = $auction->auctionid;
                        $likedAuction->save();
                    }
                    return response()->json(['message' => 'true']);
                }
            }else{
                return response()->json(['message'=>'Not allowed'],403);
            }
        }
        else{
            return response()->json(['message' => 'Invalid user or auction'],404);

        }
    }
    public function removeAuctionFromWatchlist(Request $request){
        $user = $this->validateUser($request->userid); //validate the user from the request
        $auction = $this->validateAuction($request->auctionid); //validate the auction from the request
        if ($user && $auction) {
            if($user->userid == Auth::id()){
                if ($this->userWatchlistHasAuction($user, $auction->auctionid)){
                    $watchlist = $user->authUser->watchlist;
                    $likedAuctions = $watchlist->likedAuctions;
                    foreach($likedAuctions as $likedAuction){
                        if($likedAuction->auctionid == $auction->auctionid){
                            $likedAuction->delete();
                        }
                    }
                    return response()->json(['message'=>'true']);
                } else {
                    return response()->json(['message'=>'You did not liked the auction'],403);
                }
            }else{
                return response()->json(['message' => 'Not allowed'],403);
            }
        } else {
            return response()->json(['message' => 'Invalid user or auction'],404);
        }
    }

    public function showWatchlist($userId){
        $user = $this->validateUser($userId);
        if ($user) {
            // TODO
            return "YES";
        } else {
            // ERROR 404
            return "NO";
        }
    }

    public function showEditProfile($userId){
        $user = $this->validateUser($userId);
        $authuser = $user->authUser;
        if ($user) {
            return view('partials.editProfile' , compact('user' , 'authuser'));
        } else {
            return abort(404); 
        }
    }

    public function editProfile($userId, Request $request) {

        $admin = false;
        $curr_user = Auth::id();
        $user = $this->validateUser($userId); // Ensure the user exists and is valid.

        if ($this->validateAdmin(Auth::id()) == null) { // User is not an admin
            if($curr_user != $userId){ // User isn't the same one that owns the profile
                return abort(404); 
            }
        }else{ //Ensure the user is an admin
            $admin = true;
            $user = Users::find($userId);
        }

        if (!$user && !$admin) {
            // Return 404 response if the user is not found.
            return abort(404);
        }

        
        // Validate the input data.
        $validatedData = $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'nullable|string|min:8',
            'phonenumber' => 'required|string|max:15|min:9',
            'address' => 'required|string',
            'photos' => 'image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);

        $fileName = "";

        if ($request->hasFile('photos')) {
            
            $photo = $request->file('photos');

            $extension = $photo->getClientOriginalExtension(); // Get the file extension
            $fileName = $userId .'.'. $extension; // Name format: {userid}-1.extension
            $destinationPath = public_path('images/users');
            
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }

            $existingFiles = File::glob($destinationPath . '/' . $userId . '.*'); // Mastches {userId}.* (any extension)

            foreach ($existingFiles as $file) {
                File::delete($file);
            }
            
            $photo->move($destinationPath, $fileName);
        }

        // Update user attributes.
        $user->username = $validatedData['username'];
        $user->email = $validatedData['email'];
        if (!empty($validatedData['password'])) {
            $user->password = Hash::make($validatedData['password']); // Hash the password.
        }
        $user->phonenumber = $validatedData['phonenumber'];
        $authUser = $user->authUser;
        $authUser->address = $validatedData['address'];
        if($request->hasFile('photos')){
            $authUser->profilepic = $fileName;
        }
    
        $user->save();
        $user->authUser->save();

        // Return a success response.
        return redirect()->route('profile' , ['id' => $userId]);
    }
    
    
    public function placeBid($userId, $auctionId,Request $request){
        $user = $this->validateUser($userId);
        $auction = $this->validateAuction($auctionId);

        $value = $request->value;

        if ($user && $auction) {
            try {

                if ($auction->closedate < now()){
                    return redirect()->route('auction', ['id' => $auctionId]);
                }
                
                $availableBalance = Auth::user()->getAvailableBalance();

                if($availableBalance < $value){
                    return json_encode(['error' => 'Insufficient funds']);
                }
                
                $bids = $auction->getTopBid();
                if ($bids){
                    $prevTopBidderId = $auction->getTopBid()['authid'];
                    $prevTopBid = $auction->getTopBid()['amount'];
                }


                DB::table('bid')->insert([
                    'amount' => $value,
                    'biddate' => now(),
                    'authid' => $user->authUser->authid,
                    'auctionid' => $auctionId,
                ]);

                if($bids && $prevTopBid != $auction->initvalue){
                    DB::table('notification')->insert([
                        'content' => 'You have been outbid on an auction',
                        'type' => 'Bid Covered',
                        'sentdate' => now(),
                        'seen' => false,
                        'authid' => $prevTopBidderId,
                        'auctionid' => $auctionId
                    ]);
                }

                if (Carbon::now()->diffInMinutes($auction->closedate, false) <= 15) {
                    $auction->closedate = Carbon::now()->addMinutes(30);
                    $auction->save();  
                }
        
                return redirect()->route('auction', ['id' => $auctionId]);

            } catch (\Exception $e) {
                return redirect()->route('auction', ['id' => $auctionId]);
            }

        } else {
            return abort(404);
        }
    }

    public function updateAuction($userId, $auctionId,Request $request){
        $request->validate([
            'title' => 'required|string|max:50',
            'description' => 'required|string|max:500',
            'subcategory'=> 'required|string',
            'condition'=> 'required|string',
            'init_value' => 'required|numeric|min:0.01|max:99999999.99'

        ]);
        $user = $this->validateUser($userId);
        $auction = $this->validateAuction($auctionId);
        $product = $auction->product;

        if ($user && $auction) {
            if ($this->userOwnAuction($user, $auction)){
                if($auction->bids->isEmpty()){
                    try {
                        $roundedInitValue = round($request->input('init_value'), 2);
                        $auction->update([
                            'initvalue' => $roundedInitValue
                        ]);
                        $subCategory = Subcategory::where('subcategoryname', $request->subcategory)->first();
                        $product->update([
                            'title' => $request->input('title'),
                            'description' => $request->input('description'),
                            'state' => $request->input('condition'),
                            'subcatid'=> $subCategory->subcatid

                        ]);
                    
                        return redirect('home/auction/' . $auctionId);
                        
                    } catch (\Throwable $th) {
                        abort(500, 'An unexpected error occurred.');
                    }
                    
                } else{
                    return abort(401);
                }
                
            } else {
                return abort(403);
            }
        } else {
            return abort(400);
        }
    }

    public function showUpdateAuction($userId,$auctionId){
        $user = $this->validateUser($userId);
        $auction = $this->validateAuction($auctionId);
        if($user){
            if($auction){
                if($this->userOwnAuction($user,$auction)){
                    if($auction->bids->isEmpty()){
                        $product = $auction->product;
                        $subcats = SubCategory::all();
                        $conditions = Product::getProductConditions();
                        return view('pages/editAuctionPage',compact('user', 'product','subcats','conditions','auction'));
                    }
                    else{
                        return abort(401);
                    }
                }
                else{
                    return abort(401); 
                }
            }else{
                return abort(403); 
            }
        }else{
            return abort(403); 
        }

    }

    public function showAddFunds($userId){
        $user = $this->validateUser($userId);
        if ($user) {
            return view('pages.addFunds');
        } else {
            return abort(404);
        }
    }

    public function addFunds($userId, Request $request){
        $user = $this->validateUser($userId);
        if ($user) {
            $request->validate([
                'amount' => 'required|numeric|min:0.01|max:99999999.99'
            ]);
            $user->authUser->balance += $request->amount;
            $user->authUser->save();
            return redirect()->route('home');
        } else {
            return abort(404);
        }
    }

    public function showWithdrawFunds($userId){
        $user = $this->validateUser($userId);
        if ($user) {
            return view('pages.withdrawFunds');
        } else {
            return abort(404);
        }
    }

    public function withdrawFunds($userId, Request $request){
        $user = $this->validateUser($userId);
        if ($user) {

            if($userId != Auth::id()){
                return abort(401);
            }
            $request->validate([
                'amount' => 'required|numeric|min:0.01|max:99999999.99'
            ]);

            $availableBalance = Auth::user()->getAvailableBalance();

            if ($request->amount > $availableBalance){ 
                return json_encode(['error' => 'Insufficient funds']);
            }

            $user->authUser->balance -= $request->amount;
            $user->authUser->save();
            return redirect()->route('home');
        } else {
            return abort(404);
        }
    }

    public function sendMessage($userId, Request $request){
        $user = $this->validateUser($userId);

        if ($user) {
            $request->validate([
                'message' => 'required|string|max:500',
                'auctionId' => 'required|numeric'
            ]);
            
            $message = $request->message;
            $auctionId = $request->auctionId;

            $messageSent = new Messages();
            $messageSent->senderid = $user->authUser->authid;
            $messageSent->content = $message;
            $messageSent->auctionid = $auctionId;
            $messageSent->sentdate = now();
            $messageSent->save();

            return response()->json([
                'message' => $messageSent,
                'uid' => $user->userid]);
            //TODO
            //Should broadcast the message to the next user using pusher here 

        } else {
            return abort(404);
        }
    }

    public function handlePremium($id, Request $request){
        $request->validate([
            "duration" => 'required|numeric'
        ]);

        $user = $this->validateUser($id);
        if ($user == null) return abort(400);
        
        $auth = $user->authUser;
        if ($auth == null) return abort(401);

        if ($user->userid != Auth::id()) return abort(403);

        // Check if user has enough money
        $availableBalance = Auth::user()->getAvailableBalance();

        $price = match($request->duration) {
            "1" => 9.99,
            "3" => 27.99,
            "6" => 49.99,
            default => 0
        };

        if ($price == 0) return abort(400);

        if ($availableBalance >= $price){
            // Remove balance and update expiry date
            DB::beginTransaction();
            try {
                $auth->decrement('balance', $price);
                $premium = $auth->premium;
                if ($premium){
                    $newExpiryDate = $premium->expirydate->addMonths((int) $request->duration);
                    $premium->update(['expirydate' => $newExpiryDate]);
                } else {
                    $newExpiryDate = now()->addMonths((int) $request->duration);
                    Premium::create([
                        'expirydate' => $newExpiryDate,
                        'authid' => $auth->authid
                    ]);
                }

                DB::commit();
                return redirect()->back()->with('message', 'Subscription updated successfully.');
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->back()->with('message', 'An error occurred. Please try again.');
            }

        } else {
            return redirect()->back()->with('message', 'Not enough credits.');
        }
    }

    public function unblockRequest($id){
        $user = $this->validateUser($id);
        if ($user == null) return abort(400);

        $auth = $user->authUser;
        if ($auth == null) return abort(401);

        if ($user->userid != Auth::id()) return abort(403);

        // check if user is blocked
        if (!$auth->isblocked) return abort(403);

        // Show the unblock page
        return view('pages.unblockRequest');
    }

    public function unblockRequestAction($id, Request $request){
        $request->validate([
            'content' => ['required', 'string', 'regex:/^[^<>]*$/']
        ]);

        $user = $this->validateUser($id);
        if ($user == null) return abort(400);

        $auth = $user->authUser;
        if ($auth == null) return abort(401);

        if ($user->userid != Auth::id()) return abort(403);

        // check if user is blocked
        if (!$auth->isblocked) return abort(403);

        $unblock = $user->unblockRequest;

        if ($unblock) return redirect()
                        ->back()
                        ->with('message', 'You have alredy sent a request.');

        try{
            UnblockRequest::create([
                'userid' => Auth::id(),
                'content' => $request->content,
                'date' => now()
            ]);
            return redirect()
                ->back()
                ->with('message', 'Your unblock request has been submitted successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('message', 'An error occurred while submitting your request. Please try again.');
        }
    }

    public function addReview($auctionId, $authUserId , Request $request){

        $authUserId = (int) $authUserId;
        $auctionId = (int) $auctionId;
        $reviewCount = (int) $request['reviewcount'];

        if($authUserId == Auth::user()->authUser->authid && $reviewCount == 0){
            
            $request->validate([
                'review' => 'required|string|max:500',
                'rating' => 'required|numeric|max:5|min:1'
            ]);
    
            $content = $request['review'];
            $rating = $request['rating'];
            $date = Carbon::now();
    
            DB::table('review')->insert([
                'content' => $content,
                'rating' => $rating,
                'reviewdate' => $date,
                'authidreviewer' => $authUserId,
                'auctionid' => $auctionId
            ]);

            return redirect()->route('auction' , ['id' => $auctionId]);
        }
        else{
            return abort(403);
        }
        //INSERT INTO Review (content, rating, reviewDate, authIdReviewer, auctionId) 
        //VALUES ('Great auction experience!', 4.50, '2024-10-10 10:00:00', 1, 1);
    }

    public function report(Request $request, $userId){
        // validate form input
        $request->validate([
            'userwhoreported' => 'required|numeric',
            'userreported' => 'required|numeric',
            'content' => 'required|string|max:150',
            'auctionid' => 'required|numeric'
        ]);

        //Validate Users
        $userReported = $this->validateUserNotAuth($request->userreported);
        if ($userReported == null){
            return response()->json(['success' => false, 'message' => 'Reported User does not exist.' . $request->userreported]);
        }

        $userWhoReported = $this->validateUser($request->userwhoreported);
        if ($userWhoReported == null){
            return response()->json(['success' => false, 'message' => 'Something went wrong.']);
        }

        if ($userWhoReported->userid != $userId){
            return response()->json(['success' => false, 'message' => 'You are trying to disguise yourself as someone else.']);
        }

        // validate auction
        $auction = $this->validateAuction($request->auctionid);
        if ($auction == null){
            return response()->json(['success' => false, 'message' => 'The auction is invalid.']);
        }

        if ($userReported->authuser->authid != $auction->product->authid){
            return response()->json(['success' => false, 'message' => 'The user being reported is not the auction owner.']);
        }

        // Insert report if there is no other report on the user
        if (Report::where('userreported', '=', $userReported->userid)->count() != 0){
            return response()->json(['success' => false, 'message' => 'The user was already reported.']);
        } else {
            // Add entry in the report table
            Report::create([
                'userwhoreported' => $userWhoReported->userid,
                'userreported' => $userReported->userid,
                'content' => $request->content,
                'auctionid' => $auction->auctionid,
                'date' => now()
            ]);
            return response()->json(['success' => true, 'message' => 'The user was reported.']);
        }
    }
}
