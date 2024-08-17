<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use App\Models\Machine;
use App\Models\Player;
use App\Models\Room;
use App\Models\Point;

class DashboardController extends Controller
{
    public function  dashboard() 
    {
        if (! Session::has('roomId')) {
            session::flush();
            return redirect()->route('admin.login')->with('toast_error', 'Unauthorized access');
        }

        $data['players'] = Player::with('points')->where('room_id', session('roomId'))
            ->whereHas('points', function ($query) {
                $query->whereDate('created_at', Carbon::today());
            })->get();
        $busyMachine = Point::where('room_id', session('roomId'))->where('status', 1)->get()->pluck('machine_id');
        $data['machines'] = Machine::whereNotIn('id', $busyMachine)
            ->where('room_id', session('roomId'))
            ->where('status', 1)
            ->get();

        $data['player_requests'] = Player::where('room_id', session('roomId'))
        ->where('status', 0)
        ->get();

        // $activePlayer = Player::with(['points' => function($query) {
        //     $query->whereDate('created_at', Carbon::today())
        //     ->where('status', 0)->whereOr('status', 1);
        // }])
        //     ->where('room_id', session('roomId'))
        //     ->get();

        // dd($activePlayer);

        $data['all_player'] = Player::with(['points' => function($query){
            $query->where('status', '!=', 0)
                ->where('status', '!=', 1)
                ->whereDate('created_at', Carbon::today());
        }])->where('room_id', session('roomId'))
            ->where('status', 1)
            ->get();
        
        // dd($data['all_player']);

        return view('admin.dashboard', $data);
    }

    public function switchRoom($id) {
        $id = decrypt($id);
        Session::put('roomId', $id);
        return redirect()->route('admin.dashboard');
    }
}
