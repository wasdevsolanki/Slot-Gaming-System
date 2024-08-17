@extends('admin.layouts.master')
@section('title', isset($title) ? $title : 'Home')
@section('content')
@push('post_css')
<style>
    .wrapper {
        /* height: 117vh; */
        height: auto !important;
    }
</style>
@endpush

<div class="row admin-section h-100">

    <div class="col-md-12 mb-3 h-100">
        <div class="card w-100 h-100">
            <div class="card-header bg-primary d-flex justify-content-between align-items-center">
                Reading
                <div class="d-flex">
                    <form action="{{ route('admin.pdf.reading') }}" method="POST" id="pdfForm">
                        @csrf

                        @if( $range )
                        <input type="hidden" name="time" value="{{$range}}" id="pdfInput">
                        @else
                        <input type="hidden" name="time" value="" id="pdfInput">
                        @endif

                        <button type="submit" class="btn btn-sm btn-light pdfbtn">Download PDF</button>
                    </form>
                </div>
            </div>

            <div class="card-body p-3">
                <table id="readingTable" class="table table-sm table-bordered table-hover w-100 mt-2 mb-0">
                    <thead class="table-secondary">
                        <tr>
                            <th>MID#</th>
                            <th>Type</th>
                            <th>Factor</th>
                            <th>Game</th>
                            <th>Employee</th>
                            <th>Prev.In</th> 
                            <th>Prev.Out</th>
                            <th>Curr.In</th>
                            <th>Curr.Out</th>
                            <th>In($)</th>
                            <th>Out($)</th>
                            <th>Diff($)</th>
                            <th>Out %</th>
                            <th>Life Out %</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                        @php
                            $prevInSum = 0;
                            $prevOutSum = 0;
                            $currInSum = 0;
                            $currOutSum = 0;
                            $inSum = 0;
                            $outSum = 0;
                            $diffSum = 0;
                        @endphp

                        @if(! $readings->isEmpty() )
                            @foreach( $readings as $item )
                                @php

                                    if( $isRange == true ) {
                                        $prevIn = Rprev( $item->machines->first()->id, $range ) ? Rprev( $item->machines->first()->id, $range )->in : 0;
                                        $prevOut = Rprev( $item->machines->first()->id, $range ) ? Rprev( $item->machines->first()->id, $range )->out : 0;
                                    } else {
                                        $prevIn = Rprev( $item->machines->first()->id, ) ? Rprev( $item->machines->first()->id )->in : 0;
                                        $prevOut = Rprev( $item->machines->first()->id ) ? Rprev( $item->machines->first()->id )->out : 0;
                                    }

                                    $prevInSum += $prevIn;
                                    $prevOutSum += $prevOut;
                                    $currInSum += $item->in;
                                    $currOutSum += $item->out;
                                @endphp

                                <tr>
                                    <td>{{ $item->machines->first()->serial }}</td>
                                    <td>{{ $item->machines->first()->type }}</td>
                                    <td>{{ allsetting('factor') }}</td>
                                    <td>{{ $item->machines->first()->game }}</td>
                                    <td>
                                        @if( $item->employee_id )
                                            {{ getStaff($item->employee_id)->name }}
                                        @elseif( $item->admin_id )
                                            {{ getAdmin($item->admin_id)->name }}
                                        @endif
                                    </td>
                                    <td>{{ $prevIn }}</td>
                                    <td>{{ $prevOut }}</td>
                                    <td>{{ $item->in }}</td>
                                    <td>{{ $item->out }}</td>
                                    <td>
                                        @php
                                        $In = $item->in - $prevIn;
                                        $inSum += $In;
                                        @endphp
                                        {{ $In }}
                                    </td>
                                    <td>
                                        @php
                                        $Out = $item->out - $prevOut;
                                        $outSum += $Out;
                                        @endphp
                                        {{ $Out }}
                                    </td>
                                    @php
                                    $diff = $In - $Out;
                                    $diffSum += $diff;
                                    @endphp
                                    <td class="@if($diff < 0) bg-secondary text-light @endif">
                                        {{ $diff }}
                                    </td>
                                    <td>
                                        @if( $In && $Out )
                                        {{ number_format(($Out / $In * 100), 2) }} %
                                        @else
                                        0 %
                                        @endif
                                    </td>
                                    <td>
                                        @if( $prevOut && $prevIn )
                                        {{ number_format( ( ($prevOut / $prevIn) * 100 ), 2) }} %
                                        @else
                                        0 %
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            @foreach( $all_machines as $m )
                            <tr>
                                <td>{{ $m->serial }}</td>
                                <td>{{ $m->type }}</td>
                                <td>{{ allsetting('factor') }}</td>
                                <td>{{ $m->game }}</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0%</td>
                                <td>0%</td>
                            </tr>
                            @endforeach
                        @endif
                    </tbody>

                    <tfoot class="table-secondary">
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>{{ $prevInSum }}</td>
                            <td>{{ $prevOutSum }}</td>
                            <td>{{ $currInSum }}</td>
                            <td>{{ $currOutSum }}</td>
                            <td>{{ $inSum }}</td>
                            <td>{{ $outSum }}</td>
                            <td>{{ $diffSum }}</td>
                            <td>
                                @if( $outSum && $inSum )
                                {{ number_format(($outSum / $inSum * 100), 2) }} %
                                @endif
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-5">
        <div class="card h-100">
            <div class="card-header bg-primary">Reading Session</div>
            <div class="card-body">

                <form action="{{ route('admin.reading.list')}}" method="GET">
                    @csrf
                    <div class="row p-3">
                        <div class="col-md-12 mb-3">
                            <label for="dateRange" class="form-label">Select date for reading</label>
                            <div class="input-group mb-2">
                                <span class="input-group-text" id="dateRange"><i class="las la-calendar"></i></span>
                                <input type="date" class="form-control" name="date_range" value="{{$range}}" aria-label="date range" max="<?php echo date('Y-m-d'); ?>" aria-describedby="dateRange" required>
                            </div>
                        </div>
                        <div class="col-md-12 d-flex justify-content-center">
                            <button type="submit" class="btn btn-sm btn-primary p-2 w-100">View Reading</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <div class="col-md-7 admin-right">
        <div class="card w-100">
            <div class="card-header bg-primary">Total</div>
            <div class="card-body p-0 ">
                <table class="table table-bordered mb-0 h-100">
                    <thead class="table-secondary">
                        <tr>
                            <th>Type</th>
                            <th>IN $</th>
                            <th>OUT $</th>
                            <th>Diff</th>
                            <th>% PROFIT</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalIn = 0;
                            $totalOut = 0;
                            $totalDiff = 0;
                            $totalProfit = 0;
                        @endphp
                        @if( count($record) > 0 )
                            @foreach ($record as $key => $value)
                                @php
                                    $profit = ($value['diff'] / $diffSum) * 100;
                                    $totalIn += $value['in'];
                                    $totalOut += $value['out'];
                                    $totalDiff += $value['diff'];
                                    $totalProfit += $profit;
                                @endphp
                                <tr>
                                    <td>{{ $key }}</td>
                                    <td>{{ $value['in'] }}</td>
                                    <td>{{ $value['out'] }}</td>
                                    <td>{{ $value['diff'] }}</td>
                                    <td>{{ number_format($profit, 2) }}%</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td class="text-center p-5 text-secondary" colspan="5">Reading Not Found</td>
                            </tr>
                        @endif
                    </tbody>
                    <tfoot class="table-secondary">
                        <tr>
                            <td></td>
                            <td>{{ $totalIn }}</td>
                            <td>{{ $totalOut }}</td>
                            <td>{{ $totalDiff }}</td>
                            <td>{{ number_format($totalProfit, 0) }}%</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- Model Reading In Start -->
