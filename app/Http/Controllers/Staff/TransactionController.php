<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Machine;
use App\Models\Ticket;
use App\Models\Reading;
use App\Models\Room;
use App\Models\User;
use App\Models\Bank;

class TransactionController extends Controller
{
    public function index() {

        $data['staff'] = User::where('room_id', Auth::user()->room_id)
            ->where('id', '!=', Auth::id())
            ->where('role', STAFF)
            ->get();

        $transactions = Transaction::with('transfer')
            ->where('staff_id', Auth::id())
            ->where('room_id', Auth::user()->room_id)
            ->whereDate('created_at', Carbon::today())
            ->get()->toArray();

        $recieve = Transaction::with(['transfer', 'transfer_by'])
            ->where('room_id', Auth::user()->room_id)
            ->whereDate('created_at', Carbon::today())
            ->where('reciever_id', Auth::id())
            ->get()->toArray();

        $data['transactions'] = array_merge( $transactions, $recieve );

        // dd($data['transactions']);
        return view('staff.pages.transaction.index', $data);
    }

    public function store(Request $request) {
        $request->validate([
            'amount' => 'required',
        ]);

        if( ($request->amount == 0) ) {
            return redirect()->back()->with('error', 'Amount should greater than 0 !');
        }

        $remain = remainBalance();
        if( $request->amount > $remain ) {
            return redirect()->back()->with('error', 'Your balance is not enough !');
        }
        

        if( is_null($request->reciever_id) ) {
            Transaction::create([
                'staff_id' => Auth::id(),
                'employee_id' => Auth::id(),
                'room_id' => Auth::user()->room_id,
                'type' => 'expense',
                'amount' => $request->amount,
                'note' => $request->note,
            ]);

        } else {

            $staff = Bank::where('reciever_id', $request->reciever_id)
                ->whereDate('created_at', Carbon::today())
                ->where('room_id', Auth::user()->room_id)
                ->first();

            if( $staff ) {
                Transaction::create([
                    'staff_id' => Auth::id(),
                    'employee_id' => Auth::id(),
                    'reciever_id' => $request->reciever_id,
                    'room_id' => Auth::user()->room_id,
                    'type' => 'transfer',
                    'amount' => $request->amount,
                    'note' => $request->note,
                ]);
            }
            return redirect()->back()->with('error', 'Bank is not added!') ;
        }
        return redirect()->back()->with('success', 'Transaction added successfully');
    }
}