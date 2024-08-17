@extends('admin.layouts.master')
@section('title', isset($title) ? $title : 'Home')
@section('content')

<!-- Start Dashboard List -->
<div class="row admin-section h-100">
    <div class="col-md-8">
        <div class="card w-100 h-100">
            <div class="card-header bg-primary">Client List</div>
            <div class="card-body">
                <table id="pendingTable" class="table table-sm table-hover pending-table">
                    <thead>
                        <tr>
                            <th>Profile</th>
                            <th>Name</th>
                            <th>Bonus</th>
                            <th>Phone</th>
                            <th>Gender</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>     
                        @foreach($players as $player)
                            <tr>
                                <td>
                                    <img src="/upload/{{session('roomId')}}/profile/{{$player->profile}}" alt="" class="img-fluid rounded" width="80px">
                                </td>
                                <td>{{ $player->name }}</td>
                                <td>{{ $player->bonus }}</td>
                                <td>{{ $player->phone }}</td>
                                <td>{{ $player->gender }}</td>
                                <td>
                                    <div class="btn-group btn-group rounded-pill w-100" role="group" aria-label="Basic example">
                                        <a href="#" class="viewPlayer btn btn-sm text-primary-emphasis bg-primary-subtle" data="{{ $player->id }}">VIEW</a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-4" style="padding-left: 0px;">
        <div class="card w-100 h-100">
            <div class="card-header bg-primary">Client Record</div>
            <div class="card-body isDisplay d-none">
                <div class="row userDetail">
                    <div class="col-md-12 mb-3 d-flex justify-content-between align-items-center">
                        <span class="fw-semibold userName"></span>

                        <form action="{{ route('admin.player.history') }}" method="POST">
                            @csrf
                            <input type="hidden" name="player_id" class="playerInput">
                            <button type="submit" class="btn btn-sm btn-primary">View more</button>
                        </form>
                        
                    </div>
                    <div class="col-md-12">
                        <table class="table table-sm table-bordered text-center">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Machine</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody class="table-group-divider pointTable">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="card-body noDisplay d-flex justify-content-center align-items-center text-secondary display-6">
                No Display
            </div>
        </div>

    </div>
</div>

@push('post_script')
<script>
$(document).ready(function () {

    $('#pendingTable').DataTable({
        responsive: true,
        lengthChange: false
    });

    $('.viewPlayer').on('click', function(event) {
        event.preventDefault();
        const userId = $(this).attr('data');
        fetchUserDetails(userId);
    });

    function fetchUserDetails(userId) {
        
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        $.ajax({
            url: '/admin/player/detail/',
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            data: { id : userId },
            success: function(response) {

                $('.isDisplay').removeClass('d-none');
                $('.noDisplay').addClass('d-none');
                $('.pointTable').empty();

                
                $('.userName').text(response.player.name);
                $('.playerInput').val(response.player.id);

                if(response.points) {

                    $.each(response.points, function(key, value) {
                        var checkin = new Date(value.checkin);
                        var isDate = `${checkin.toLocaleDateString('en-US', { day: '2-digit' })}`;
                        var isTime = `${checkin.toLocaleDateString('en-US', { month: 'short', hour: '2-digit', minute: '2-digit', hour12: true })}`;

                        var list = `
                            <tr>
                                <td>${isDate} ${isTime}</td>
                                <td>${value.get_machine.type} - ${value.get_machine.serial}</td>
                                <td>${value.amount}</td>
                            </tr>`;
                        $('.pointTable').append(list);
                    });
                } else {

                    var list = `
                        <tr>
                            <td colspan="4" class="p-5">No Record</td>
                        </tr>`;
                    $('.pointTable').append(list);
                }
                                

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