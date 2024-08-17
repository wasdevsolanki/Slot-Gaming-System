<?php
 
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Crypt; 
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Models\Transaction;
use App\Models\Permission;
use App\Models\Setting;
use App\Models\Reading;
use App\Models\Payroll;
use App\Models\Player;
use App\Models\Ticket;
use App\Models\Admin;
use App\Models\User;
use App\Models\Room;
use App\Models\Bank;

if(! function_exists('fileUpload'))
{
    function fileUpload($img, $path, $user_file_name = null, $width = null, $height = null)
    {
        
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        if (isset($user_file_name) && $user_file_name != "" && file_exists($path . $user_file_name)) {
            unlink($path . $user_file_name);
        }

        // saving image in target path
        // $imgName = uniqid() . time() . '.' . $img->getClientOriginalExtension();

        $imgName = $user_file_name;
        $imgPath = ($path . $imgName);
        // making image
        $makeImg = Image::make($img)->orientate();
        if ($width != null && $height != null && is_int($width) && is_int($height)) {
            $makeImg->fit($width, $height);
        }
        if ($makeImg->save($imgPath)) {
            return $imgName;
        }
        return false;
    }
}

if(! function_exists('admin'))
{
    function admin(){
        return Admin::where('id', session('key'))->first();
    }
}

if(! function_exists('Payroll'))
{
    function Payroll(){
        return Payroll::where('staff_id', Auth::id())
            ->whereDate('checkin', Carbon::today())
            ->first();
    }
}

if(! function_exists('getAdmin'))
{
    function getAdmin($id){
        return Admin::where('id', $id)->first();
    }
}

if(! function_exists('getStaff'))
{
    function getStaff($id){
        return User::where('id', $id)->where('role', STAFF)->first();
    }
}

if(! function_exists('room'))
{
    function room()
    {
        return Room::where('id', session('roomId'))->first();
    }
}

if(! function_exists('staffBalance'))
{
    function staffBalance()
    {
        $bank = Bank::where('reciever_id', Auth::id())
            ->whereDate('created_at', Carbon::today())
            ->first();

        $recieved = Transaction::where('reciever_id', Auth::id())
            ->whereDate('created_at', Carbon::today())
            ->where('type', 'transfer')
            ->sum('amount');
        return $bank;
    }
}

if(! function_exists('remainBalance')) 
{
    function remainBalance()
    {
        $ticketSum = Ticket::where('employee_id', Auth::id())
            ->whereDate('created_at', Carbon::today())
            ->sum('amount');

        $transSum = Transaction::where('staff_id', Auth::id())
            ->whereDate('created_at', Carbon::today())
            ->whereNot('type', 'bank')
            ->sum('amount');
        
        $transferSum = Transaction::where('reciever_id', Auth::id())
            ->whereDate('created_at', Carbon::today())
            ->where('type', 'transfer')
            ->sum('amount');
        
        $total = staffBalance() ? staffBalance()->total + $transferSum : 0;
        $balance = $total - ($ticketSum + $transSum);
        
        return $balance ? $balance : 0;
    }
}

if(! function_exists('Rprev'))
{
    function Rprev($id, $range = null)
    {
        $previous = $range ? Carbon::createFromDate($range)->subDays(1) : Carbon::yesterday();

        if( Auth::user() && Auth::user()->role == STAFF ) {
            $readingPrev = Reading::with(['machines' => function($query) {
                $query->where('room_id', Auth::user()->room_id);
            }])
            ->where('machine_id', $id)
            ->whereDate('in_date', $previous)
            ->where('out_date', '!=', null)
            ->first();
        } else {

            $readingPrev = Reading::with(['machines' => function($query) {
                $query->where('room_id', session('roomId'));
            }])
            ->where('machine_id', $id)
            ->whereDate('in_date', $previous)
            ->where('out_date', '!=', null)
            ->first();
        }
        
        if(! is_null($readingPrev)) {
            return $readingPrev;
        } else {
            return null;
        }
    }
}

if(! function_exists('Rcurr'))
{
    function Rcurr($id, $range = null)
    {
        $current = $range ? $range : Carbon::today();
        if( Auth::user() && Auth::user()->role == STAFF ) {
            $readingCurr = Reading::with(['machines' => function($query) {
                $query->where('room_id', Auth::user()->room_id);
            }])
            ->where('machine_id', $id)
            // ->whereDate('in_date', Carbon::today())
            ->whereDate('in_date', $current)
            ->where('out_date', '!=', null)
            ->first();
        } else {

            $readingCurr = Reading::with(['machines' => function($query) {
                $query->where('room_id', session('roomId'));
            }])
            ->where('machine_id', $id)
            // ->whereDate('in_date', Carbon::today())
            ->whereDate('in_date', $current)
            ->where('out_date', '!=', null)
            ->first();
        }
    
        if(! is_null($readingCurr)) {
            return $readingCurr;
        } else {
            return null;
        }
    }
}