<div class="modal fade" id="inModel" tabindex="-1" aria-labelledby="inModelLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title fs-5" id="inModelLabel">Reading (IN)</h2>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="{{ route('admin.reading.in') }}" method="POST" id="formIn">
            @csrf
            <input type="hidden" name="in_date" id="inDate">
            <div class="modal-body">

                <div class="input-group mb-3">
                    <label class="input-group-text" for="inputMachine"><i class="lab la-codiepie"></i></label>
                    <select class="form-select" id="inputMachine" name="machine">
                        @foreach ($all_machines as $machine)
                            <option value="{{ $machine->id }}">{{ $machine->serial }} -- {{ $machine->type }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="input-group flex-nowrap mb-3">
                    <span class="input-group-text" id="In"><i class="las la-arrow-down"></i></span>
                    <input type="number" class="form-control" placeholder="Enter Reading In" aria-label="In" aria-describedby="In" name="in">
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default border" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Add Reading IN</button>
            </div>
        </form>
    </div>
  </div>
</div>
<!-- Model End -->

<!-- Model Reading In Start -->
<div class="modal fade" id="outModel" tabindex="-1" aria-labelledby="outModelLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title fs-5" id="outModelLabel">Reading (OUT)</h2>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="{{ route('admin.reading.out') }}" method="POST" id="formOut">
            @csrf
            <input type="hidden" name="out_date" id="outDate">
            <div class="modal-body">

                <div class="input-group mb-3">
                    <label class="input-group-text" for="inputMachine"><i class="lab la-codiepie"></i></label>
                    <select class="form-select" id="inputMachine" name="machine">
                        @foreach ($all_machines as $machine)
                            <option value="{{ $machine->id }}">{{ $machine->serial }} -- {{ $machine->type }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="input-group flex-nowrap mb-3">
                    <span class="input-group-text" id="Out"><i class="las la-arrow-down"></i></span>
                    <input type="number" class="form-control" placeholder="Enter Reading Out" aria-label="Out" aria-describedby="Out" name="out">
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default border" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Add Reading OUT</button>
            </div>
        </form>
    </div>
  </div>
</div>
<!-- Model End -->

@push('post_script')
<script>
$(document).ready(function () {

    $('#readingTable').DataTable({
        responsive: true,
        pageLength: 10,
        lengthChange: false,
    });

    $('#formIn').submit(function(event) {
        // Prevent the default form submission
        event.preventDefault();

        // Get the current date and time
        let now = new Date();
        let year = now.getFullYear();
        let month = String(now.getMonth() + 1).padStart(2, '0');
        let date = String(now.getDate()).padStart(2, '0');
        let hours = String(now.getHours()).padStart(2, '0');
        let minutes = String(now.getMinutes()).padStart(2, '0');
        let seconds = String(now.getSeconds()).padStart(2, '0');

        // Create the formatted date and time string
        let formattedDateTime = `${year}-${month}-${date} ${hours}:${minutes}:${seconds}`;

        // Set the value of the hidden input field
        $('#inDate').val(formattedDateTime);

        // Submit the form
        $(this).unbind('submit').submit();
    });

    $('#pdfForm').submit(function(event) {
        event.preventDefault();

        // Get the current date and time
        let now = new Date();
        let year = now.getFullYear();
        let month = String(now.getMonth() + 1).padStart(2, '0');
        let date = String(now.getDate()).padStart(2, '0');
        let hours = String(now.getHours()).padStart(2, '0');
        let minutes = String(now.getMinutes()).padStart(2, '0');
        let seconds = String(now.getSeconds()).padStart(2, '0');

        // Create the formatted date and time string
        let formattedDateTime = `${year}-${month}-${date} ${hours}:${minutes}:${seconds}`;

        var inputDate = $('#pdfInput').val();
        if( inputDate == '') {
            // alert('inputDate');
            $('#pdfInput').val(formattedDateTime);
        }
        $(this).unbind('submit').submit();

    });

    $('#formOut').submit(function(event) {
        // Prevent the default form submission
        event.preventDefault();

        // Get the current date and time
        let now = new Date();
        let year = now.getFullYear();
        let month = String(now.getMonth() + 1).padStart(2, '0');
        let date = String(now.getDate()).padStart(2, '0');
        let hours = String(now.getHours()).padStart(2, '0');
        let minutes = String(now.getMinutes()).padStart(2, '0');
        let seconds = String(now.getSeconds()).padStart(2, '0');

        // Create the formatted date and time string
        let formattedDateTime = `${year}-${month}-${date} ${hours}:${minutes}:${seconds}`;

        // Set the value of the hidden input field
        $('#outDate').val(formattedDateTime);

        // Submit the form
        $(this).unbind('submit').submit();
    });
   
});
</script>
@endpush
@endsection