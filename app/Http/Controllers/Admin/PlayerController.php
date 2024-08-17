<?php

namespace App\Http\Controllers\Admin;

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
use App\Models\Player;
use App\Models\Ticket;
use App\Models\Admin; 
use App\Models\Point;
use App\Models\Room;
use App\Models\User;

class PlayerController extends Controller
{
    public function index()
    {
        $data['players'] = Player::where('room_id', session('roomId'))->get();
        return view('admin.pages.player.list', $data);
    }

    public function storePlayer(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users,email',
            'gender' => 'required',
            'bonus' => 'required',
        ]);

        $bonus = $request->bonus;
        if ($bonus >= allsetting('min_bonus') && $bonus <= allsetting('max_bonus')) {

            // dd($request->all());
            $roomId = session('roomId');
            $player = Player::create([
                'ref_id' =>   $request->ref_id ? $request->ref_id : null,
                'name' =>   $request->name,
                'email' =>  $request->email,
                'phone' =>  $request->phone ? $request->phone : null,
                'dob' =>  $request->dob ? $request->dob : null,
                'gender' =>  $request->gender,
                'dl' =>  $request->driving_license ? $request->driving_license : null,
                'ssn' =>  $request->ssn ? $request->ssn : null,
                'bonus' =>  $request->bonus,
                'room_id' =>  $roomId,
                'status' => 1,
            ]);

            if(! is_null($request->document) ) {

                $directory = 'upload/' . $roomId .  '/document';
                $image = $request->file('document');
                if ( file_exists($directory) ) {

                    $filename = $player->id . '.' . $image->getClientOriginalExtension();
                    fileUpload($image, $directory . '/', $filename);
                    $player->update([
                        'document'   =>  $filename,
                    ]);

                } else {

                    mkdir($directory, 0755, true);
                    $filename = $player->id . '.' . $image->getClientOriginalExtension();
                    fileUpload($image, $directory . '/', $filename);
                    $player->update([
                        'document'   =>  $filename,
                    ]);
                }
            }
            return redirect()->route('admin.player.face', ['id' => encrypt($player->id)]);

        } else {
            return redirect()->back()->with('error', 'Please select correct Range!');
        }
    }

    public function PlayerRef(Request $request)
    {
        $reference = $request->input('reference');
        $users = Player::where('name', 'like', '%'.$reference.'%')->where('room_id', session('roomId'))->get();
       return  response()->json(['data'=>$users],200);
    }

    public function facePlayer($id)
    {
        $data['pid'] = $id;
        return view('admin.pages.player.profile', $data);
    }

    public function faceStorePlayer(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'profile' => 'required',
        ]);

        $player = Player::find(decrypt($request->id));
        $roomId = session('roomId');

        $directory = 'upload/' . $roomId .  '/profile';
        $image = $request->file('profile');
        if ( file_exists($directory) ) {

            $filename = $player->id . '.' . $image->getClientOriginalExtension();
            fileUpload($image, $directory . '/', $filename);
            $player->update([
                'profile'   =>  $filename,
            ]);

        } else {

            mkdir($directory, 0755, true);
            $filename = $player->id . '.' . $image->getClientOriginalExtension();
            fileUpload($image, $directory . '/', $filename);
            $player->update([
                'profile'   =>  $filename,
            ]);
        }

        return redirect()->route('admin.dashboard')->with('success','Profile added successfully!');
    }

    public function ajaxplayers() {
        $players = Player::where('profile', '!=', null )
            ->where('room_id', session('roomId'))
            ->get();
        return response()->json(['players' => $players]);
    }

    public function PlayerDetail(Request $request) {

        $player = Player::where('id', $request->id)->first();
        
        $points = Point::with('get_machine')
            ->where('player_id', $player->id)
            ->whereNotNull('checkin')
            ->whereNotNull('checkout')
            ->get();

        return response()->json([
            'player' => $player,
            'points' => $points,
        ]);
    }

    public function playerHistory(Request $request) {

        $data['player'] = Player::where('id', $request->player_id)->first();
        $data['points'] = Point::with(['get_machine', 'employee'])
            ->where('player_id', $data['player']['id'])
            ->whereNotNull('checkin')
            ->whereNotNull('checkout')
            ->get();

        if(count($data['points']) == 0){
            return redirect()->back()->with('error', 'Record not found !');
        }

        $data['tickets'] = Ticket::with(['player', 'machine', 'point', 'employee', 'admin'])
            ->where('player_id', $data['player']['id'])
            ->get();
        
        $room = Room::select('admin_id')->where('id', $data['player']['room_id'])->first();
        $data['admin'] = getAdmin($room->admin_id)->name;

        return view('admin.pages.player.history', $data);
    }

    public function ajax_profile_match(Request $request) {
        
        $profile = $request->profile;
        $data = Str::beforeLast($profile, '.');

        $player = Player::with(['points' => function($query) {
            $query->where('status', '!=', 1);
        }])
            ->where('id', $data)
            ->where('status', 1)
            ->where('room_id', session('roomId'))
            ->first();

        return response()->json($player);
    }

    public function PlayerStatus($id)
    {
        $id = decrypt($id);
        $player = Player::find($id);
        if (!$player) {
            return redirect()->back()->with('error', 'Player not found!');
        }
        $player->update(['status' => 1]);

        return redirect()->back()->with('success', 'Player approved!');
    }

    // Search Player
    public function searchPlayer(Request $request)
    {
        $reference = $request->input('reference');
        $players = Player::where('name', 'like', '%'.$reference.'%')->get();
        return  response()->json(['data'=> $players],200);
    }
}