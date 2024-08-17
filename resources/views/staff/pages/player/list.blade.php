@extends('staff.layouts.master')
@section('title', isset($title) ? $title : 'Home')
@section('content')

<!-- Start Dashboard List -->
<div class="row admin-section h-100">
    <div class="col-md-8 h-100">
        <div class="card h-100">
            <div class="card-header bg-primary">Client List</div>
            <div class="card-body">
                <table id="playerTable" class="table table-sm table-hover pending-table">
                    <thead>
                        <tr>
                            <th>Profile</th>
                            <th>#PID</th>
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
                                    <img src="/upload/{{Auth::user()->room_id}}/profile/{{$player->profile}}" alt="" class="img-fluid rounded" width="100px">
                                </td>
                                <td>{{ $player->code }}</td>
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

    <div class="col-md-4 admin-right">
        <div class="card w-100">
            <div class="card-header bg-primary">Details</div>
            <div class="card-body isDisplay d-none">
                <div class="row userDetail">
                    <div class="col-md-12 d-flex justify-content-between align-items-center mb-4">
                        <strong>Player Information</strong>
                        <span class="badge p-2 bg-primary userRole">Player</span>
                    </div>
                    <div class="col-md-12 d-flex justify-content-center align-items-center p-2 mb-3">
                        <img class="rounded userProfile" src="https://rb.gy/hky7xt" alt="" width="150">
                    </div>
                    <div class="col-md-12 mb-3">
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Name
                                <span class="userName badge text-bg-secondary p-2">------</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Points Bonus
                                <span class="userBonus badge text-bg-secondary p-2">------</span>
                            </li>
                        </ul>
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

    $('#playerTable').DataTable({
        responsive: true,
        lengthChange: false,
        pageLength: 10,
        processing: true,
    });

    $('#playerTable tbody').on('click', '.viewPlayer', function(event) {
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