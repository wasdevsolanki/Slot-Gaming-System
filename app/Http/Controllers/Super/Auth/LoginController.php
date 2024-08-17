<?php

namespace App\Http\Controllers\Super\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    public function login() {
        $data['title'] = 'Login';
        return view('super.auth.login', $data);
    }

    public function loginPost(Request $request) {
        
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);
        
        // Attempt authentication
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            if ( Auth::user()->role == SUPERADMIN ) {
                return redirect()->route('super.dashboard');
            } else {
                Auth::logout();
                return redirect()->back()->with('error', __('Something went wrong!'));
            }
        }

        return redirect()->route('super.login')->with('error', __('Wrong Credential'));
    }

    /**
     * Log out account user.
     *
     * @return \Illuminate\Routing\Redirector
     */
    public function logout()
    {
        Session::flush();
        Auth::logout();
        return redirect('super/login');
    }
}
