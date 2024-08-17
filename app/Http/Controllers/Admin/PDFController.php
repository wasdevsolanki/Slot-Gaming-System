<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Carbon;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Models\Machine;
use App\Models\Reading;
use App\Models\Room;
use Dompdf\Options;
use Dompdf\Dompdf;

class PDFController extends Controller
{
    public function reading(Request $request) {

        $request->validate([
            'time' => 'required'
        ]);

        $range = $request->time;
        $readings = Reading::with(['machines' => function($query) {
            $query->where('room_id', session('roomId'));
        }])
            ->where('room_id', session('roomId'))
            ->whereDate('in_date', $range)
            ->where('out_date', '!=', null)
            ->get();


        if( $readings->isEmpty() ) {
            return redirect()->back()->with('error', 'Reading not found !');
        }

        $record = [];
        foreach ($readings as $item) {

            $prevIn = Rprev( $item->machines->first()->id, $range) ? Rprev( $item->machines->first()->id, $range )->in : 0;
            $prevOut = Rprev( $item->machines->first()->id, $range ) ? Rprev( $item->machines->first()->id, $range )->out : 0;

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

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $dompdf = new Dompdf($options);
    

        // READING VARS
        $prevInSum = 0;
        $prevOutSum = 0;
        $currInSum = 0;
        $currOutSum = 0;
        $inSum = 0;
        $outSum = 0;
        $diffSum = 0;

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
                            <span><strong>In Reading Date: </strong>'. $range .'</span></br>
                            <span><strong>Out Reading Date: </strong>'. $range .'</span></br>
                            <span><strong>Gameroom: </strong>'. room()->name .'</span></br>
                            <span><strong>Reading Session ID: </strong>1061</span>
                        </div>
                        <table class="readingTable" style="margin-bottom:25px;">
                            <thead>
                                <tr>
                                    <th>MID#</th>
                                    <th>Type</th>
                                    <th>Factor</th>
                                    <th>Game</th>
                                    <th>Employee</th>
                                    <th>Prev IN</th>
                                    <th>Prev OUT</th>
                                    <th>Curr IN</th>
                                    <th>Curr OUT</th>
                                    <th>In ($)</th>
                                    <th>Out ($)</th>
                                    <th>Diff ($)</th>
                                    <th>% Out</th>
                                    <th>% Lifeout</th>
                                </tr>
                            </thead>
                            <tbody>';

                            foreach ($readings as $item) {

                                $prevIn = Rprev( $item->machines->first()->id, $range) ? Rprev( $item->machines->first()->id, $range )->in : 0;
                                $prevOut = Rprev( $item->machines->first()->id, $range ) ? Rprev( $item->machines->first()->id, $range )->out : 0;

                                $prevInSum += $prevIn;
                                $prevOutSum += $prevOut;
                                $currInSum += $item->in;
                                $currOutSum += $item->out;

                                $html .= '<tr>';

                                    $html .= '<td>'. $item->machines->first()->serial .'</td>';
                                    $html .= '<td>'. $item->machines->first()->type .'</td>';
                                    $html .= '<td>'. allsetting('factor') .'</td>';
                                    $html .= '<td>'. $item->machines->first()->game .'</td>';
                                    
                                    $person = '';
                                    if( $item->employee_id ) {
                                        $person = getStaff($item->employee_id)->name;
                                    } else {
                                        $person = getAdmin($item->admin_id)->name;
                                    }

                                    $html .= '<td>'. $person .'</td>';
                                    $html .= '<td>'. $prevIn .'</td>';
                                    $html .= '<td>'. $prevOut .'</td>';
                                    $html .= '<td>'. $item->in .'</td>';
                                    $html .= '<td>'. $item->out .'</td>';

                                    $In = $item->in - $prevIn;
                                    $inSum += $In;
                                    $html .= '<td>'. $In .'</td>';

                                    $Out = $item->out - $prevOut;
                                    $outSum += $Out;
                                    $html .= '<td>'. $Out .'</td>';

                                    $diff = $In - $Out;
                                    $diffSum += $diff;
                                    $html .= '<td>'. $diff .'</td>';

                                    if( $In && $Out ) {
                                      $InOut = number_format(($Out / $In * 100), 2) .' %';
                                      $html .= '<td>'. $InOut .'</td>';
                                    } else {
                                        $InOut = 0;
                                        $html .= '<td>'. $InOut .'%</td>';
                                    }

                                    if( $prevOut && $prevIn ){
                                        $html .= '<td>'. number_format( ( ($prevOut / $prevIn) * 100 ), 2) .'%</td>';
                                    }else {
                                        $html .= '<td>0%</td>';
                                    }

                                $html .= '</tr>';
                            }

                            $html .= '</tbody>';

                            $html .= '<tfoot>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td>'. $prevInSum .'</td>
                                                <td>'. $prevOutSum .'</td>
                                                <td>'. $currInSum .'</td>
                                                <td>'. $currOutSum .'</td>
                                                <td>'. $inSum .'</td>
                                                <td>'. $outSum .'</td>
                                                <td>'. $diffSum .'</td>';

                                                if( $outSum && $inSum ) { 
                                                   $html .= '<td>'. number_format(($outSum / $inSum * 100), 2) .' %' .'</td>';
                                                } else {
                                                    $html .= '<td></td>';
                                                }

                                            $html .= '<td></td></tr>
                                        </tfoot>';
                            

                        $html .= '</table>';

                        // TOTAL TABLE ---------------------------------
                        $html .= '<div class="totalDiv">
                        <span>Total by Machine Type</span>
                        <table class="totalTable">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>In ($)</th>
                                    <th>Out ($)</th>
                                    <th>Diff ($)</th>
                                    <th>% Lifeout</th>
                                </tr>
                            </thead>
                            <tbody>';
                            
                                $totalIn = 0;
                                $totalOut = 0;
                                $totalDiff = 0;
                                $totalProfit = 0;

                                if( count($record) > 0 ){
                                    foreach ($record as $key => $value){
                                        
                                        if($diffSum) {
                                            $profit = ($value['diff'] / $diffSum) * 100;
                                        } else {
                                            $profit = 0;
                                        }

                                        $totalIn += $value['in'];
                                        $totalOut += $value['out'];
                                        $totalDiff += $value['diff'];
                                        $totalProfit += $profit;
                                        
                                        $html .= '<tr>
                                            <td>'. $key .'</td>
                                            <td>'. $value['in'] .'</td>
                                            <td>'. $value['out'] .'</td>
                                            <td>'. $value['diff'] .'</td>
                                            <td>'. number_format($profit, 2) .'%</td>
                                        </tr>';
                                    }
                                } else {
                                    $html .= '<tr><td class="text-center p-5 text-secondary" colspan="5">Reading Not Found</td></tr>';
                                }
                            $html .= '</tbody>
                            <tfoot class="table-secondary">
                                <tr>
                                    <td></td>
                                    <td>'. $totalIn .'</td>
                                    <td>'. $totalOut .'</td>
                                    <td>'. $totalDiff .'</td>
                                    <td>'. number_format($totalProfit, 0) .'%</td>
                                </tr>
                            </tfoot>';


                        $html .= '</table></div>';

                    $html .= '</body>
                </html>';

    
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
    
        return $dompdf->stream($range);

        // $pdf = $dompdf->output();
        // return new Response($pdf, 200, [
        //     'Content-Type' => 'application/pdf',
        //     'Content-Disposition' => 'inline; filename="document.pdf"'
        // ]);
    }

}