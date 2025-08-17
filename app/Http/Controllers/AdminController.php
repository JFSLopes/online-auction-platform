<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Users;
use App\Models\Auction;
use App\Models\Admin;
use App\Models\Bid;
use App\Models\AuthenticatedUser;
use App\Models\UnblockRequest;
use App\Models\Report;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
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

    /**
     * Check if user is valid and is an authUser
     */
    private function validateUser($userId): ?Users{
        if (!is_numeric($userId)) return NULL;
        $user = Users::find($userId);

        if (!$user || $user->authUser === NULL) return NULL;
        return $user;
    }

    private function validateAuction($id) : ?Auction {
        if (is_numeric($id)) return Auction::find($id);
        return NULL;
    }



    public function showAdminPage($adminId){
        $admin = $this->validateAdmin($adminId);
        if ($admin && $admin->userid == Auth::id()){
            return view('pages.adminPage');
        } else {
            return abort(401);
        }
    }

    public function showUserInfo($adminId, $userId){
        $admin = $this->validateAdmin($adminId);
        $user = $this->validateUser($userId);
        if ($admin && $user && $admin->userid == Auth::id()){
            // TODO
        } else {
            // ERROR 404
        }
    }

    public function updateUser($adminId, $userId){
        $admin = $this->validateAdmin($adminId);
        $user = $this->validateUser($userId);
        if ($admin && $user && $admin->userid == Auth::id()){
            // TODO
        } else {
            // ERROR 404
        }
    }

    public function deleteUser($adminId, $userId){
        $admin = $this->validateAdmin($adminId);
        if ($admin == null || $admin->userid != Auth::id()){
            return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 401);
        }

        $user = $this->validateUser($userId);
        if ($user == null){
            return response()->json(['success' => false, 'message' => 'User not found.'], 400);
        }

        // User elimination means it will become anonymous and that account will be invalid
        $auth = $user->authUser;
        if ($auth == null) {
            return response()->json(['success' => false, 'message' => 'Authentication details not found.'], 400);
        }

        DB::beginTransaction();
        try {
            // By deleting products, auctions are deleted and then bids as well. Only products from ongoin auctions or that will happen
            $products = $auth->products()->whereHas('auction', function ($query) {
                $query->where('closedate', '>', now()); // Auction is ongoing or future
            })->get();

            foreach($products as $product){
                $product->delete();
            }

            // Deletes bids where the user is the top bidder
            $topBids = Bid::select('bid.auctionid', DB::raw('MAX(bid.amount) as highestBid'))
                        ->join('auction', 'bid.auctionid', '=', 'auction.auctionid')
                        ->where('auction.initdate', '<', now())
                        ->where('auction.closedate', '>', now())
                        ->groupBy('bid.auctionid')
                        ->get()
                        ->keyBy('auctionid');

            $userBids = $auth->bids;

            foreach($userBids as $bid){
                if (isset($topBids[$bid->auctionid]) && $topBids[$bid->auctionid]->highestbid == $bid->amount) {
                    $bid->delete();
                }
                
            }

            $auth->uid = Users::where('username', 'anonymous')->value('userid');
            $auth->save();

            $user->delete();
            
            DB::commit();
            return response()->json(['success' => true, 'message' => 'User deleted successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'An error occurred while deleting the user.'], 500);
        }
    }

    public function searchUsersQuery($adminId, Request $request){
        $admin = $this->validateAdmin($adminId);

        if ($admin && $admin->userid == Auth::id()){

            $request->validate([
                'searchQuery' => 'nullable|string|max:255'
            ]);
            
            if ($request->searchQuery == null) {
                $users = Users::has('authUser')->get(); // Only users that are authenticated Users
            }else{
                $users = Users::where('username', 'LIKE', '%' . $request->searchQuery . '%')->has('authUser')->get();
            }

            return view('pages.usersInfo',compact('users'));
        } else {
            return abort(401);
        }
    }

    public function editProfileAdmin($adminId, $userId){
        $admin = $this->validateAdmin($adminId);
        if ($admin && $admin->userid == Auth::id()){
            $user = Users::find($userId);
            if ($user == null) return abort(400);

            $authuser = $user->authUser;
            if ($authuser == null) abort(400);
            return view('partials.editProfileAdmin', compact('user', 'authuser'));
        } else {
            return abort(401);
        }
    }

    public function editProfileAdminAction($adminId, $userId, Request $request){
        $admin = $this->validateAdmin($adminId);
        if ($admin && $admin->userid == Auth::id()){

            // Validate User
            $user = Users::find($userId);
            if ($user == null) return abort(400);

            $auth = $user->authUser;
            if ($auth == null) abort(400);

            // Validate Request
            $request->validate([
                'username' => 'required|string|max:255',
                "photos" => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048'
            ]);
            // Image storage 
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

            $user->username = $request['username'];
            if ($request->hasFile('photos')) $auth->profilepic = $fileName;

            $user->save();
            $auth->save();

            return redirect()->route('searchUserAccounts', ['adminId' => Auth::id()])->with('success', 'Profile updated successfully.');

        } else {
            return abort(401);
        }
    }

    public function seeUserProfileAdmin($adminId, $userId){
        if($userId == 1) return redirect()->route('showAdminPage', ['adminId' => $adminId]);

        $admin = $this->validateAdmin($adminId);
        $user = Users::find($userId);
        if ($user == NULL) return abort(400);

        $auth = $user->authUser;
        if ($auth == NULL) return abort(400);
        $auctions = $auth->personalAuctions();
        $topBids = $auth->getActiveTopBids();
        $reviews = $auth->reviewsTo();

        if ($admin && $user){
            return view('partials.visitorProfile', compact('user','auth','reviews','auctions','topBids'));
        } else {
            return abort(401);
        }
    }

    public function blockUser($adminId, $userId){
        $admin = $this->validateAdmin($adminId);
        if ($admin == null || $admin->userid != Auth::id()){
            return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 401);
        }

        $user = $this->validateUser($userId);
        if ($user == null){
            return response()->json(['success' => false, 'message' => 'User not found.'], 400);
        }

        $auth = $user->authuser;
        if ($auth == null){
            return response()->json(['success' => false, 'message' => 'Authentication details not found.'], 400);
        }

        // Delete the user unblock request before unblocking
        if ($auth->isblocked){
            $unblockRequest = $user->unblockRequest;
            if ($unblockRequest) $unblockRequest->delete();
        }

        $str = $auth->isblocked ? "unblocked" : "blocked";
        $auth->isblocked = $auth->isblocked ? False : True;
        $auth->save();
        
        return response()->json(['success' => true, 'message' => 'User was ' . $str . '.']);
    }

    public function seeUnblockRequests($adminId){
        $admin = $this->validateAdmin($adminId);
        if ($admin == null || $admin->userid != Auth::id()){
            return abort(401);
        }

        $users = Users::join('unblockrequest', 'users.userid', '=', 'unblockrequest.userid')
                      ->select('users.*', 'unblockrequest.*')
                      ->get();

        $reports = Report::join('users', 'users.userid', '=', 'report.userwhoreported')
                         ->join('authenticateduser', 'authenticateduser.uid', '=', 'report.userreported')
                         ->select('report.*', 'users.*', 'authenticateduser.isblocked')
                         ->get()
                         ->keyBy(function ($item) {
                            return $item->userreported; // Use `userreported` from the `reports` table for the key
        });

        // Key track of the keys that have a match in the $users and $reports
        $usedKeys = [];

        // Map users to include associated reports
        $usersWithReports = $users->map(function ($user) use ($reports, &$usedKeys) {
            $report = $reports->get($user->userid, collect()); // Find the report for the user
            $usedKeys[$user->userid] = true; // Mark this key as used
            return [
                'user' => $user,
                'report' => $report,
            ];
        });

        // Reports to be decided
        $reportsWaitingApproval = $reports->filter(function ($report) use ($usedKeys) {
            return !isset($usedKeys[$report->userreported]); // Include only reports with unused keys
        });

        return view('pages.unblockRequests', compact(['usersWithReports', 'reportsWaitingApproval']));
    }

    public function unblockRequestsActions(Request $request, $adminId){
        $request->validate([
            'accept' => 'required|numeric|in:0,1',
            'userid' => 'required|numeric',
            'report' => 'required|numeric|in:0,1'
        ]);
        // Check if is admin
        $admin = $this->validateAdmin($adminId);
        if ($admin == null || $admin->userid != Auth::id()){
            return response()->json(['success' => false, 'message' => 'You are not a admin. Nice Try.']);
        }

        // Check if user is valid
        $user = $this->validateUser($request->userid);
        if ($user == null){
            return response()->json(['success' => false, 'message' => 'The userid is not valid.']);
        }

        // Try unblock or refuse the request
        if ($request->report == '0'){ // Is a unblock request
            // Delete report and request
            $report = Report::where('userreported', $user->userid)->first();
            if ($report) $report->delete();

            $requestUser = UnblockRequest::where('userid', $user->userid)->first();
            if ($requestUser) $requestUser->delete();
            
            if ($request->accept == '1'){
                $auth = $user->authUser;
                if ($auth){
                    $auth->isblocked = False;
                    $auth->save();
                }
            }
        } else { // Is a report
            if ($request->accept == '1'){
                $auth = $user->authUser;
                if ($auth){
                    $auth->isblocked = True;
                    $auth->save();
                }
            }
        }
        $str = $request->accept == '1' ? 'accepted' : 'denied';
        return response()->json(['success' => true, 'message' => 'Request was ' . $str . '.']);
    }

    public function cancelAuction($userId,$auctionId){
        $admin = $this->validateAdmin($userId);
        $auction = $this->validateAuction($auctionId);
        if($admin){
            if($auction){
                if($auction->bids->isEmpty()){
                    $auction->update(['closedate' => $auction->closedate]);
                    $auction->delete();
                    return response()->json(['success' => 'true']);
                }
                else{
                    return response()->json(['message' => 'Auction already has bids'], 404);
                }
            }else{
                return response()->json(['message' => 'Auction not found'], 404); 
            }
        }else{
            return response()->json(['message' => 'User not found'], 404); 
        }
    }

    public function seeCategories($adminId){
        $admin = $this->validateAdmin($adminId);
        if ($admin == null || $admin->userid != Auth::id()){
            return abort(401);
        }

        $subCategoriesByCategory = DB::table('subcategory')
            ->join('category', 'subcategory.catid', '=', 'category.catid')
            ->select('category.categoryname as category_name', 'subcategory.*')
            ->orderBy('category.categoryname')
            ->get()
            ->groupBy('category_name');

        return view('pages.categories', compact('subCategoriesByCategory'));
    }

    public function addCategory($adminId, Request $request){
        $admin = $this->validateAdmin($adminId);
        if ($admin == null || $admin->userid != Auth::id()){
            return abort(401);
        }

        $request->validate([
            'categoryname' => 'required|string|max:255'
        ]);

        $categoryName = $request->categoryname;
        $category = DB::table('category')->where('categoryname', $categoryName)->first();

        if ($category){
            // Category already exists
            return abort(400);
        }

        DB::table('category')->insert(['categoryname' => $categoryName]);

        DB::table('subcategory')->insert(['catid' => DB::table('category')->where('categoryname', $categoryName)->value('catid'), 'subcategoryname' => 'Default']);

        // Category added successfully
        return back();
    }

    public function addSubcategory($adminId, Request $request){
        $admin = $this->validateAdmin($adminId);
        
        if ($admin == null || $admin->userid != Auth::id()){
            return abort(401);
        }

        $request->validate([
            'catid' => 'required|string|max:255',
            'subcategoryName' => 'required|string|max:255'
        ]);

        $categoryId = $request->catid;
        $subcategoryName = $request->subcategoryName;

        $category = DB::table('category')->where('catid', $categoryId)->first();

        if (!$category){
            //Category doesn't exists already exists
            return abort(400);
        }

        $subcategory = DB::table('subcategory')
            ->where('subcategoryname', $subcategoryName)
            ->where('catid', $categoryId)
            ->first();


        if ($subcategory){
            // Category already exists
            return abort(400);
        }

        DB::table('subcategory')->insert(['catid' => $category->catid, 'subcategoryname' => $subcategoryName]);

        // Subcategory added successfully
        return back();
    }

    public function deleteCategory($adminId, Request $request){
        $admin = $this->validateAdmin($adminId);
        if ($admin == null || $admin->userid != Auth::id()){
            return abort(401);
        }

        $request->validate([
            'catid' => 'required|string|max:255'
        ]);

        $categoryId = $request->catid;

        $category = DB::table('category')->where('catid', $categoryId)->first();

        if (!$category){
            // Category doesn't exist
            return abort(400);
        }

        $subcategories = DB::table('subcategory')->where('catid', $categoryId)->get();

        foreach($subcategories as $subcategory){
            $products = DB::table('product')->where('subcatid', $subcategory->subcatid)->get();
            if (count($products) > 0){
                // Category has subcategories being used on items
                return abort(400);
            }
        }

        foreach($subcategories as $subcategory){
            DB::table('subcategory')->where('subcatid', $subcategory->subcatid)->delete();
        }

        DB::table('category')->where('catid', $categoryId)->delete();

        // Category deleted successfully
        return back();
    }

    public function deleteSubcategory($adminId, Request $request){
        $admin = $this->validateAdmin($adminId);
        if ($admin == null || $admin->userid != Auth::id()){
            return abort(401);
        }

        $request->validate([
            'subcatid' => 'required|string|max:255'
        ]);

        $subcategoryId = $request->subcatid;

        $subcategory = DB::table('subcategory')->where('subcatid', $subcategoryId)->first();

        if (!$subcategory){
            // Subcategory doesn't exist
            return abort(400);
        }

        $products = DB::table('product')->where('subcatid', $subcategory->subcatid)->get();
        if (count($products) > 0){
            // Subcategory is already used in items
            abort(400);
        }

        DB::table('subcategory')->where('subcatid', $subcategory->subcatid)->delete();

        if(DB::table('subcategory')->where('catid', $subcategory->catid)->count() == 0){
            DB::table('category')->where('catid', $subcategory->catid)->delete();
        }


        // Subcategory deleted successfully
        return back();
    }
}

