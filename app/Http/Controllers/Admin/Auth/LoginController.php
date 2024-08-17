<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Admin;
use App\Models\Room;

class LoginController extends Controller
{
    public function login() {

        if(session::has('key')) {
            return redirect()->route('admin.dashboard');
        }

        $data['title'] = 'Login';
        return view('admin.auth.login', $data);
    }

    public function loginPost(Request $request) {
        $request->validate([
            'key' => 'required',
        ]);

        $admin = Admin::where('key', $request->key)->first();
        if( $admin && $admin->status == 2 ) {
            
            Session::put('key', $admin->id);
            return redirect()->route('install.reading');
        }

        if ( $admin ) {

            Session::put('key', $admin->id);
            $data['rooms'] = Room::where('admin_id', $admin->id)->get();

            if( $admin->status == 0 ) {
                return redirect()->route('install.setup');
            }

            return view('admin.pages.room.index', $data);
        } else {
            return redirect()->back()->with('error', __('Something went wrong!'));
        }
    }

     /**
     * Log out account user.
     *
     * @return \Illuminate\Routing\Redirector
     */
    public function logout()
    {
        Session::flush();
        return redirect('admin/login');
    }

    public function validateAdmin(Request $request) {

        $requestfile = $request->json()->all();
        $Content = $requestfile['file'];
        $data = decrypt($Content);
        $isAdmin = Admin::where('location', $data)->first();

        if( $isAdmin ){
            return response()->json([
                'status' => 'true',
                'data' => $isAdmin,
            ], 200);
            
        } else {
            return response()->json([
                'status' => 'false',
                'data' => '',
            ], 200);
        }

    }

}


// $auth_token = 'AUTH_TOKEN';
// $url = 'https://webhook.site/bd414316-35f1-4e4d-90ec-fe3132eecd2b';
// $data = $request->json()->all();

// $ch = curl_init($url);
// curl_setopt($ch, CURLOPT_POST, 1);
// curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
// curl_setopt($ch, CURLOPT_HTTPHEADER, array(
//   'Authorization: Bearer ' . $auth_token,
//   'Content-Type: application/json'
// ));
// $result = curl_exec($ch);