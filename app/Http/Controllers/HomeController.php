<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Auction;
use App\Models\AuthenticatedUser;
use App\Http\Controllers\UserController;
use App\Http\Controllers\NotificationController;
use App\Models\Users;
use App\Models\Product;
use App\Models\Category;
use App\Models\Bid;
use App\Models\Premium;
use App\Models\Image;
use App\Models\Messages;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;


class HomeController extends Controller
{
    private function validateAuction($id) : ?Auction {
        if (is_numeric($id)) return Auction::find($id);
        return NULL;
    }

    private function validateCategory($id) : ?Category {
        if (is_numeric($id)) return Category::find($id);
        return NULL;
    }

    private function validateUser($id) : ?Users {
        if (!is_numeric($id)) return NULL;
        
        // Check if ID is valid
        $user = Users::find($id);
        if (!$user || $user->authUser === NULL) return NULL;

        return $user;
    }

    private function getValidConditions($condictions){
        $all_conditions = Product::getProductConditions();
        $result = array();
        foreach($condictions as $condiction){
            if (in_array($condiction, $all_conditions)){
                array_push($result, $condiction);
            }
        }
        return empty($result) ? NULL : $result;
    }


    //-------------------------------- Views --------------------------------//
    public function showHome(Request $request){
        $categories = Category::all();

        $premiumAuctions = Premium::getAllUnfinishedPremiumAuctions();
        $aux = $premiumAuctions->count() / 3;
        $numPagesPremiumAuctions = (int) ($premiumAuctions->count() % 3 == 0 ? $aux : $aux + 1);

        $featuredAuctions = Product::getAllUnfinishedAuctionsAndProducts();
        $aux = $featuredAuctions->count() / 9;
        $numPagesFeaturedAuctions = (int) ($featuredAuctions->count() % 9  == 0 ? $aux : $aux + 1);

        // Extract only a few of them
        $premiumAuctions = $premiumAuctions->take(3);
        $featuredAuctions = $featuredAuctions->take(9);

        return view('layouts.app', compact('categories', 'premiumAuctions', 'featuredAuctions', 'numPagesPremiumAuctions', 'numPagesFeaturedAuctions'));
    }

    public function showAuction($id){
        $auction = $this->validateAuction($id);

        if ($auction){
            $product = $auction->product;
            if($product == NULL) return redirect('/home');

            $auction_owner = $product->authUser;
            if($auction_owner == NULL) return redirect('/home');

            $user = $auction_owner->user;
            if($user == NULL) return redirect('/home');

            $product_subcat = $product->subCategory;
            if($product_subcat == NULL) return redirect('/home');

            $product_cat = $product_subcat->category;
            if($product_cat == NULL) return redirect('/home');

            $auth_user = Auth::user();
            $images = $product->getImages($product->productid);
            $hasAuctionWatchlist = UserController::userWatchlistHasAuction($auth_user,$id);
            $topBidderId = $auction->getTopBidder();
            
            $reviews = $auction->getReviewOnAuction();
            $reviewsOnUser = $auction_owner->reviewsTo();

            return view('pages.itemPage',compact('product','auction_owner','user','auction','product_subcat','product_cat','auth_user', 'images','hasAuctionWatchlist' , 'topBidderId' , 'reviews', 'reviewsOnUser'));
        } else {
            return redirect('/home');
        }
    }

    public function auctionHistory($id){
        $auction = $this->validateAuction($id);

        if ($auction){
            $bids = Bid::query()
            ->select('bid.*', 'users.username as username', 'users.userid as userid')
            ->join('auction', 'bid.auctionid', '=', 'auction.auctionid')
            ->join('authenticateduser', 'bid.authid', '=', 'authenticateduser.authid')
            ->join('users', 'users.userid', '=', 'authenticateduser.uid')
            ->where('auction.auctionid', $id)
            ->orderby('bid.biddate', 'desc')
            ->get();

            return $bids;
        } else {
            return abort(400);
        }
    }

    public function getAuctionDetails($id){
        $auction = $this->validateAuction($id);

        if ($auction){
            $full_details = Auction::query()
            ->select('auction.*','product.*','subcategory.*','category.*')
            ->join('product', 'auction.productid', '=', 'product.productid')
            ->join('subcategory','product.subcatid','=','subcategory.subcatid')
            ->join('category','subcategory.catid','=','category.catid')
            ->where('auction.auctionid', $id)
            ->first();

            return $full_details;
        } else {
            return abort(400);
        }
    }

