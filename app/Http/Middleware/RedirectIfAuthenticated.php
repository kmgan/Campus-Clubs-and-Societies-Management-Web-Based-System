<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                /** @var \App\Models\User $user */
                $user = Auth::guard($guard)->user();

                // Redirect based on the user's role
                if ($user->hasRole('admin')) {
                    return redirect()->route('iclub.user.page');
                } elseif ($user->hasRole('club_manager')) {
                    return redirect()->route('iclub.clubMember.page');
                } elseif ($user->hasRole('user')) {
                    return redirect()->route('iclub.club.page');
                }
            }
        }

        return $next($request);
    }
}
