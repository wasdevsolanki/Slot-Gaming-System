<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Super
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->role == SUPERADMIN) {
            return $next($request);
        }
        return redirect()->route('super.login')->with('toast_error', 'Unauthorized access');
    }
}