    public function showProfile($id){
        if ($id == '1'){
            return abort(403);
        }

        $user = $this->validateUser($id);

        if ($user == NULL) abort(400);
        $auth = $user->authUser;

        if ($auth == NULL) abort(400); 
        $auctions = $auth->personalAuctions();
        $auctionsWhereUserIsTopBidder = $auth->getAuctionsWhereUserIsTopBidder();
        $reviews = $auth->getReviewsOnUserAndReviewer();
        $averageRating = 0;

        foreach($reviews as $review){
            $averageRating += $review['review']->rating;
        }

        if(count($reviews) > 0){
            $averageRating = $averageRating / count($reviews);
        }

        if ($user){

            $topBids = $auth->getActiveTopBids();
            $reviews = $auth->reviewsTo();

            if($user->userid != Auth::id()){
                return view('pages.visitor',compact('user','auth','reviews','auctions','topBids'));
            }

            return view('pages.profilePage' , compact('auth' , 'user' , 'auctions' , 'auctionsWhereUserIsTopBidder' , 'reviews' , 'averageRating'));
        } else {
            return abort(400);
        }
    }

    public function showSearch(Request $request){
        // Retrive the product conditions
        $conditions = Product::getProductConditions();

        // Retrive all categories
        $categories = Category::all();

        if ($request->has('search') && !empty($request->input('search'))){
            $query = $request->input('search');

            $product = new Product();
            $auctions = $product->exactMatch($query)->take(8);;

            return view('pages.searchPage', compact('conditions', 'categories', 'auctions'));
        } else {
            $auctions = Product::getAllAuctionsAndProducts()->take(8);
            return view('pages.searchPage', compact('conditions', 'categories', 'auctions'));
        }
    }

    public function getSubCategories($id){
        $category = $this->validateCategory($id);

        if ($category){
            return $category->subCategories;
        } else {
            return redirect('/home');
        }
    }

    public function getUserDetails($id){
        $user = $this->validateUser($id);

        if($user){
            return $user;
        }else{
            return abort(400);
        }
    }

    public function showDashboard($id){
        $user = $this->validateUser($id);
        $auth = $user->authUser;

        $wonItems = $auth->getDashboardWonItems();  //i have to fetch pride paid as well

        $activeBids = $auth->getDashboardBids(); //valor , data de expiração e nome do produto

        if($user){
            return view('partials.dashboard', compact('user' , 'auth' , 'wonItems' , 'activeBids'));
        }else{
            return redirect('/home');
        }
    }

    public function showActiveProfile($id){
        $user = $this->validateUser($id);
        $auth = $user->authUser;
        $auctions = $auth->personalAuctions();
        $topBids = $auth->getActiveTopBids();

        $auctionsWhereUserIsTopBidder = $auth->getAuctionsWhereUserIsTopBidder();
        $reviews = $auth->getReviewsOnUserAndReviewer();
        $averageRating = 0;

        foreach($reviews as $review){
            $averageRating += $review['review']->rating;
        }

        if(count($reviews) > 0){
            $averageRating = $averageRating / count($reviews);
        }

        $reviews = $auth->reviewsTo();

        if($user){
            return view('partials.profile', compact('user' , 'auth' , 'auctions' , 'topBids' , 'reviews' , 'averageRating' , 'auctionsWhereUserIsTopBidder'));
        }else{
            return redirect('/home');
        }
    }

    public function showStatistics($id){
        $user = $this->validateUser($id);
        if ($user == null) return redirect('/home');
        $auth = $user->authUser;

        if($user){
            return view('partials.statistics', compact('user' , 'auth'));
        }else{
            return redirect('/home');
        }
    }

    public function showPremium($id){
        $user = $this->validateUser($id);
        if ($user == null) return abort(400);

        $auth = $user->authUser;
        if ($auth == null) return abort(401);

        $premium = $auth->premium;

        if ($premium != null && $premium->expirydate < now()){
            $premium->delete();
        }

        if ($user){
            return view('partials.premium', compact('premium'));
        } else {
            return redirect('/home');
        }
    }

