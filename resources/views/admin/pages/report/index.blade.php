@extends('admin.layouts.master')
@section('title', isset($title) ? $title : 'Home')
@section('content')
<div class="row h-100">
    <div class="col-md-4" style="padding-right: 0px;">
        <div class="card w-100 h-100">
            <div class="card-header bg-primary">STAFF</div>
            <div class="card-body d-flex justify-content-center h-50">
                <form action="{{ route('admin.report.staff') }}" method="GET">
                    @csrf
                    <div class="row d-flex justify-content-center align-items-center">

                        <div class="col-md-12 mb-3">
                            <label for="inputStaff" class="form-label">Select Staff</label>
                            <select class="form-control Select2 w-100" name="staff_id" id="inputStaff" style="width: 100%" required>
                                @foreach( $staff as $item )
                                    @if(! empty($s_staff) )
                                        <option value="{{encrypt($item->id)}}" {{$item->name == $s_staff->name ? 'selected' : ''}}>{{ $item->name }}</option>
                                    @else
                                        <option value="{{encrypt($item->id)}}">{{ $item->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-8 col-sm-12 mb-3 text-end">
                            @if(! empty($date) )
                            <input type="date" class="form-control inputDate" name="date" value="{{ $date }}" id="inputDate">
                            @else
                            <input type="date" class="form-control" name="date" value="{{ date('Y-m-d') }}" id="inputDate">
                            @endif
                        </div>
                        <div class="col-lg-4 col-sm-12">
                            <button type="submit" class="btn btn-primary p-2 w-100"><i class="las la-search"></i> Search</button>
                        </div>

                    </div>
                </form>
            </div>
            <div class="card-body h-50">
                @if(! empty($s_staff) )
                    <ul class="list-group fw-semibold">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Starting Balance
                            <span class="badge text-bg-primary rounded-pill">{{ $bank ? $bank->total : 0 }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Remaining Balance
                            <span class="badge text-bg-primary rounded-pill">{{ $remainBalance }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Transactions
                            <span class="badge text-bg-primary rounded-pill">{{ $sum_transactions }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Tickets
                            <span class="badge text-bg-primary rounded-pill">{{ $sum_tickets }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Points
                            <span class="badge text-bg-primary rounded-pill">{{ $sum_points }}</span>
                        </li>
                    </ul>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-8 admin-right">
        <div class="card h-100  w-100">
            <div class="card-header bg-primary d-flex justify-content-between">
                Report
                @if(! empty($s_staff) )

                    <div class="d-flex flex-row">
                        <form action="{{ route('admin.report.staff.point') }}" method="POST" id="pdfForm">
                            @csrf
                            <input type="hidden" name="time" value="{{ $date }}" id="pdfInput">
                            <input type="hidden" name="id" value="{{ $s_staff->id }}">
    
                            <button type="submit" class="btn btn-sm btn-light pdfbtn">Point Report</button>
                        </form>

                        <form action="{{ route('admin.report.staff.transaction') }}" method="POST" id="pdfForm1">
                            @csrf

                            <input type="hidden" name="time" value="{{ $date }}" id="pdfInput1">
                            <input type="hidden" name="id" value="{{ $s_staff->id }}">
                            <button type="submit" class="btn btn-sm btn-light pdfbtn">Transaction Report</button>
                        </form>
                    </div>
                @endif
            </div>
            <div class="card-body">
                <div class="row">

                    <div class="col-md-12 mb-2">
                        <h5>Transactions</h5>
                        <table class="TableOne table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th class="text-center">Staff</th>
                                    <th class="text-center">Transaction</th>
                                    <th class="text-center">Amount</th>
                                    <th>Note</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $index = 1; @endphp
                                @if( ! empty($transactions) )
                                @foreach( $transactions as $t )
                                    <tr>
                                        <td>{{ $index++ }}</td>
                                        <td class="text-center">
                                            @if( $t['transfer'] && ! is_null($t['transfer']) )
                                                @if(! isset($t['transfer_by']) )
                                                <span class="badge text-bg-secondary p-2">{{ $t['transfer']['name'] }}</span>
                                                @else
                                                <span class="badge text-bg-secondary p-2">{{ $t['transfer_by']['name'] }}</span>
                                                @endif
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if( $t['type'] == 'bank' )
                                            <span class="badge text-bg-success rounded-pill p-2 w-50">BANK <i class="las la-arrow-down"></i></span>
                                            @elseif( $t['type'] == 'transfer')
                                            <span class="badge text-bg-warning rounded-pill p-2 w-50">TRANSFER <i class="las la-arrow-up"></i></span>
                                            @elseif( $t['type'] == 'expense')
                                            <span class="badge text-bg-danger rounded-pill p-2 w-50">EXPENSE <i class="las la-arrow-up"></i></span>
                                            @else
                                            <span class="badge text-bg-info rounded-pill p-2 w-50">Recieve <i class="las la-arrow-down"></i></span>
                                            @endif
                                        </td>
                                        <td class="text-center fw-semibold">{{ $t['amount'] }}</td>
                                        <td>{{ $t['note'] }}</td>
                                    </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <div class="col-md-12 mb-2">
                        <h5>Tickets</h5>
                        <table class="TableTwo table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Client</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $i = 0; @endphp
                                @if( ! empty($transactions) )
                                @foreach( $tickets as $ticket )
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td>{{ $ticket->player->name }}</td>
                                    <td>{{ $ticket->amount }}</td>
                                </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>

                </div>   
            </div>
        </div>
    </div>
</div>

@push('post_script')
<script>

    $('.Select2').select2({
        width: 'resolve',
        placeholder: 'Select an option',
    });

    $('.TableOne, .TableTwo').DataTable({
        responsive: true,
        lengthChange: false,
        pageLength: 5,
        processing: true,
    });

    // $('#pdfForm').submit(function(event) {
    //     event.preventDefault();

    //     // Get the current date and time
    //     let now = new Date();
    //     let year = now.getFullYear();
    //     let month = String(now.getMonth() + 1).padStart(2, '0');
    //     let date = String(now.getDate()).padStart(2, '0');
    //     let hours = String(now.getHours()).padStart(2, '0');
    //     let minutes = String(now.getMinutes()).padStart(2, '0');
    //     let seconds = String(now.getSeconds()).padStart(2, '0');

    //     // Create the formatted date and time string
    //     let formattedDateTime = `${year}-${month}-${date} ${hours}:${minutes}:${seconds}`;


    //     let InputDate = $('.inputDate').val();
    //     if( InputDate != null ) {
    //         $('#pdfInput').val(InputDate);
    //     } else {
    //         $('#pdfInput').val(formattedDateTime);
    //     }

    //     $(this).unbind('submit').submit();
    // });

    // $('#pdfForm1').submit(function(event) {
    //     event.preventDefault();

    //     // Get the current date and time
    //     let now = new Date();
    //     let year = now.getFullYear();
    //     let month = String(now.getMonth() + 1).padStart(2, '0');
    //     let date = String(now.getDate()).padStart(2, '0');
    //     let hours = String(now.getHours()).padStart(2, '0');
    //     let minutes = String(now.getMinutes()).padStart(2, '0');
    //     let seconds = String(now.getSeconds()).padStart(2, '0');

    //     // Create the formatted date and time string
    //     let formattedDateTime = `${year}-${month}-${date} ${hours}:${minutes}:${seconds}`;

    //     $('#pdfInput1').val(formattedDateTime);
    //     $(this).unbind('submit').submit();
    // });


</script>
@endpush
@endsection