<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RedirectIfBlocked
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        if ($user == null) return $next($request);

        $auth = $user->authUser;
        if ($auth == null) return $next($request);

        if (Auth::check() && $auth->isblocked) {
            // Redirect to unblock page
            return redirect()->to('/user/' . Auth::id() . '/unblock');
        }

        return $next($request);
    }
}