    public function getAuctionsHome(Request $request){
        $request->validate([
            'premium' => 'required|boolean',
            'page' => 'required|numeric'
        ]);

        $numPages = 10;
        $perPage = 0;
        $auctions = new Collection();
        if ($request->premium){
            $auctions = Premium::getAllUnfinishedPremiumAuctions()->take(3*$numPages);
            $perPage = 3;
        } else {
            $auctions = Product::getAllUnfinishedAuctionsAndProducts()->take(9*$numPages);
            $perPage = 9;
        }

        $totalResults = $auctions->count();
        $paginatedResults = $auctions->forPage($request->page, $perPage);

        return response()->json([
            'premium' => $request->premium,
            'results' => $paginatedResults->values(),
            'currentPage' => (int)$request->page,
            'totalPages' => ceil($totalResults / $perPage)
        ]);
    }
    /**
     * Filters the auction based on:
     *      - Auction initial price
     *      - Range of acceptable dates
     *      - Product condition
     *      - search query
     *      - category of the products
     */
    public function getAuctionsFromSearch(Request $request){
        $validated = $request->validate([
            'search' => 'nullable|string|max:255',                           // Search term (optional)
            'min_value' => 'numeric|min:0',                                  // Minimum value
            'max_value' => 'numeric|gte:min_value',                          // Maximum value
            'conditions' => 'nullable|string',                               // Conditions (optional)
            'start_datetime' => 'date',                                      // Start datetime
            'end_datetime' => 'nullable|date|after_or_equal:start_datetime', // End datetime must be after start
            'category' => 'nullable|string',                                 // Category of product
            'page' => 'required|numeric'                                     // Current page
        ]);

        $page = $request->input('page', 1);         // Sets Default to page 1 if not specified
        $perPage = $request->input('perPage', 8);  // Default to 8 results per page

        // Get the results from the FULL TEXT SEARCH
        $result_1 = new Collection();
        $result_2 = new Collection();
        // Allow only letters, numbers, spaces, '|', '&', and '!'
        $sanitizedQuery = preg_replace('/[^a-zA-Z0-9\s|&!]/', '', $request->search);
        if ($request->search){
            // Check if query contains '!', '&', or '|'
            if (strpos($sanitizedQuery, '!') !== false || strpos($sanitizedQuery, '&') !== false || strpos($sanitizedQuery, '|') !== false) {
                try {
                    // Attempt Full-Text Search
                    $result_1 = Product::fullTextSearch($sanitizedQuery);
                    $searchType = "FTS Done";
                } catch (\Exception $e) {
                    // No search will be done
                    $result_1 = Product::getAllAuctionsAndProducts();
                    $searchType = "FTS query was wrong, so it wasn't used";
                }
            } else {
                // Perform Exact Match if no special characters found
                $result_1 = Product::exactMatch($sanitizedQuery);
                $searchType = "Exact match done";
            }
        } else {
            $searchType = "No query search done";
            $result_1 = Product::getAllAuctionsAndProducts();
        }


        // Apply the category filter
        if ($request->category && $request->category != 'none'){
            $category = Category::where('categoryname', $request->category)->first();
            if ($category !== NULL){
                $category_id = $category->catid;
                foreach($result_1 as $value){
                    if (Product::find($value->productid)->subCategory->catid === $category_id){
                        $result_2->push($value);
                    }
                }
            }
        } else {
            $result_2 = $result_1;
        }


        // Apply the date filter
        $result_1 = new Collection(); // Clear 
        if ($request->start_datetime || $request->end_datetime){
            $start_datetime = $request->start_datetime ?? null;
            $end_datetime = $request->end_datetime ?? null;
            $now = Carbon::now();
            // Get auction IDs that meet the date criteria
            $auction_ids = Auction::when($start_datetime, function ($query) use ($start_datetime,$now) {
                // First condition: initdate < start_datetime and closedate > end_datetime
                $query->where('initdate', '<', $start_datetime)
                      ->where('closedate', '>', $now);
                })
                ->orWhere(function ($query) use ($start_datetime, $end_datetime) {
                    // Second condition: initdate >= start_datetime and closedate <= end_datetime
                    $query->where('initdate', '>', $start_datetime)
                          ->where('closedate', '<', $end_datetime);
            })
            ->orderBy('closedate', 'asc')
            ->pluck('auctionid') // Get only the IDs
            ->toArray();
            // Filter `$result_2` to include only auctions that match the IDs
            foreach ($result_2 as $value){
                if (in_array($value->auctionid, $auction_ids)) {
                    $result_1->push($value); // Add the matching value to the new collection
                }
            }
        } else {
            $result_1 = $result_2;
        }


        // Apply the bid range price filter
        $result_2 = new Collection(); // Clear the data 
        foreach($result_1 as $value){
            if ($value->initvalue >= $request->min_value && $value->initvalue <= $request->max_value){
                $result_2->push($value);
            }
        }


        // Apply the condition filter
        $result_1 = new Collection(); // Clear
        $all_conditions = explode('@', $request->conditions);
        $valid_conditions = $this->getValidConditions($all_conditions);
        if ($valid_conditions){
            foreach ($result_2 as $value){
                if (in_array($value->state, $valid_conditions)){
                    $result_1->push($value);
                }
            }
        } else {
            $result_1 = $result_2;
        }

        $totalPages = ceil($result_1->count() / $perPage);
        $currentPage = $page > $totalPages ? $totalPages : $page;
        $paginatedResults = $result_1->forPage($currentPage, $perPage);

        return response()->json([
            'searchType' => $searchType,
            'results' => $paginatedResults->values(),
            'currentPage' => $page,
            'totalPages' => $totalPages
        ]);
    }

