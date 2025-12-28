<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureTraderEmailIsVerified
{
    public function handle(Request $request, Closure $next)
    {
        $trader = Auth::guard('trader')->user();

        if ($trader && !$trader->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        return $next($request);
    }
}
