<?php

namespace App\Http\Controllers\Super;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Permission;
use App\Models\Setting;
use App\Models\User;
use App\Models\Admin;
use App\Models\Room;

class AdminController extends Controller
{
    public function index() {
        $data['admins'] = Admin::all();
        $data['title'] = 'Admin';
        return view('super.pages.admin.index', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'gender' => 'required',
            'rooms' => 'required',
            'phone' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
        ]);

        $guid = Str::uuid()->toString();
        $admin = Admin::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'key' => $this->generateUniqueKey(),
            'gender' => $request->gender,
            'location' => $guid,
        ]);

        if( $admin ) {
            for( $i=1; $i<=$request->rooms; $i++ ) {

                $room = Room::create([
                    'name' => 'Room-'.$i,
                    'admin_id' => $admin->id,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                ]);

                if( $room ) {
                    DB::table('room_admin')->insert([
                        'room_id' => $room->id,
                        'admin_id' => $admin->id
                    ]);

                    Setting::create(['slug' => 'factor', 'value' => 1.0,        'room_id' => $room->id,]);
                    Setting::create(['slug' => 'min_bonus', 'value' => 20,       'room_id' => $room->id,]);
                    Setting::create(['slug' => 'max_bonus', 'value' => 100,     'room_id' => $room->id,]);
                    Setting::create(['slug' => 'bonus_hour', 'value' => 4,    'room_id' => $room->id,]);
                    Setting::create(['slug' => 'repoint', 'value' => null,       'room_id' => $room->id,]);
                    Setting::create(['slug' => 'times_ticket', 'value' => 3,  'room_id' => $room->id,]);
                    Setting::create(['slug' => 'max_ticket', 'value' => 10000,    'room_id' => $room->id,]);
                }
            }

            return redirect()->back()->with('success', 'Succesfully Added!');
        } else {
            return redirect()->back()->with('error', 'Something went wrong!');
        }
    }

    public function generateUniqueKey()
    {
        $key = Str::random(16);
        $existingAdmin = Admin::where('key', $key)->first();

        if ($existingAdmin) {
            return $this->generateUniqueKey();
        }
        return $key;
    }

}
