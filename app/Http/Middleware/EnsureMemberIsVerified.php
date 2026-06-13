<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureMemberIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->status === 'PENDING') {
            // Allow them to visit the onboarding page and submit the interview
            if ($request->is('onboarding') || $request->is('onboarding/submit')) {
                return $next($request);
            }
            
            // Otherwise redirect to onboarding
            return redirect()->route('member.onboarding');
        }

        return $next($request);
    }
}
