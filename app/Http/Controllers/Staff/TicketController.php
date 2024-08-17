<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Player;
use App\Models\Point;

class TicketController extends Controller
{
    public function ticketStore(Request $request) {

        $request->validate([
            'player_id' => 'required',
            'point_id' => 'required',
            'ticket_amount' => 'required',
            'checkout' => 'required',
        ]);


        if ($request->ticket_amount <= allsetting('max_ticket') ) {
            
            if( ( $request->ticket_amount == 0 ) ) {
                return redirect()->back()->with('error', 'Amount should greater than 0 !');
            }
    
            $remain = remainBalance();
            if( $request->ticket_amount > $remain ) {
                return redirect()->back()->with('error', 'Your balance is not enough !');
            }

            $point = Point::where('id', $request->point_id)->first();
            if(! is_null($point) && is_null($request->repoint) ) {

                if( allsetting('times_ticket') == 0 ) {
                    return redirect()->back()->with('error', 'Ticket times is 0 !');
                }

                $pointTime = $point->amount * allsetting('times_ticket');
                if( $request->ticket_amount < $pointTime ) {
                    return redirect()->back()->with('error', 'Ticket amount is not in range !');
                }

                $ticket = Ticket::create([
                    'player_id' => $request->player_id,
                    'machine_id' => $point->machine_id,
                    'employee_id' => Auth::id(),
                    'room_id' => Auth::user()->room_id,
                    'point_id' => $point->id,
                    'amount' => $request->ticket_amount,
                ]);
                
                if( $point ) {
                    $point->update([
                        'status' => 2,
                        'checkout' => $request->checkout,
                    ]);
                }
    
                return redirect()->back()->with('success', 'Ticket created successfully!');
            } else if (! is_null($point) ) {
    
                if( allsetting('times_ticket') == 0 ) {
                    return redirect()->back()->with('error', 'Ticket times is 0 !');
                }

                $pointTime = $point->amount * allsetting('times_ticket');
                if( $request->ticket_amount < $pointTime ) {
                    return redirect()->back()->with('error', 'Ticket amount is not in range !');
                }

                $ticket = Ticket::create([
                    'player_id' => $request->player_id,
                    'machine_id' => $point->machine_id,
                    'employee_id' => Auth::id(),
                    'room_id' => Auth::user()->room_id,
                    'point_id' => $point->id,
                    'amount' => $request->ticket_amount,
                ]);
                if( $point ) {
                    
                    $point->update([
                        'status' => 2,
                        'checkout' => $request->checkout,
                    ]);

                    Point::create([
                        'amount' => $request->repoint,
                        'machine_id' => $point->machine_id,
                        'player_id' => $point->player_id,
                        'room_id' => $point->room_id,
                        'creator_id' => Auth::id(),
                        'status' => 1,
                        'checkin' => $request->checkout,
                    ]);
                }
                return redirect()->back()->with('success', 'Ticket created successfully!');
            }
        }
        return redirect()->back()->with('error', 'Ticket Range is exceeded!');
    }
}
