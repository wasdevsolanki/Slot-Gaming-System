<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Carbon;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Machine;
use App\Models\Reading;
use App\Models\Ticket;
use App\Models\Point;
use App\Models\Room;
use App\Models\Bank;
use App\Models\User;
use Dompdf\Options;
use Dompdf\Dompdf;

class ReportController extends Controller
{
    public function index() {
        $data['staff'] = User::where('role', STAFF)->where('room_id', session('roomId'))->get();
        return view('admin.pages.report.index', $data);
    }

    public function staff(Request $request) {
        
        $request->validate([
            'staff_id' => 'required'
        ]);

        $id = decrypt($request->staff_id);
        $date = $request->date ? $request->date : Carbon::today();

        $data['staff'] = User::where('role', STAFF)->where('room_id', session('roomId'))->get();

        $data['s_staff'] = User::where('id', $id)->first();

        $data['transactions'] = Transaction::with('transfer_by')->where('staff_id', $id)
            ->whereDate('created_at', $date)    
            ->get();

        $data['tickets'] = Ticket::with('player')->where('employee_id', $id)
            ->whereDate('created_at', $date)
            ->get();

        $data['sum_transactions'] = Transaction::with('transfer_by')->where('staff_id', $id)
            ->whereDate('created_at', $date)    
            ->sum('amount');

        $data['sum_tickets'] = Ticket::with('player')->where('employee_id', $id)
            ->whereDate('created_at', $date)
            ->sum('amount');

        $data['sum_points'] = Point::whereDate('checkIn', $date)
            ->where('creator_id', $id)
            ->sum('amount');


        $data['bank'] = Bank::where('reciever_id', $id)
            ->whereDate('created_at', $date)
            ->first();

        $total = $data['bank'] ? $data['bank']['total'] + $data['sum_transactions'] : 0;
        $balance = $total - ($data['sum_tickets'] + $data['sum_transactions']);
        $data['remainBalance'] = $balance ? $balance : 0;
 
        $data['date'] = $date;
        return view('admin.pages.report.index', $data);
    }

    public function staffPointCompress(Request $request) {

        $request->validate([
            'id' => 'required',
            'time' => 'required',
        ]);

        $id = $request->id;
        $time = $request->time;

        $date = $time ? $time : Carbon::today();

        $staff = User::where('id', $id)->where('role', STAFF)->first();
        $room = Room::where('id', $staff->room_id)->first();

        $points = Point::with(['player', 'machine'])
            ->whereDate('checkIn', $date)
            ->where('creator_id', $id)
            ->get();
        
        $totalPoint = Point::whereDate('checkIn', $date)
            ->where('creator_id', $id)
            ->sum('amount');
        
        $totalPlayer = Point::whereDate('checkIn', $date)
            ->where('creator_id', $id)
            ->distinct('player_id')
            ->count('player_id');
            
        if( count($points) > 0 ) {
        
            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $dompdf = new Dompdf($options);

            $html = '<html>
                <head>
                    <style>
                        body {
                            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
                        }
                        .readingTable {
                            width: 100%;
                            border-collapse: collapse;
                        }
                        .totalDiv {
                            width: 50%;
                            margin-left: auto;
                        }
                        .totalDiv .totalTable {
                            width: 100%;
                            border-collapse: collapse;
                        }
                        .totalDiv span {
                            font-weight: bolder;
                            line-height: 50px;
                        }
                        th, td {
                            border: 1px solid black;
                            text-align: left;
                        }
                        th {
                            background-color: #e2e3e5;
                            font-size: 0.8em;
                            padding: 8px;
                            text-align:center;
                            overflow-wrap: anywhere;
                        }
                        td {
                            text-align:center;
                            font-size: 0.8em;
                            padding: 5px;
                            overflow-wrap: anywhere;
                        }
                        tfoot td{
                            background-color: #e2e3e5;
                            overflow-wrap: anywhere;
                            text-align:center;
                            font-weight: bolder;
                            font-size: 0.8em;
                            padding: 8px;
                        }
                        .top-section {
                            max-width: 100%;
                            margin-bottom: 15px;
                        }
                        .top-section span strong {
                            margin-right: 5px;
                        }
                    </style>
                </head>
                <body>
                    <div class="top-section">
                        <span><strong style="font-size: 1.3em;">'. $room->name .'</strong></span></br>
                        <span><strong>Out Reading Date: </strong>'. $time .'</span></br>
                        <span><strong>Employee: </strong>'. $staff->name .'</span></br>
                        <span><strong>Customer:</strong>('. $totalPlayer .')</span>
                        <span><strong>Point Total: </strong>'. $totalPoint .'</span></br>
                        <span><strong>Reading Session ID: </strong>1061</span>
                    </div>
                    <table class="readingTable" style="margin-bottom:25px;">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>ID</th>
                                <th>Customer Name</th>
                                <th>Machine</th>
                                <th>Point</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>';

                            $index = 1;
                            foreach( $points as $point ) {
                                $html .= '<tr>';
                                    $html .= '
                                        <td>'. $index++ .'</td>
                                        <td>'. $point->player->code .'</td>
                                        <td>'. $point->player->name .'</td>
                                        <td>'. $point->machine->first()->serial .'</td>
                                        <td>'. $point->amount .'</td>
                                        <td>'. $point->checkin .'</td>
                                    ';
                                $html .= '</tr>';
                            }

                        $html .= '</tbody>';
                    $html .= '</table>';
                $html .= '</body>';
            $html .= '</html>';

            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();


            return $dompdf->stream($time);
        }

        return redirect()->back()->with('info', 'Staff record is not found');
    }

