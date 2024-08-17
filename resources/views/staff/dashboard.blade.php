@extends('staff.layouts.master')
@section('title', isset($title) ? $title : 'Home')
@section('content')
@push('post_css')
<style>
    .wrapper {
        height: 100vh !important;
    }
</style>
@endpush

<!-- Start Dashboard List -->
<div class="row admin-section h-100">
    <div class="col-md-8 h-100">
        <div class="card h-50">
            <div class="card-header bg-primary">Pending Players</div>
            <div class="card-body">
                <table id="pendingTable" class="table table-sm table-hover pending-table">
                    <thead>
                        <tr>
                            <th>Id#</th>
                            <th>Name</th>
                            <th>Check IN</th>
                            <th>Points</th>
                            <th>Bonus</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($players as $player)
                            @foreach ( $player->points as $point )
                                @if( $point->status == 0 ) 
                                <tr>
                                    <td>{{ $player->code }}</td>
                                    <td>{{ $player->name }}</td>
                                    <td>
                                        @php
                                            $checkIn = $point->created_at;
                                        @endphp
                                        {{ $checkIn->format('j F Y g:i A') }}
                                    </td>
                                    <td>{{ $point->amount }}</td>
                                    <td>{{ $player->bonus }}</td>
                                    <td>


                                        <div class="btn-group btn-group rounded-pill w-100" role="group" aria-label="Basic example">
                                            <a href="#" class="viewPlayer btn btn-sm text-primary-emphasis bg-primary-subtle" data="{{ $player->id }}">View</a>
                                        </div>

                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card h-50">
            <div class="card-header bg-primary">Active Players</div>
            <div class="card-body">
                <table id="acceptTable" class="table table-sm table-hover success-table">
                    <thead>
                        <tr>
                            <th>Id#</th>
                            <th>Name</th>
                            <th>Check In</th>
                            <th>Points</th>
                            <th>Machine</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($players as $player)
                            @foreach ( $player->points as $point )
                                @if( $point->status == 1 )
                                <tr>
                                    <td>{{ $player->code }}</td>
                                    <td>{{ $player->name }}</td>
                                    <td>
                                        @php
                                            $checkIn = $point->created_at;
                                        @endphp
                                        {{ $checkIn->format('j F Y g:i A') }}
                                    </td>
                                    <td>{{ $point->amount }}</td>
                                    <td>
                                        {{$point->machine->first()->serial}} -
                                        {{$point->machine->first()->type}}
                                    </td>
                                    <td>
                                        <a href="#"  class="btn btn-sm btn-primary w-100 mb-2"  data-bs-toggle="modal" data-bs-target="#checkoutModal{{$player->id}}">Ticket</a>
                                        <form action="{{ route('staff.point.checkout') }}" method="post" id="checkoutFormDirect">
                                            @csrf
                                            <input type="hidden" name="point_id" value="{{$point->id}}">
                                            <input type="hidden" name="checkout_input" value="" class="checkoutInput">
                                            <button type="submit" class="btn btn-sm w-100 m btn-primary checkoutSubmitbtn">checkout</button>
                                        </form>
                                    </td>
                                </tr>

                                <!-- Modal Point -->
                                <div class="modal fade" id="checkoutModal{{$player->id}}" tabindex="-1" aria-labelledby="checkoutModal{{$player->id}}" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">

                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="checkoutModalLabel{{$player->id}}">Ticket</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>

                                            <div class="modal-body">
                                                <form action="{{route('staff.ticket.store')}}" method="post" id="ticketForm">
                                                    @csrf
                                                    <input type="hidden" name="player_id" value="{{ $player->id }}">
                                                    <input type="hidden" name="point_id" value="{{ $point->id }}">
                                                    <input type="hidden" name="checkout" class="checkoutDate" value="">

                                                    <div class="row">
                                                        
                                                        <div class="col-md-6 mb-4">
                                                            <span class="fs-5 fw-semibold mb-0">Player Ticket</span><br>
                                                            <span class="badge text-bg-secondary"> MIN : {{ $point->amount * allsetting('times_ticket') }}</span>  
                                                            <span class="badge text-bg-secondary"> MAX : {{ allsetting('max_ticket') }}</span>  
                                                        </div> 

                                                        @if( allsetting('repoint') )
                                                        <div class="col-md-6 mb-4 d-flex justify-content-end align-items-center">
                                                            <div class="form-check form-check-reverse">
                                                                <input class="form-check-input shadow-none" type="checkbox" value="" id="playerContinue">
                                                                <label class="form-check-label" for="playerContinue">
                                                                    Player Continue
                                                                </label>
                                                            </div>
                                                        </div>
                                                        @endif
                                                        
                                                        <div class="col-md-6">
                                                            <div class="form-floating mb-3">
                                                                <input type="number" class="form-control shadow-none" name="ticket_amount" id="ticketAmountInput" value="{{ $point->amount * allsetting('times_ticket') }}">
                                                                <label for="ticketAmountInput">Ticket Amount = {{ $point->amount * allsetting('times_ticket') }}</label>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6 rePointInput d-none">
                                                            <div class="form-floating mb-3">
                                                                <input type="number" class="form-control shadow-none" name="repoint" id="AddPointReInput">
                                                                <label for="AddPointReInput">Add up points</label>
                                                            </div>
                                                        </div>

                                                    </div>

                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                                                        <button type="submit" class="btn btn-primary ticketOut">Ticket out</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @endif
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-4 h-100 admin-right flex-column justify-content-start">

        @if( checkPermit( Auth::id() )->set_player_point == 1 )
        <div class="card mb-2">
            <div class="card-header bg-primary">Add up point</div>
            <div class="card-body">

                <form action="{{ route('staff.point.store.form') }}" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <select class="form-control playerSelect2" name="player_id" required>
                                <option value="">Select player</option>
                                @foreach( $all_player as $item )
                                    <option value="{{ $item->id }}">{{ $item->name }} -- Bonus: {{ $item->bonus }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <input type="number" class="form-control" name="point" id="inputPoint" placeholder="Add up point">
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-primary w-100">Add Point</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
        @endif

        <div class="card w-100 h-100">
            <div class="card-header bg-primary">Player Info</div>
            <div class="card-body isDisplay d-none">
                <div class="row userDetail">

                    <div class="col-md-4 d-flex align-items-center mb-3">
                        <img class="rounded userProfile" src="https://rb.gy/hky7xt" alt="" width="150">
                    </div>

                    <div class="col-md-8 d-flex align-items-center mb-3">
                        <ul class="list-group w-100">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Name
                                <span class="userName">------</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Points Bonus
                                <span class="userBonus">------</span>
                            </li>
                        </ul>
                    </div>

                    <div class="col-md-12 p-5">
                        <form action="{{ route('staff.point.checkin') }}" method="post" id="checkinForm">
                            @csrf
                            <input type="hidden" name="player_id" class="playerId" value="">
                            <input type="hidden" name="checkin" class="checkinDate" value="">

                            <div class="col-md-12 mb-3">
                                <select class="playerSelect2" name="machine" style="width: 100%" required>
                                    @foreach ($machines as $machine)
                                    <option value="{{ $machine->id }}">{{ $machine->type }} -- {{  $machine->serial }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12 d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary mt-3 w-50 checkInPlayer">Check In</button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
            <div class="card-body noDisplay d-flex p-5 justify-content-center align-items-center text-secondary display-6">
                No Display
            </div>
        </div>
    </div>
</div>

@push('post_script')
<script>
$(document).ready(function () {

    $('#playerContinue').change(function(){
        if($(this).is(':checked')) {
            $('.rePointInput').removeClass('d-none');
            $('.rePointInput').addClass('d-block');
        } else {
            $('.rePointInput').removeClass('d-block');
            $('.rePointInput').addClass('d-none');
        }
    });

    $('.checkoutSubmitbtn').on('click', function(event) {
        event.preventDefault();

        let now = new Date();
        let year = now.getFullYear();
        let month = String(now.getMonth() + 1).padStart(2, '0');
        let day = String(now.getDate()).padStart(2, '0');
        let hours = String(now.getHours()).padStart(2, '0');
        let minutes = String(now.getMinutes()).padStart(2, '0');
        let seconds = String(now.getSeconds()).padStart(2, '0');
        
        let DateTime = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
        $('.checkoutInput').val(DateTime);
        $('#checkoutFormDirect').submit();
    });

    $('.playerSelect2').select2({
        width: 'resolve',
        placeholder: 'Select an option',
    });

    $('.checkInPlayer').on('click', function(event) {
        event.preventDefault();

        let now = new Date();
        let year = now.getFullYear();
        let month = String(now.getMonth() + 1).padStart(2, '0');
        let day = String(now.getDate()).padStart(2, '0');
        let hours = String(now.getHours()).padStart(2, '0');
        let minutes = String(now.getMinutes()).padStart(2, '0');
        let seconds = String(now.getSeconds()).padStart(2, '0');
        
        let DateTime = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
        $('.checkinDate').val(DateTime);
        $('#checkinForm').submit();
    });

    $('.ticketOut').on('click', function(event) {
        event.preventDefault();

        let now = new Date();
        let year = now.getFullYear();
        let month = String(now.getMonth() + 1).padStart(2, '0');
        let day = String(now.getDate()).padStart(2, '0');
        let hours = String(now.getHours()).padStart(2, '0');
        let minutes = String(now.getMinutes()).padStart(2, '0');
        let seconds = String(now.getSeconds()).padStart(2, '0');
        
        let DateTime = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
        $('.checkoutDate').val(DateTime);
        $('#ticketForm').submit();
    });

    $('#pendingTable, #acceptTable').DataTable({
        responsive: true,
        pageLength: 5,
        lengthChange: false
    });

    $('#pendingTable tbody').on('click', '.viewPlayer', function(event) {
        event.preventDefault();
        const userId = $(this).attr('data');
        fetchUserDetails(userId);
    });

    function fetchUserDetails(userId) {
        
        const csrfToken = $('meta[name="csrf-token"]').attr('content');

        $.ajax({
            url: '/staff/player/detail/',
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            data: { id : userId },
            success: function(response) {
                
                $('.isDisplay').removeClass('d-none');
                $('.noDisplay').addClass('d-none');
                $('.playerId').val(response.id);
                
                $('.userName').text(response.name);
                $('.userBonus').text(response.bonus);
                $('.userProfile').attr('src', '/upload/' + response.room_id + '/profile/' + response.profile);

            },
            error: function(xhr, status, error) {
                console.error(error);
            }

        });
    }

});
</script>
@endpush
@endsection