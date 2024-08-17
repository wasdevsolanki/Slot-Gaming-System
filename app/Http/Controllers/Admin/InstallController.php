<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Admin;
use App\Models\Room;
use App\Models\Machine;
use App\Models\Reading;

class InstallController extends Controller
{
    public function install() {
        $data['title'] = 'Install';
        $data['install'] = true;
        $data['admin'] = Admin::with('rooms')->where('id', session('key'))->first();
        return view('admin.pages.install.index', $data);
    }

    public function createPin(Request $request) {
        $range = $request->range;
        $pinLength = intval($range);
        $pin = $this->generateUniquePin($pinLength);
        return $pin;
    }

    public function generateUniquePin($length) {
        do {
            $pin = '';
            for ($i = 0; $i < $length; $i++) {
                $pin .= rand(0, 9);
            }
            $existingAdmin = Admin::where('key', $pin)->first();
        } while ($existingAdmin);
    
        return $pin;
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'pincode' => 'required|min:4|unique:admins,key',
        ]);

        // $ipAddress = $request->ip();
        // $response = Http::get("http://ip-api.com/json/{$ipAddress}");

        $admin = Admin::where('id', session('key'))->first();
        $admin->update([
            'key'   => $request->pincode,
            'status'    => 2,
        ]);

        $data = $request->except([
            '_token',
            'pincode',
        ]);
        
        foreach ($data as $key => $value) {
            if (!Str::startsWith($key, 'qty_')) {
               Room::where('id', $key)->update(['name' => $value, 'status' => 1]);
            } else {
                
                $roomId = Str::of( $key )->after('qty_');
                if( $value && $roomId ) {
                    for( $i=1; $i<=$value; $i++ ){
                        Machine::create([
                            'room_id' => $roomId,
                            'type' => 'Machine- '.$i,
                            'game' => 'Game- '.$i,
                            'serial' => '00'.$i,
                        ]);
                    }
                }

            } 
        }

        return redirect()->route('admin.dashboard')->with('success', 'Your are welcome !');
    }

    public function readingStore() {

        if(! Session::has('key')) {
            Session::flush();
            return redirect('admin/login');
        }

        $data['title'] = 'Reading Setup';
        $data['install'] = true;
        $data['rooms']= Room::with('machines')->where('admin_id', session('key'))->get();
        return view('admin.pages.install.reading', $data);
    }

    public function readingSave(Request $request) {

        $request->validate([
            'data' => 'required',
        ]);

        $data = $request->data;
        $room = Room::where('admin_id', session('key'))->get();

        if( $room && $room->count() != count($data) ) {
            return redirect()->back()->with('error', 'Something went wrong !');
        }

        foreach( $data as $room => $machines ){
            foreach( $machines as $key => $value ){
                
                $machine = Machine::find($key);
                $machine->update([
                    'serial'  => $value['serial'],
                    'type'    => $value['type'],
                    'game'    => $value['game'],
                    'room_id' => $room,
                    'status'  => 1,
                ]);

                if( $machine ){
                    Reading::create([
                        'machine_id'  => $machine->id,
                        'admin_id' => session('key'),
                        'room_id'     => $room,
                        'in_date'     => Carbon::yesterday(),
                        'out_date'    => Carbon::yesterday(),
                        'in'          => $value['in_reading'],
                        'out'         => $value['out_reading'],
                        'created_at'  => Carbon::yesterday(),
                    ]);
                }
            }
        }

        Admin::where('id', session('key'))->update(['status' => 1]);
        return redirect()->route('admin.dashboard')->with('success', 'Installation Complete !');
    }
}
