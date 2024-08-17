<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Player;
use App\Models\Point;

use Illuminate\Support\Facades\Http;

class PointController extends Controller
{
    public function PlayerPointStore( Request $request )
    {

        $request->validate([
            'player_id' => 'required',
            'amount' => 'required',
            'player_bonus' => 'required',
            'player_name' => 'required',
        ]);

        $isPoint = Point::where('player_id', $request->player_id)
            ->whereDate('created_at', Carbon::today())
            ->where('status', 0)
            ->first();

        if( ! empty($isPoint) ) {
            return redirect()->back()->with('error', 'Player is already in pending list !');
        }

        $isPoint = Point::where('player_id', $request->player_id)
            ->whereDate('created_at', Carbon::today())
            ->where('status', 1)
            ->first();

        if( ! empty($isPoint) ) {
            return redirect()->back()->with('error', 'Player is already checkin !');
        }

        $roomId = session('roomId');
        $point = Point::create([
            'amount' => $request->amount,
            'player_id' => $request->player_id,
            'room_id' => $roomId,
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Point Assigned Successfully');
    }

    public function PlayerPointCheckIn(Request $request) {
        $request->validate([
            'player_id' => 'required',
            'machine' => 'required',
            'checkin' =>  'required'
        ]);

        $point = Point::where('player_id', $request->player_id)
                ->where('room_id', session('roomId'))
                ->where('machine_id', null)
                ->where('status', 0)
                ->whereDate('created_at', Carbon::today())
                ->first();

        if( ! is_null($point) ) {
            $point->update([
                'status' => 1,
                'machine_id' => $request->machine,
                'checkin'   => $request->checkin
            ]);
            return redirect()->back()->with('success', 'Player Checked In Successfully');
        }

        return redirect()->back()->with('error', 'Player Not Found');
    }

    public function PlayerPointCheckout(Request $request)
    {
        $request->validate([
            'point_id' => 'required',
            'checkout_input' => 'required'
        ]);

        $point = Point::find($request->point_id);
        $point->update([
            'status' => 2,
            'checkout' => $request->checkout_input
        ]);
        return redirect()->back()->with('success', 'Player Checked Out Successfully');
    }

    public function PlayerPointStoreForm( Request $request )
    {
        $request->validate([
            'player_id' => 'required',
            'point' => 'required',
        ]);
        
        $isPoint = Point::where('player_id', $request->player_id)
            ->whereDate('created_at', Carbon::today())
            ->where('status', 0)
            ->first();
        
        if( ! empty($isPoint) ) {
            return redirect()->back()->with('error', 'Player is already in pending list !');
        }

        $isPoint = Point::where('player_id', $request->player_id)
            ->whereDate('created_at', Carbon::today())
            ->where('status', 1)
            ->first();

        if( ! empty($isPoint) ) {
            return redirect()->back()->with('error', 'Player is already checkin !');
        }

        $roomId = session('roomId');
        $point = Point::create([
            'amount' => $request->point,
            'player_id' => $request->player_id,
            'room_id' => $roomId,
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Point Assigned Successfully');
    }

    public function pointImage() {
        return view('admin.pages.player.point-image');
    }

    public function location(Request $request) {
        $ip = $request->ip();
        $response = Http::get("http://ip-api.com/json/{$ip}");
        return $response->json();
    }
}