if(! function_exists('getAllRoom'))
{
    function getAllRoom()
    {
        return Room::where('admin_id', session('key'))->get();
    }
}

if(! function_exists('timezone'))
{
    function timezone()
    {
        return [
            'America/Adak',              // Hawaii-Aleutian Time (HAST/HADT)
            'America/Anchorage',         // Alaska Time (AKST/AKDT)
            'America/Boise',             // Mountain Time (MST/MDT)
            'America/Chicago',           // Central Time (CST/CDT)
            'America/Denver',            // Mountain Time (MST/MDT)
            'America/Detroit',           // Eastern Time (EST/EDT)
            'America/Indiana/Indianapolis', // Eastern Time (EST/EDT)
            'America/Indiana/Knox',      // Central Time (CST/CDT)
            'America/Indiana/Marengo',   // Eastern Time (EST/EDT)
            'America/Indiana/Petersburg', // Eastern Time (EST/EDT)
            'America/Indiana/Tell_City', // Central Time (CST/CDT)
            'America/Indiana/Vevay',     // Eastern Time (EST/EDT)
            'America/Indiana/Vincennes', // Eastern Time (EST/EDT)
            'America/Indiana/Winamac',   // Eastern Time (EST/EDT)
            'America/Juneau',            // Alaska Time (AKST/AKDT)
            'America/Kentucky/Louisville', // Eastern Time (EST/EDT)
            'America/Kentucky/Monticello', // Eastern Time (EST/EDT)
            'America/Los_Angeles',       // Pacific Time (PST/PDT)
            'America/Menominee',         // Central Time (CST/CDT)
            'America/Metlakatla',        // Alaska Time (AKST/AKDT)
            'America/New_York',          // Eastern Time (EST/EDT)
            'America/Nome',              // Alaska Time (AKST/AKDT)
            'America/North_Dakota/Beulah', // Central Time (CST/CDT)
            'America/North_Dakota/Center', // Central Time (CST/CDT)
            'America/North_Dakota/New_Salem', // Central Time (CST/CDT)
            'America/Phoenix',           // Mountain Standard Time (MST)
            'America/Sitka',             // Alaska Time (AKST/AKDT)
            'America/Yakutat',           // Alaska Time (AKST/AKDT)
            'Pacific/Honolulu',          // Hawaii-Aleutian Time (HAST/HADT)
        ];

    }
}

if(! function_exists('checkPermit'))
{
    function checkPermit($auth)
    {
        return Permission::where('user_id', $auth)->first();
    }
}

if(! function_exists('newPlayers'))
{
    function newPlayers()
    {
        return Player::where('status', 0)->get();
    }
}


function allsetting($array = null)
{
    if( ! is_null(session('roomId')) || Auth::user() && ! is_null(Auth::user()->room_id) ) {
        $roomId = session('roomId') ? session('roomId') : Auth::user()->room_id;
        if (!isset($array[0])) {
            $allsettings = Setting::where('room_id', $roomId)->get();
            if ($allsettings) {
                $output = [];
                foreach ($allsettings as $setting) {
                    $output[$setting->slug] = $setting->value;
                } 
                return $output;
            }
            return false;
        } elseif (is_array($array)) {
            $allsettings = Setting::whereIn('slug', $array)->where('room_id', $roomId)->get();
            if ($allsettings) {
                $output = [];
                foreach ($allsettings as $setting) {
                    $output[$setting->slug] = $setting->value;
                }
                return $output;
            }
            return false;
        } else {
            $allsettings = Setting::where(['slug' => $array])->where('room_id', $roomId)->first();
            if ($allsettings) {
                $output = $allsettings->value;
                return $output;
            }
            return false;
        }
    }
}


// function allsetting($array = null)
// {
//     if (!isset($array[0])) {
//         $allsettings = Setting::get();
//         if ($allsettings) {
//             $output = [];
//             foreach ($allsettings as $setting) {
//                 $output[$setting->slug] = $setting->value;
//             } 
//             return $output;
//         }
//         return false;
//     } elseif (is_array($array)) {
//         $allsettings = Setting::whereIn('slug', $array)->get();
//         if ($allsettings) {
//             $output = [];
//             foreach ($allsettings as $setting) {
//                 $output[$setting->slug] = $setting->value;
//             }
//             return $output;
//         }
//         return false;
//     } else {
//         $allsettings = Setting::where(['slug' => $array])->first();
//         if ($allsettings) {
//             $output = $allsettings->value;
//             return $output;
//         }
//         return false;
//     }
// }