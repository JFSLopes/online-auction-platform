<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\AuthenticationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PasswordResetController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::redirect('/', '/home');

//Home
Route::controller(HomeController::class)->group(function () {
    Route::get('/home', 'showHome')->name('home');
    Route::get('/home/auction/{id}', 'showAuction')->name('auction');
    Route::get('/home/auction/{id}/history', 'auctionHistory');
    Route::get('/home/profile/{id}', 'showProfile')->name('profile');
    Route::get('/home/search', 'showSearch');
    Route::get('/home/category/{id}', 'getSubCategories');
    Route::get('/home/bi-json/users/{id}', 'getUserDetails');
    Route::get('/home/bi-json/users/{id}/auction/{auctionId}/message', 'getAuctionMessages');
    Route::get('/home/bi-json/users/{userId}/currentchats', 'getCurrentChats');
    Route::get('/home/bi-json/auctions/{id}', 'getAuctionDetails');
    Route::get('/home/bi-json/search', 'getAuctionsFromSearch');
    Route::get('/home/bi-json/search/home', 'getAuctionsHome');
    Route::get('/home/bi-api/userspic/{userId}', 'getUserPicture')->name('getUserPicture');
    Route::get('/home/bi-api/auctionspic/{auctionId}', 'getAuctionPicture')->name('getAuctionPicture');
    Route::get('/load-dashboard/{id}', 'showDashboard');
    Route::get('/load-profile/{id}', 'showActiveProfile');
    Route::get('/load-statistics/{id}', 'showStatistics');
    Route::get('/load-watchlist/{id}', 'showWatchlist');
    Route::get('/load-premium/{id}', 'showPremium');
    Route::post('/checkEndendAuctions' , 'checkEndendAuctions')->name('checkEndendAuctions');
    Route::get('/home/about', 'showAbout')->name('about');
    Route::get('/home/support', 'showSupport')->name('support');
});

// Authentication

Route::controller(AuthenticationController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login')->withoutMiddleware(['redirect']);
    Route::post('/login', 'authenticate')->withoutMiddleware(['redirect']);
    Route::post('/logout', 'logout')->name('logout')->withoutMiddleware(['redirect']);
    Route::post('/register', 'register')->withoutMiddleware(['redirect']);
    Route::post('/register-admin/{adminid}' , 'adminRegisterUser')->name('adminRegisterUser');
    Route::post('/register-user/{adminid}' , 'adminRegisterAdmin')->name('adminRegisterAdmin');
    Route::get('/register', 'showLoginForm')->name('register');
    Route::get('/forgot-password', 'showForgotPassword')->name('forgotPassword');
    Route::get('/login/google', 'redirectToGoogle')->name('loginGoogle');
    Route::get('/login/google/callback', 'handleGoogleCallback');
});

// User
Route::controller(UserController::class)->group(function (){
    Route::post('/user/{userId}/auction', 'createAuction');                               // Create auction
    Route::post('/user/{userId}/logout', 'logout');                                       // Log out
    Route::post('/user/{userId}/report', 'report');
    Route::post('/user/{userId}/watchlist/add/{auctionId}','addAuctionWatchlist');               // Add Auction to watchlist
    Route::post('/user/{userId}/watchlist/remove/{auctionId}', 'removeAuctionFromWatchlist');    // Remove auction from whislist
    Route::get('/user/{userId}/show-edit/profile', 'showEditProfile')->name('showeditProfile');    // Get view that allows to edit profile
    Route::post('/user/{userId}/edit/profile', 'editProfile')->name('editProfile');                            // Request that edits his profile
    Route::get('/user/{userId}/createAuction', 'showCreateAuction');                                   
    Route::post('/user/{userId}/createAuction/auction', 'createAuction')->name('createAuction');
    Route::post('/user/{userId}/auction/{auctionId}/bid', 'placeBid')->name('placeBid');             // Place a bid
    Route::post('/user/{userId}/edit/auction/{auctionId}', 'updateAuction')->name('updateAuction');              // Update Auction
    Route::post('/user/{userId}/delete/auction/{auctionId}', 'deleteAuction')->name('deleteAuction');            // Delete Auction
    Route::get('/user/{userId}/edit/auction/{auctionId}','showUpdateAuction');
    Route::get('/user/{userId}/add-funds', 'showAddFunds')->name('showAddFunds');
    Route::post('/user/{userId}/add-funds', 'addFunds')->name('addFunds');
    Route::get('/user/{userId}/withdraw-funds', 'showWithdrawFunds')->name('showWithdrawFunds');
    Route::post('/user/{userId}/withdraw-funds', 'withdrawFunds')->name('withdrawFunds');
    Route::get('/user/{userId}/messages', 'showMessages')->name('showMessages');
    Route::post('/user/{userId}/messages/sendMessage', 'sendMessage')->name('sendMessage');
    Route::post('/user/{userId}/premium', 'handlePremium');
    Route::get('/user/{userId}/unblock', 'unblockRequest')->name('unblock')->withoutMiddleware(['redirect']);
    Route::post('/user/{userId}/unblock', 'unblockRequestAction')->withoutMiddleware(['redirect']);
    Route::post('user/{auctionId}/{userId}/addReview' , 'addReview')->name('addReview');
});

// Admin
Route::controller(AdminController::class)->group(function (){
    Route::get('/admin/{adminId}', 'showAdminPage')->name('showAdminPage');
    Route::get('/admin/{adminId}/user/{userId}', 'showUserInfo');
    //Route::post('/admin/{adminId}/user/{userId}/updateProfile', 'updateUser');
    Route::get('/admin/{adminId}/user/{userId}/editProfile', 'editProfileAdmin')->name('editProfileAdmin');
    Route::post('/admin/{adminId}/user/{userId}/editProfile', 'editProfileAdminAction')->name('editProfileAdminAction');
    Route::get('/admin/{adminId}/user/{userId}/seeProfile', 'seeUserProfileAdmin')->name('seeUserProfileAdmin');
    Route::get('/admin/{adminId}/requests', 'seeUnblockRequests')->name('seeUnblockRequests');
    Route::post('/admin/{adminId}/requests', 'unblockRequestsActions')->name('unblockRequestsActions');
    Route::post('/admin/{adminId}/user/{userId}/del', 'deleteUser')->name('deleteUserAccount');
    Route::get('/admin/{adminId}/search-user-accounts', 'searchUsersQuery')->name('searchUserAccounts');
    Route::post('/admin/{adminId}/user/{userId}/block', 'blockUser')->name('blockUser');
    Route::post('/admin/{adminId}/delete/auction/{auctionId}','cancelAuction')->name('cancelAuction');
    Route::get('admin/{adminId}/seeCategories', 'seeCategories')->name('seeCategories');
    Route::post('admin/{adminId}/addCategory', 'addCategory')->name('addCategory');
    Route::post('admin/{adminId}/addSubcategory', 'addSubcategory')->name('addSubcategory');
    Route::post('admin/{adminId}/deleteSubcategory', 'deleteSubcategory')->name('deleteSubcategory');
    Route::post('admin/{adminId}/deleteCategory', 'deleteCategory')->name('deleteCategory');

});

Route::controller(NotificationController::class)->group(function (){
    Route::post('/notification/{userid}/{notificationid}/seen', 'updateNotificationSeenStatus')->name('updateNotificationSeenStatus');
});

Route::controller(PasswordResetController::class)->group(function (){
    Route::get('/password-reset/{token}', 'showResetForm')->name('password.reset');
    Route::post('/password-reset', 'reset');
    Route::post('/password-reset/send', 'sendPasswordResetEmail');
});