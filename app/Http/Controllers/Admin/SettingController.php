<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Permission;
use App\Models\Room;
use App\Models\Setting;

class SettingController extends Controller
{
    public function general() {
        return view('admin.pages.settings.index');
    }
    
    public function generalSetting(Request $request) {
        
        $roomId = session('roomId');
        Setting::where('slug', 'factor')->where('room_id', $roomId)->update(['value' => $request->factor]);
        return redirect()->back()->with('success', 'General Setting Updated Successfully');
    }

    public function point() {
        return view('admin.pages.settings.point');
    }

    public function pointSetting(Request $request) {
        
        $roomId = session('roomId');
        Setting::where('slug', 'min_bonus')->where('room_id', $roomId)->update(['value' => $request->min_bonus]);
        Setting::where('slug', 'max_bonus')->where('room_id', $roomId)->update(['value' => $request->max_bonus]);
        Setting::where('slug', 'bonus_hour')->where('room_id', $roomId)->update(['value' => $request->bonus_hour]);
        return redirect()->back()->with('success', 'Point Setting Updated Successfully');
    }

    public function ticket() {
        return view('admin.pages.settings.ticket');
    }

    public function ticketSetting(Request $request) {
        $roomId = session('roomId');
        Setting::where('slug', 'repoint')->where('room_id', $roomId)->update(['value' => $request->repoint]);
        Setting::where('slug', 'times_ticket')->where('room_id', $roomId)->update(['value' => $request->times_ticket]);
        Setting::where('slug', 'max_ticket')->where('room_id', $roomId)->update(['value' => $request->max_ticket]);
        return redirect()->back()->with('success', 'Ticket Setting Updated Successfully');
    }
}
// Setting::create(['slug' => 'app_title', 'value' => 'Zairito',]);