<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use App\Models\Reading;
use App\Models\Machine; 
use App\Models\Room;

class MachineController extends Controller
{
    public function index() {

        // $data['machines'] = Machine::with(['c_readings' => function($query){
        //     $query->whereDate('in_date', Carbon::today())
        //         ->where('out_date', null)
        //         ->where('out', null);
        // }])->where('room_id', session('roomId'))->get();

        $data['machines'] = Machine::where('room_id', session('roomId'))->get();

        return view('admin.pages.machine.index', $data);
    }

    public function ajaxMachineInfo (Request $request) {

        $machine = Machine::find($request->id);

        $current = Reading::where('machine_id', $machine->id)
        ->whereDate('in_date', Carbon::today())
        ->first();

        $previous = Reading::where('machine_id', $machine->id)
        ->whereDate('in_date', Carbon::yesterday())
        ->first();

        return response()->json([
            'machine' => $machine,
            'current' => $current,
            'previous' => $previous,
        ]);
    }

    public function editMachine(Request $request) {

        $request->validate([
            'type' => 'required',
            'game' => 'required',
            'machine_id' => 'required',
        ]);
        
        $machine = Machine::find($request->machine_id);
        $machine->update([
            'type' => $request->type,
            'game' => $request->game,
        ]);
        
        if( ! is_null($request->in)  || ! is_null($request->out) ) {

            if( $request->in && $request->in <= 0 ) {
                return redirect()->back()->with('error', 'Value should greater than 0 !');
            }

            if( $request->out && $request->out <= 0 ) {
                return redirect()->back()->with('error', 'Value should greater than 0 !');
            }

            $isReading = Reading::where('machine_id', $request->machine_id)
                ->whereDate('in_date', Carbon::today())
                ->first();

            if( $request->out && $request->out > $isReading->in ) {
                return redirect()->back()->with('error', 'Reading out should not greater than reading In !');
            }

            if( $isReading ) {
                $isReading->update([
                    'machine_id' => $machine->id,
                    'admin_id' => session('key'),
                    'in_date' => $request->today ? $request->today : $isReading->in_date,
                    'out_date' => $request->today ? $request->today : $isReading->out_date, 
                    'in' => $request->in ? $request->in : $isReading->in,
                    'out' => $request->out ? $request->out : $isReading->out,
                    'room_id' => session('roomId'),
                ]);
            } else {
                Reading::create([
                    'machine_id' => $machine->id,
                    'admin_id' => session('key'),
                    'in_date' => $request->today,
                    'out_date' => $request->today, 
                    'in' => $request->in,
                    'room_id' => session('roomId'),
                ]);
            }
        }
        return redirect()->back()->with('success', 'Machine updated successfully');
    }

    public function storeMachine(Request $request) {

        $request->validate([
            'type' => 'required',
            'game' => 'required',
            'serial' => 'required',
        ]);

        $serialExist = Machine::where('serial', $request->serial)
            ->where('room_id', session('roomId'))
            ->exists();

        if( $serialExist ) {
            return redirect()->back()->with('error', 'Machine serial already in use!');
        }

        $machine = Machine::create([
            'type' => $request->type,
            'game' => $request->game,
            'serial' => $request->serial,
            'room_id' => session('roomId'),
        ]);

        if(! is_null($machine) ){
            return redirect()->back()->with('success', 'Machine added successfully');
        }
        return redirect()->back()->with('error', 'Something went wrong!');
    }

    public function activeMachine($id){
        $id = decrypt($id);
        $machine = Machine::find($id);

        if( $machine ) {
            $machine->update(['status' => 1]);
            return redirect()->back()->with('success', 'Machine Activated!');
        }
        return redirect()->back()->with('error', 'Something went wrong!');
    }

    public function blockMachine($id){
        $id = decrypt($id);
        $machine = Machine::find($id);

        if( $machine ) {
            $machine->update(['status' => 0]);
            return redirect()->back()->with('success', 'Machine Blocked!');
        }
        return redirect()->back()->with('error', 'Something went wrong!');
    }
}