    public function staffTransactionCompress(Request $request) {
        
        $id = $request->id;
        $time = $request->time;
    
        $date = $time ? $time : Carbon::today();

        $staff = User::where('id', $id)->where('role', STAFF)->first();
        $room = Room::where('id', $staff->room_id)->first();

        $bank = Bank::where('reciever_id', $id)->whereDate('created_at', $date)->first();

        $transactions = Transaction::with('transfer')
            ->where('staff_id', $id)
            ->where('room_id', $staff->room_id)
            ->whereDate('created_at', $date)
            ->get()->toArray();

        $recieve = Transaction::with(['transfer', 'transfer_by'])
            ->where('room_id', $staff->room_id)
            ->whereDate('created_at', $date)
            ->where('reciever_id', $id)
            ->get()->toArray();

        $transactions =  array_merge( $transactions, $recieve );

        $totalPoint = Point::whereDate('checkIn', $date)
            ->where('creator_id', $id)
            ->sum('amount');
        
        $totalPlayer = Point::whereDate('checkIn', $date)
            ->where('creator_id', $id)
            ->distinct('player_id')
            ->count('player_id');
            
        $totalTicket = Ticket::where('employee_id', $id)
            ->whereDate('created_at', $date)
            ->distinct('player_id')    
            ->count('player_id');
            
        $ticketSum = Ticket::where('employee_id', $id)
            ->whereDate('created_at', $date)
            ->sum('amount');


        if( count($transactions) > 0 ) {
           
            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $dompdf = new Dompdf($options);

            $html = '<html>
                <head>
                    <style>
                        body {
                            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
                        }
                        .readingTable {
                            width: 100%;
                            border-collapse: collapse;
                        }
                        .totalDiv {
                            width: 50%;
                            margin-left: auto;
                        }
                        .totalDiv .totalTable {
                            width: 100%;
                            border-collapse: collapse;
                        }
                        .totalDiv span {
                            font-weight: bolder;
                            line-height: 50px;
                        }
                        th, td {
                            border: 1px solid black;
                            text-align: left;
                        }
                        .bg-light {
                            background-color: #e2e3e5;
                        }
                        th {
                            background-color: #e2e3e5;
                            font-size: 0.7em;
                            padding: 8px;
                            text-align:center;
                            overflow-wrap: anywhere;
                        }
                        td {
                            text-align:center;
                            font-size: 0.7em;
                            padding: 5px;
                            overflow-wrap: anywhere;
                        }
                        tfoot td{
                            background-color: #e2e3e5;
                            overflow-wrap: anywhere;
                            text-align:center;
                            font-weight: bolder;
                            font-size: 0.7em;
                            padding: 8px;
                        }
                        .top-section {
                            max-width: 100%;
                            margin-bottom: 15px;
                        }
                        .top-section span strong {
                            margin-right: 5px;
                        }
                    </style>
                </head>
                <body>
                    <div class="top-section">
                        <span><strong style="font-size: 1em;">'. $room->name .'</strong></span></br>
                        <span><strong style="font-size: 0.9em;">Out Reading Date: </strong>'. $date .'</span></br>
                        <span><strong style="font-size: 0.9em;">Employee: </strong>'. $staff->name .'</span></br>
                        <span><strong style="font-size: 0.9em;">Report: </strong>Shift Report</span>
                    </div>
                    <table class="readingTable" style="margin-bottom:25px;">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Note</th>
                                <th>Transaction</th>
                                <th>Transfer</th>
                                <th>Bank</th>
                                <th>Expense</th>

                            </tr>
                        </thead>
                        <tbody>';

                            $index = 1;
                            $bankSum = 0;
                            $otherSum = 0;
                            $bankCount = 0;
                            foreach( $transactions as $t ) {
                                $html .= '<tr>';
                                
                                    $html .= '<td>'. $index++ .'</td>';

                                    $html .= '<td>'. $t['note'] .'</td>';

                                    $html .= '<td>';
                                        if( $t['type'] == 'bank' ) {
                                            $html .= '<span>BANK</span>';
                                        } else if ( $t['type'] == 'transfer' && ! isset($t['transfer_by']) ) {
                                            $html .= '<span>TRANSFER</span>';
                                        } else if ( $t['type'] == 'expense' && ! isset($t['transfer_by']) ) {
                                            $html .= '<span>EXPENSE</span>';
                                        } else {
                                            $html .= '<span>RECIEVE</span>';
                                        }
                                    $html .= '</td>';

                                    $html .= '<td>';
                                        if( $t['transfer'] && ! is_null($t['transfer']) ){
                                            if( ! isset($t['transfer_by']) ){
                                                $html .= '<span>'. $t['transfer']['name'] .'</span>';
                                            } else {
                                                $html .= '<span>'. $t['transfer_by']['name'] .'</span>';
                                            }
                                        }
                                    $html .= '</td>';
                                
                                    $html .= '<td>';
                                        if( $t['type'] == 'bank' ){
                                            $bankCount++;
                                            $bankSum += $t['amount'];
                                            $html .= '<span>'. $t['amount'] .'</span>';
                                        }          
                                    $html .= '</td>';
                                        
                                    $html .= '<td>';
                                        if( $t['type'] != 'bank' ) {
                                            $otherSum += $t['amount'];
                                            $html .= '<span>'. $t['amount'] .'</span>';
                                        }
                                    $html .= '</td>';

                                $html .= '</tr>';
                            }

                        $html .= '</tbody>';

                        // TFOOT OF TABLE
                        $html .= '<tfoot>
                                    <tr>
                                        <td colspan="4"><strong>Transaction Total</strong></td>
                                        <td>'. $bankSum .'</td>
                                        <td>'. $otherSum .'</td>
                                    
                                    </td>
                                 </tfoot>';

                    $html .= '</table>';
                    // ALL TOTAL TRANSACTIONS
                    
                    $outComing = $ticketSum + $totalPoint;
                    $diff = $bank['total'] - $outComing;
                    $html .=    '<div class="grandTotal">
                                    <h5>Total of All transactions</h5>
                                    <table class="readingTable">
                                        <tr>
                                            <td colspan="14"><strong>Total of Bank and Point</strong></td>
                                        </tr>
                                        <tr>
                                            <td colspan="5"><strong>Bank</strong> ('. $bankCount .') '. $bankSum  .'</td>
                                            <td colspan="5"><strong>Points</strong> ('. $totalPlayer .') '. $totalPoint  .'</td>
                                            <td colspan="4"><strong>Tickets</strong> ('. $totalTicket .') '. $ticketSum  .'</td>
                                        </tr>
                                        <tr>
                                            <td colspan="14"><strong>Amount by Stacks</strong></td>
                                        </tr>

                                        <tr>
                                            <td class="bg-light"><strong>100s</strong></td><td>';
                                                if( $bank['100'] ) {
                                                    $html .= '<span>'. $bank['100'] .'</span></td>';
                                                } else {
                                                    $html .= '<span>0</span></td>';
                                                }

                                            $html .= '<td class="bg-light"><strong>50s</strong></td><td>';
                                                if( $bank['50'] ) {
                                                    $html .= '<span>'. $bank['50'] .'</span></td>';
                                                } else {
                                                    $html .= '<span>0</span></td>';
                                                }

                                            $html .= '<td class="bg-light"><strong>20s</strong></td><td>';
                                                if( $bank['20'] ) {
                                                    $html .= '<span>'. $bank['20'] .'</span></td>';
                                                } else {
                                                    $html .= '<span>0</span></td>';
                                                }

                                            $html .= '<td class="bg-light"><strong>10s</strong></td><td>';
                                                if( $bank['10'] ) {
                                                    $html .= '<span>'. $bank['10'] .'</span></td>';
                                                } else {
                                                    $html .= '<span>0</span></td>';
                                                }

                                            $html .= '<td class="bg-light"><strong>5s</strong></td><td>';
                                                if( $bank['5'] ) {
                                                    $html .= '<span>'. $bank['5'] .'</span></td>';
                                                } else {
                                                    $html .= '<span>0</span></td>';
                                                }

                                            $html .= '<td class="bg-light"><strong>1s</strong></td><td>';
                                                if( $bank['1'] ) {
                                                    $html .= '<span>'. $bank['1'] .'</span></td>';
                                                } else {
                                                    $html .= '<span>0</span></td>';
                                                }

                                            $html .= '<td class="bg-light"><strong>Total</strong></td>
                                            <td>'. $bank['total'] .'</td>
                                        </tr>
                                        
                                    </table>
                                    <h5>Grand Total</h5>
                                    <table class="readingTable">
                                        <thead>
                                            <tr>
                                                <th>Total (Bank)</th>
                                                <th>Outcoming (Ticket + Points)</th>
                                                <th>Difference (Bank - Outcoming)</th>
                                            </tr>
                                        </thead>
                                        <tr>
                                            <td>'. $bank['total'] .'</td>
                                            <td>'. $outComing .'</td>
                                            <td><strong> '. $bank['total'] .' - '. $outComing .' = '.  $diff .'</strong></td>
                                        </tr>
                                    </table>';

                                    if( $diff < 0 ) {
                                        $html .= '<h5>Comments: SHORT('. $diff .')</h5>';
                                    } else {
                                        $html .= '<h5>Comments: OVER('. $diff .')</h5>';
                                    }

                                    $html .= '<table class="readingTable">
                                        <tr><td>&nbsp;</td></tr>
                                        <tr><td>&nbsp;</td></tr>
                                        <tr><td>&nbsp;</td></tr>
                                        <tr><td>&nbsp;</td></tr>
                                        <tr><td>&nbsp;</td></tr>
                                    </table>
                                </div>';  
                    
                $html .= '</body>';
            $html .= '</html>';

            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            
            return $dompdf->stream($time);
            
            // $pdf = $dompdf->output();
            // return new Response($pdf, 200, [
            //     'Content-Type' => 'application/pdf',
            //     'Content-Disposition' => 'inline; filename="document.pdf"'
            // ]);
        }
        return redirect()->back()->with('error', 'Record not found !');
    }
}