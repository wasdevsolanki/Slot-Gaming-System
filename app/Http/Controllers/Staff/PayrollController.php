<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use App\Models\Payroll;
use App\Models\User;

class PayrollController extends Controller
{
    public function index() {

        $data['staffs'] = User::with(['payroll' => function($query){
            $query->whereDate('checkin', Carbon::today());
        }])->where('room_id', Auth::user()->room_id)->where('role', STAFF)->get();
        return view('staff.pages.payroll.index', $data);
    }

    public function staffDetail(Request $request)
    {
        $roomId = Auth::user()->room_id;

        $user = User::with(['payroll' => function($query){
            $query->orderBy('created_at', 'desc');
        }])->where('id', $request->id)
            ->where('room_id', $roomId)
            ->first();
        
        $payroll = $user->payroll;

        return response()->json([
            'user' => $user,
            'payroll' => $payroll,
        ]);
    }

    public function staffPaymentStatus($id) {
        $id = decrypt($id);
        $payroll = Payroll::find($id);

        if( $payroll ) {
            $payroll->update(['status' => 1]);
            return redirect()->back()->with('success', 'Payment paid successfully');
        }
        return redirect()->back()->with('error', 'Something went wrong !');
    }

    public function staffCheckin(Request $request) {
        $request->validate([
            'id' => 'required',
            'time' => 'required',
        ]);

        $staff = User::find($request->id);
        if( $staff ){
            $payroll = Payroll::create([
                'staff_id' => $staff->id,
                'checkin' => $request->time,
                'hourly' => $staff->hourly,
                'room_id' => $staff->room_id,
            ]);
            return response()->json([
                'status' => 'success',
            ]);
        }

        return response()->json([
            'status' => 'error',
        ]);
    }


    public function checkin(Request $request) {
        $request->validate([
            'datetime' => 'required'
        ]);

        $payroll = Payroll::create([
            'staff_id' => Auth::id(),
            'checkin' => $request->datetime,
            'hourly' => Auth::user()->hourly,
            'room_id' => Auth::user()->room_id,
        ]);

        if( ! empty($payroll) ) {
            return redirect()->back()->with('success', 'Checkin Successfully !');
        } else {
            return redirect()->back()->with('error', 'Something went wrong !');
        }
    }

    public function checkout(Request $request) {
        $request->validate([
            'datetime' => 'required'
        ]);

        $payroll = Payroll::where('staff_id', Auth::id())
            ->whereDate('checkin', Carbon::today())
            ->whereNull('checkout')
            ->first();

        $action = $payroll->update([
            'checkout' => $request->datetime,
        ]);

        if( ! empty($action) ) {
            return redirect()->back()->with('success', 'Checkout Successfully !');
        } else {
            return redirect()->back()->with('error', 'Something went wrong !');
        }
    }

    public function staffCheckout(Request $request) {
        $request->validate([
            'id' => 'required',
            'time' => 'required',
            'slug' => 'required',
        ]);

        $payroll = Payroll::where('id', $request->slug)
            ->whereNull('checkout')
            ->where('staff_id', $request->id)
            ->first();
        if( $payroll ){
            $payroll->update([
                'checkout' => $request->time,
            ]);
            return response()->json([
                'status' => 'success',
            ]);
        }
        return response()->json([
            'status' => 'error',
        ]);

    }

}
