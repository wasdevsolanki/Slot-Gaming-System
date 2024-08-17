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

class PlayerController extends Controller
{
    public function index()
    {
        $data['players'] = Player::where('room_id', Auth::user()->room_id)->get();

        return view('staff.pages.player.list', $data);
    }

    public function facePlayer($id) {
        $data['pid'] = $id;
        return view('staff.pages.player.profile', $data);
    }

    public function storePlayer(Request $request) 
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|unique:players,email',
            'gender' => 'required',
            'bonus' => 'required',
        ]);

        $bonus = $request->bonus;
        if ($bonus >= allsetting('min_bonus') && $bonus <= allsetting('max_bonus')) {

            $roomId = Auth::user()->room_id;
            $player = Player::create([
                'ref_id' =>   $request->ref_id,
                'name' =>   $request->name,
                'creator_id' =>   Auth::id(),
                'email' =>  $request->email,
                'phone' =>  $request->phone,
                'dob' =>  $request->dob,
                'gender' =>  $request->gender,
                'dl' =>  $request->driving_license,
                'room_id' =>  $roomId,
                'ssn' =>  $request->ssn,
                'bonus' =>  $request->bonus,
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
            return redirect()->route('staff.player.face', ['id' => encrypt($player->id)]);

        } else {

            return redirect()->back()->with('error', 'Please select correct Range!');
        }
    }

    public function faceStorePlayer(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'profile' => 'required',
        ]);

        //        $roomId = Auth::user()->room_id;
        //        $directory = 'upload/' . $roomId .  '/profile';
        //        $image = $request->file('test_profile');
        //        if ( file_exists($directory) ) {
        //
        //            $filename = 2 . '.' . $image->getClientOriginalExtension();
        //            fileUpload($image, $directory . '/', $filename);
        //            dd("Upload");
        //        } else {
        //
        //            dd("0");
        //        }
        //
        //        dd('exit');

        $player = Player::find(decrypt($request->id));
        $roomId = Auth::user()->room_id;

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

        return redirect()->route('staff.dashboard')->with('success','Profile added successfully!');

    }

    public function ajaxplayers()
    {
        $players = Player::where('profile', '!=', null )
            ->where('room_id', Auth::user()->room_id)
            ->get();

        return response()->json(['players' => $players]);
    }

    public function ajax_profile_match(Request $request)
    {
        $profile = $request->profile;
        $data = Str::beforeLast($profile, '.');
        $player = Player::where('id', $data)->where('status', 1)->first();
        return response()->json($player);
    }

    public function PlayerRef(Request $request)
    {
        $reference = $request->input('reference');
        $users = Player::where('name', 'like', '%'.$reference.'%')->get();
       return  response()->json(['data'=>$users],200);
    }

    // Players
    public function PlayerDetail(Request $request)
    {
        $roomId = Auth::user()->room_id;
        $player = Player::where('id', $request->id)
            ->where('room_id', $roomId)
            ->first();
        return response()->json($player);
    }

    public function PlayerStatus($id)
    {
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
