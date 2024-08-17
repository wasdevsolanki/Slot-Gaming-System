<?php

namespace App\Http\Controllers\Admin;

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
        }])->where('room_id', session('roomId'))->where('role', STAFF)->get();

        return view('admin.pages.payroll.index', $data);
    }

    public function staffDetail(Request $request)
    {
        $roomId = session('roomId');

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