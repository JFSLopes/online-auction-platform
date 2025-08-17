<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\Models\Users;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordResetMail;

class PasswordResetController extends Controller
{
    public function showResetForm($token)
    {
        return view('auth.password-reset', ['token' => $token]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|min:8|confirmed',
            '_token' => 'required'
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => bcrypt($request->password)
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }

    public function sendPasswordResetEmail(Request $request){

        $request->validate([
            'email' => 'required|email'
        ]);

        $status = Password::sendResetLink($request->only('email'));

        if($status == Password::RESET_LINK_SENT){
            $token = DB::table('resettoken')->where('email', $request->email)->value('token');
        
            $url = url("/password-reset/{$token}");

            Mail::to($request->email)->send(new PasswordResetMail($url));


            return response()->json(['message' => 'Email sent']);
        }

        return response()->json(['message' => 'Email not sent']);
    }
}
?>