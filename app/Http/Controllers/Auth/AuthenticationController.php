<?php
 
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Hash;

use Illuminate\View\View;

use App\Models\Users;

use App\Models\AuthenticatedUser;

use App\Models\Admin;

use Laravel\Socialite\Facades\Socialite;

class AuthenticationController extends Controller
{

    /**
     * Display a login form.
     */
    public function showLoginForm(Request $request)
    {
        if ($request->deleteCookie == 1){
            $this->logout($request);
        }
        
        if (Auth::check()) {
            return redirect('/home');
        } else {
            return view('pages.login');
        }
    }

    /**
     * Handle an authentication attempt.
     */
    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
 
        if (Auth::attempt($credentials, $request->filled('remember'))) {

            $request->session()->regenerate();
 
            return redirect()->intended('/home');
        }
 
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function register(Request $request) : RedirectResponse
    {
        //Colocar numa transaction
        //Validar também a request antes

        $request->validate([
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'phonenumber' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8',
            'address' => 'required|string|max:255'
        ]);

        $user = Users::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phonenumber' => $request->phonenumber
        ]);

        AuthenticatedUser::create([
            'uid' => $user->userid,
            'address' => $request->address,
            'registerdate' => now()
        ]);

        $credentials = $request->only('email', 'password');
        Auth::attempt($credentials);
        $request->session()->regenerate();
        return redirect('/home')->withSuccess('You have successfully registered & logged in!');
    }

    /**
     * Log out the user from application.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')
            ->withSuccess('You have logged out successfully!');
    } 


    public function adminRegisterUser($adminid , Request $request){
        
        if(Auth::user()->admin->adminid == $adminid){


            $user = Users::create([
                'username' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phonenumber' => $request->phone
            ]);
    
            AuthenticatedUser::create([
                'uid' => $user->userid,
                'address' => "placeholder",
                'registerdate' => now()
            ]);
        
            return redirect('/home');
        }else{
            abort(400);
        }
    }

    public function adminRegisterAdmin($adminid , Request $request){
        
        if(Auth::user()->admin->adminid == $adminid){
    
            $user = Users::create([
                'username' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phonenumber' => $request->phone
            ]);

            $adminlevel = 1;

            Admin::create([
                'uid' => $user->userid,
                'adminlevel' => $adminlevel
            ]);
    
            return redirect('/home');
        }else{
            abort(400);
        }
    }

    public function showForgotPassword(){
        return view('pages.forgotPassword');
    }

    public function redirectToGoogle(){
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request){
        try {

            $googleUser = Socialite::driver('google')->stateless()->user();
    
            $user = Users::where('email', $googleUser->email)->first();
    
            if (!$user) {

                $user = Users::create([
                    'username' => $googleUser->name,
                    'email' => $googleUser->email,
                    'password' => Hash::make($googleUser->id),
                    'phonenumber' => 000000000
                ]);
        
                AuthenticatedUser::create([
                    'uid' => $user->userid,
                    'address' => "placeholder",
                    'registerdate' => now()
                ]);
            }

            $credentials = [
                'email' => $googleUser->email,
                'password' => $googleUser->id
            ];
        
            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();
                return redirect()->intended('/home');
            }
        
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');

        } catch (Exception $e) {

            return redirect()->route('login')->with('error', 'Something went wrong. Please try again.');
        }
    
    }
}