    public function getAuctionMessages($userId, $auctionId){
        $user = $this->validateUser($userId);
        $auction = $this->validateAuction($auctionId);
        
        if ($user && $auction) {

            $messages = $auction->messages;

            foreach ($messages as $message) {
                $authid = $message->senderid;
                $authUser = AuthenticatedUser::find($authid);
                $message->uid = $authUser->uid;
            }
            
        
            return response()->json($messages);
        } else {
            return abort(404);
        }
    }

    public function showWatchlist($userId){
        $user = $this->validateUser($userId);
        if ($user){
            $watchlist = $user->authUser->watchlist;
            $likedAuctions = $watchlist ? $watchlist->likedAuctions : new Collection();
            return view('partials.watchlist', compact('likedAuctions'));
        }else{
            return abort(404);
        }
    }

    public function checkEndendAuctions() {
        $recentlyEndedAuctions = Auction::whereBetween('closedate', [
            Carbon::now()->subMinutes(5),
            Carbon::now(),
        ])->get();
    
        foreach ($recentlyEndedAuctions as $auction) {
            DB::transaction(function () use ($auction) {
                $topBidderId = $auction->getTopBidder();
                $topBidValue = $auction->getTopBid();
    
                if ($topBidderId !== null && $topBidValue != $auction->initvalue) {
                    $topBidder = Users::find($topBidderId);
                    $authUser = $topBidder->authUser;
    
                    $authUser->balance -= $topBidValue;
                    $authUser->save();
    
                    NotificationController::createNotification(
                        'You have just won an auction!', 
                        'Auction End', 
                        Carbon::now(),
                        false,
                        $authUser->id,
                        $auction->authid
                    );
                }
            });
        }
    
        return response()->json('Updated ending auctions');
    }

    public function getUserPicture($userId) {
        $user = $this->validateUser($userId);
        if (!$user) {
            return response()->json(['error' => 'Invalid user'], 400);
        }
        
        $auth = $user->authUser;
        if (!$auth) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $baseDirectory = base_path() . '/public/images/users/';

        $imageFiles = glob($baseDirectory . $user->userid . '.*');
        $imagePath = "";
        if (!empty($imageFiles)) {
            $imagePath = $imageFiles[0];
        } 

        $fileExtension = pathinfo($imagePath, PATHINFO_EXTENSION);
        $imageUrl = File::exists($imagePath) ? asset('images/users/' . $user->userid . '.' . $fileExtension) : asset('images/svg/user.svg');
        
        return response()->file($imagePath);
    }

    public function getAuctionPicture(Request $request, $auctionId){
        $auction = $this->validateAuction($auctionId);

        if (!$auction) {
            return response()->json(['error' => 'Invalid auction'], 400);
        }

        $product = $auction->product;

        if (!$product) {
            return response()->json(['error' => 'Invalid product'], 400);
        }

        $photo = 0;
        if (isset($request->photoNum) && is_numeric($request->photoNum)){
            $photo = (int) $request->photoNum;
        }

        $imagePaths = $product->getImages($product->productid);

        if (empty($imagePaths)){
            return response()->file('images/svg/auction.svg');
        } else {
            $photo = max(0, min($photo, count($imagePaths) - 1));
            $fullPath = public_path($imagePaths[$photo]);
            if (file_exists($fullPath)) {
                $queryParam = '?v=' . filemtime($fullPath);
                $url = asset($imagePaths[$photo]) . $queryParam;
                return response()->file($fullPath, [
                    'Cache-Control' => 'no-cache, must-revalidate',
                ]);
            }

            return response()->file('images/svg/auction.svg');
        }
    }

    public function getCurrentChats($userId){
        $user = $this->validateUser($userId);
        if (!$user) {
            return response()->json(['error' => 'Invalid user'], 400);
        }

        $auth = $user->authUser;

        if (!$auth) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $authId = $auth->authid;

        $chats = Messages::query()
            ->select('messages.*','product.title')
            ->from('messages')
            ->join('auction', 'messages.auctionid', '=', 'auction.auctionid')
            ->join('product', 'auction.productid', '=', 'product.productid')
            ->where(function ($query) use ($authId) {
                $query->where('messages.senderid', $authId)
                      ->orWhere('product.authid', $authId);
            })
            ->whereIn('messages.messageid', function ($query) {
                $query->select(DB::raw('MAX(messages.messageid)'))
                      ->from('messages')
                      ->groupBy('messages.auctionid');
            })
            ->get();

        return response()->json($chats);
    }

    public function showAbout(){
        return view('pages.about');
    }

    public function showSupport(){
        return view('pages.support');
    }
}