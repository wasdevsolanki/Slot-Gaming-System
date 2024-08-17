<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Permission;
use App\Models\User;

class StaffController extends Controller
{
    public function index() {
        $data['staff'] = User::where('role', STAFF)
        ->where('room_id', Auth::user()->room_id)
        ->get();
        return view('staff.pages.staff.index', $data);
    }

    public function staffStore(Request $request) {

        $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users,email',
            'password' => 'required|min:6',
            'phone' => 'required',
            'gender' => 'required',
            'position' => 'required',
        ]);

        $staff = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => hash::make($request->password),
            'phone' => $request->phone,
            'gender' => $request->gender,
            'role' => STAFF,
            'position' => $request->position,
            'room_id' => Auth::user()->room_id,
        ]);
        
        if($staff) {
            
            Permission::create([
                'user_id' => $staff->id,
                'player' => $request->player ? $request->player : 0,
                'set_player' => $request->set_player ? $request->set_player : 0,
                'set_player_point' => $request->set_player_point ? $request->set_player_point : 0,
                'machine' => $request->machine ? $request->machine : 0,
                'winning' => $request->winning ? $request->winning : 0,
                'reading' => $request->reading ? $request->reading : 0,
                'chat' => $request->chat ? $request->chat : 0,
                'raffle' => $request->raffle ? $request->raffle : 0,
                'staff' => $request->staff ? $request->staff : 0,
                'bank' => $request->staff ? $request->bank : 0,
                'bank_all' => $request->bank_all ? $request->bank_all : 0,
                'transaction' => $request->transaction ? $request->transaction : 0,
                'setting' => $request->setting ? $request->setting : 0,
            ]);
            
            return redirect()->back()->with('success', 'Staff created successfully');
        }
        return redirect()->back()->with('error', 'Something went wrong');
    }

    public function staffCommonStore(Request $request) {

        $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users,email',
            'gender' => 'required',
            'hourly' => 'required',
        ]);

        $staff = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => hash::make($request->email),
            'phone' => $request->phone,
            'gender' => $request->gender,
            'position' => 'common',
            'hourly' => $request->hourly,
            'role' => STAFF,
            'room_id' => Auth::user()->room_id,
        ]);
        
        if($staff) {
            Permission::create([
                'user_id' => $staff->id,
                'player' => 0,
                'set_player' => 0,
                'set_player_point' => 0,
                'machine' => 0,
                'winning' => 0,
                'reading' => 0,
                'chat' => 0,
                'raffle' => 0,
                'staff' => 0,
                'bank' => 0,
                'bank_all' => 0,
                'transaction' => 0,
                'setting' => 0,
            ]);
            return redirect()->back()->with('success', 'Staff created successfully');
        }
        return redirect()->back()->with('error', 'Something went wrong !');
    }

    public function staffDetail(Request $request)
    {
        $roomId = Auth::user()->room_id;
        $user = User::where('id', $request->id)
            ->where('room_id', $roomId)
            ->first();

        $permission = Permission::select('player', 'set_player', 'set_player_point', 'machine', 'winning', 'bank',  'bank_all', 'transaction', 'reading', 'chat', 'raffle', 'staff', 'setting')
            ->where('user_id', $user->id)
            ->first();
        
        return response()->json(['user' => $user , 'permission' => $permission]);
    }

    public function staffPermissionEdit(Request $request) {
        $request->validate([
            'user_id' => 'required',
        ]);
        
        $permission = Permission::where('user_id', $request->user_id)->first();
        if( $permission ) {
            $permission->update([
                'player' => $request->player ? $request->player : 0,
                'set_player' => $request->set_player ? $request->set_player : 0,
                'set_player_point' => $request->set_player_point ? $request->set_player_point : 0,
                'machine' => $request->machine ? $request->machine : 0,
                'winning' => $request->winning ? $request->winning : 0,
                'reading' => $request->reading ? $request->reading : 0,
                'chat' => $request->chat ? $request->chat : 0,
                'raffle' => $request->raffle ? $request->raffle : 0,
                'staff' => $request->staff ? $request->staff : 0,
                'bank' => $request->bank ? $request->bank : 0,
                'bank_all' => $request->bank_all ? $request->bank_all : 0,
                'transaction' => $request->transaction ? $request->transaction : 0,
                'setting' => $request->setting ? $request->setting : 0,
            ]);
            
            return redirect()->back()->with('success', 'Staff updated successfully');
        }
        return redirect()->back()->with('error', 'Something went wrong !');

    }
}
