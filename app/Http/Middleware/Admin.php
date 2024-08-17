<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class Admin
{
    public function handle(Request $request, Closure $next)
    {

        if (Session::has('key')) {
            
            $key = session('key');
            $admin = \App\Models\Admin::where('id', $key)->first();

            if(is_null($admin)) {
                session::flush();
                return redirect()->route('admin.login')->with('toast_error', 'Unauthorized access');
            }
    
            if( $admin && $admin->status == 0 ) {
                return redirect()->route('install.setup');
            } else if( $admin && $admin->status == 2) {
                return redirect()->route('install.reading');
            } else {
                return $next($request);
            }
        }

        session::flush();
        return redirect()->route('admin.login')->with('toast_error', 'Unauthorized access');
    }
}
