<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Machine;
use App\Models\Reading;
use App\Models\Room;
use App\Models\User;
use App\Models\Bank;

class BankController extends Controller
{
    public function index() {

        $data['staff'] = User::where('room_id', session('roomId'))->get();
        $data['banks'] = Bank::with('staff')->where('room_id', session('roomId'))->whereDate('created_at', Carbon::today())->get();
        return view('admin.pages.bank.index', $data);
    }

    public function store(Request $request) {
        
        $request->validate([
            'total' => 'required',
            'reciever_id' => 'required'
        ]);
        
        if( ($request->total == 0) ) {
            return redirect()->back()->with('error', 'Amount should greater than 0 !');
        }

        $bank = Bank::create([
            'admin_id' => session('key'),
            'room_id' => session('roomId'),
            'reciever_id' => $request->reciever_id,
            'total' => $request->total,
            '1' => $request->stack_1 ? $request->stack_1 : null,
            '5' => $request->stack_5 ? $request->stack_5 : null,
            '10' => $request->stack_10 ? $request->stack_10 : null,
            '20' => $request->stack_20 ? $request->stack_20 : null,
            '50' => $request->stack_50 ? $request->stack_50 : null,
            '100' => $request->stack_100 ? $request->stack_100 : null,
            'note' => $request->note ? $request->note : null,
        ]);

        if(! is_null($bank) ) {
            Transaction::create([
                'staff_id' => $request->reciever_id,
                'admin_id' => session('key'),
                'room_id' => session('roomId'),
                'type' => 'bank',
                'amount' => $request->total,
                'note' => $request->note ? $request->note : null,
            ]);
        }

        return redirect()->back()->with('success', 'Bank entry added successfully');
    }
}