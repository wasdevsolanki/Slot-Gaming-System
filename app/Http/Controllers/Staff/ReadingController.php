<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use App\Models\Machine;
use App\Models\Reading;
use App\Models\Room;

class ReadingController extends Controller
{
    public function index() {
        
        $data['readings'] = Reading::with(['machines' => function($query) {
            $query->where('room_id', Auth::user()->room_id);
        }])
            ->where('room_id', Auth::user()->room_id)
            ->whereDate('in_date', Carbon::today())
            ->where('out_date', '!=', null)
            ->get();

        $record = [];
        foreach ($data['readings'] as $item) {

            $prevIn = Rprev( $item->machines->first()->id ) ? Rprev( $item->machines->first()->id)->in : 0;
            $prevOut = Rprev( $item->machines->first()->id ) ? Rprev( $item->machines->first()->id)->out : 0;

            $type = $item->machines->first()->type;
            $in = $item->in - $prevIn;
            $out = $item->out - $prevOut;
            $diff = $in - $out;
            
            if (!isset($record[$type])) {
                $record[ $type ] = [
                    'in' => 0,
                    'out' => 0,
                    'diff' => 0,
                ];
            }
        
            $record[$type]['in'] += $in;
            $record[$type]['out'] += $out;
            $record[$type]['diff'] += $diff;

        }

        $data['record'] = $record;
        $data['all_machines'] = Machine::where('room_id', Auth::user()->room_id)->get();
        $data['machines'] = Machine::where('room_id', Auth::user()->room_id)->get()->groupBy('type');
       
        return view('staff.pages.reading.index', $data);
    }

    public function readingIn(Request $request) {

        $request->validate([
            'machine' => 'required',
            'in' => 'required',
            'in_date' => 'required',
        ]);

        $reading = Reading::create([
            'machine_id' => $request->machine,
            'employee_id' => Auth::id(),
            'room_id' => Auth::user()->room_id,
            'in_date' => $request->in_date,
            'in' => $request->in,
        ]);

        if ( $reading ) {
            return redirect()->back()->with('success', 'Reading In Successfully');
        } else {
            return redirect()->back()->with('error', 'Reading In Failed');
        }
    }

    public function readingOut(Request $request) {
        $request->validate([
            'machine' => 'required',
            'out' => 'required',
            'out_date' => 'required',
        ]);

        $reading = Reading::where('machine_id', $request->machine)
                    ->whereDate('in_date', Carbon::today())
                    ->whereNull('out_date')
                    ->first();
 
        if (! is_null($reading) ) {

            $reading->update([
                'out_date' => $request->out_date,
                'out' => $request->out,
            ]);

            return redirect()->back()->with('success', 'Reading Out Added');
        } else {
            return redirect()->back()->with('error', 'First Add In Reading');
        }
    }

}