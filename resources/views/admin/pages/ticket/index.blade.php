@extends('admin.layouts.master')
@section('title', isset($title) ? $title : 'Home')
@section('content')

<div class="row admin-section h-100">

    <div class="col-md-12 h-100">
        <div class="card w-100 h-100">

            <div class="card-header bg-primary">
                Ticket list
            </div>

            <div class="card-body">
                <table id="ticketTable" class="table table-sm table-hover">
                    <thead>
                        <tr>
                            <th>Profile</th>
                            <th>Player</th>
                            <th>Gender</th>
                            <th>Amount</th></th>
                            <th>Machine</th>
                            <th>Ticket By</th>
                            <th>Check IN</th>
                            <th>Check OUT</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach( $tickets as $item )
                            @php 
                                $checkin = \Carbon\Carbon::parse($item->point->checkin);
                                $checkout = \Carbon\Carbon::parse($item->point->checkout);
                            @endphp
                        <tr>
                            <td><img src="/upload/{{session('roomId')}}/profile/{{$item->player->profile}}" alt="" class="img-fluid rounded" width="100px"></td>
                            <td>{{ $item->player->name }}</td>
                            <td>{{ $item->player->gender }}</td>
                            <td><span class="badge text-bg-success w-75 p-2">{{ $item->amount }}</span></td>
                            <td>{{ $item->machine->type }}</td>
                            <td>
                                @if(! is_null($item->employee) )
                                    {{ $item->employee->name }}
                                @else
                                    {{ $item->admin->name }}
                                @endif
                            </td>
                            <td>{{ $checkin->format('j F Y g:i A') }}</td>
                            <td>{{ $checkout->format('j F Y g:i A') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

@push('post_script')
<script>
$(document).ready(function () {
    $('#ticketTable').DataTable({
        responsive: true,
        lengthChange: false
    });
});
</script>
@endpush
@endsection