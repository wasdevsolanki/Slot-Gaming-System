@extends('admin.layouts.master')
@section('title', isset($title) ? $title : 'Home')
@section('content')
@push('post_css')
<style>

</style>
@endpush

<!-- Start Dashboard List -->
<div class="row admin-section h-100">
    
    <div class="col-md-5 mb-1">
        <div class="card w-100 h-100">
            <div class="card-header bg-primary">Point history</div>
            <div class="card-body">
                <table id="pointTable" class="table table-sm table-hover pending-table">
                    <thead>
                        <tr>
                            <th>Checkin</th>
                            <th>Checkout</th>
                            <th>Amount</th>
                            <th>Machine</th>
                            <th>Point By</th>
                        </tr>
                    </thead>
                    <tbody class="table-group-divider"> 
                        @php $pointCount = 0; $pointSum = 0 @endphp
                        @foreach( $points as $item )

                            @php 
                                $checkin = \Carbon\Carbon::parse($item->checkin);
                                $checkout = \Carbon\Carbon::parse($item->checkout);
                                $pointCount++;
                                $pointSum += $item->amount;
                            @endphp
                        <tr>
                            <td>{{ $checkin->format('j F g:i A') }}</td>
                            <td>{{ $checkout->format('j F g:i A') }}</td>
                            <td><span class="fw-semibold">{{ $item->amount }}</span></td>
                            <td>{{ $item->get_machine->type }}</td>
                            <td>
                                @if(! is_null($item->employee) )
                                    {{ $item->employee->name }}
                                @else
                                    {{ $admin }}
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-5 mb-1 admin-right">
        <div class="card w-100 h-100">
            <div class="card-header bg-primary">Ticket history</div>
            <div class="card-body">
                <table id="ticketTable" class="table table-sm table-hover pending-table">
                    <thead>
                        <tr>
                            <th>Checkin</th>
                            <th>Checkout</th>
                            <th>Amount</th></th>
                            <th>Machine</th>
                            <th>Ticket By</th>
                        </tr>
                    </thead>
                    <tbody class="table-group-divider">
                        @php $ticketCount = 0; $ticketSum = 0 @endphp
                        @foreach( $tickets as $item )
                            @php 
                                $checkin = \Carbon\Carbon::parse($item->point->checkin);
                                $checkout = \Carbon\Carbon::parse($item->point->checkout);
                                $ticketCount++;
                                $ticketSum += $item->amount;
                            @endphp
                        <tr>
                            <td>{{ $checkin->format('j F g:i A') }}</td>
                            <td>{{ $checkout->format('j F g:i A') }}</td>
                            <td><span class="fw-semibold">{{ $item->amount }}</span></td>
                            <td>{{ $item->machine->type }}</td>
                            <td>
                                @if(! is_null($item->employee) )
                                    {{ $item->employee->name }}
                                @else
                                    {{ $item->admin->name }}
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-2 p-1">          

        <ul class="list-group rounded mb-3">
            <li class="list-group-item d-flex justify-content-between">Name <span class="fw-semibold">{{ $player->name }}</span></li>
            <li class="list-group-item d-flex justify-content-between">Bonus <span class="fw-semibold">{{ $player->bonus }}</span></li>
            <li class="list-group-item d-flex justify-content-between">Start From <span class="fw-semibold">{{ $player->created_at->format('j F Y') }}</span></li>
        </ul>


        <div class="card mb-2">
            <div class="card-body text-center">
                Amount <h1 class="fw-bold mb-0">{{ $pointSum }}</h1>
            </div>
            <div class="card-footer fw-semibold d-flex justify-content-between">
                Points
                <span class="fs-6">Count: {{ $pointCount }}</span>
            </div>
        </div>

        <div class="card mb-2">
            <div class="card-body text-center">
                Amount <h1 class="fw-bold mb-0">{{ $ticketSum }}</h1>
            </div>
            <div class="card-footer fw-semibold d-flex justify-content-between">
                Tickets
                <span class="fs-6">Count: {{ $ticketCount }}</span>
            </div>
        </div>

        <div class="card mb-2">
            <div class="card-body text-center">
                @php $percentage = $ticketSum / $pointSum * 100; @endphp
                Winning 
                <h2 class="fw-bold mb-0 {{ $percentage > 0 ? 'text-success' : 'text-danger' }}">
                    {{ number_format($percentage, 2) }}%
                </h2>

            </div>
            <div class="card-footer fw-semibold text-center">
                Grand Total
            </div>
        </div>
                    
    </div>

</div>

@push('post_script')
<script>
$(document).ready(function () {
    $('#pointTable, #ticketTable').DataTable({
        responsive: true,
        lengthChange: false
    });
});
</script>
@endpush
@endsection