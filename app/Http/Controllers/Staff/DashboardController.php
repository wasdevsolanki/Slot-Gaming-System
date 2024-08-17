<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Machine;
use App\Models\Point;
use App\Models\User;
use App\Models\Player;


class DashboardController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    } 
    
    public function  dashboard() 
    {
        $data['players'] = Player::with(['points' => function($query) {
            $query->whereDate('created_at', Carbon::today());
        }])
            ->where('room_id', Auth::user()->room_id)
            ->get();

        $busyMachine = Point::where('room_id', Auth::user()->room_id)->where('status', 1)->get()->pluck('machine_id');
        $data['machines'] = Machine::whereNotIn('id', $busyMachine)
            ->where('room_id', Auth::user()->room_id)
            ->where('status', 1)
            ->get();
        
        $data['player_requests'] = Player::where('room_id', Auth::user()->room_id)
            ->where('status', 0)
            ->get();

        $data['all_player'] = Player::where('room_id', Auth::user()->room_id)
            ->where('status', 1)
            ->get();

        return view('staff.dashboard', $data);
    }
}
