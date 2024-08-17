@extends('staff.layouts.master')
@section('title', isset($title) ? $title : 'Home')
@section('content')

<div class="row admin-section h-100">
    <div class="col-md-8 d-flex h-100">
        <div class="card w-100 h-100">

            <div class="card-header bg-primary d-flex justify-content-between align-items-center">
                Staff List
                <button type="button" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#staffModel" data-bs-whatever="@mdo"><i class="las la-plus"></i> Add Staff</button>
            </div>

            <div class="card-body">

                <table id="staffTable" class="table table-sm table-hover pending-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th class="text-center">Position</th>
                            <th class="text-center">Reg. Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach( $staff as $item )
                            <tr>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->email }}</td>
                                <td class="text-center">
                                    @if( $item->position && $item->position == 'manager' )
                                    <span class="badge text-bg-success w-75 p-1">MANAGER</span>
                                    @elseif( $item->position && $item->position == 'employee'  )
                                    <span class="badge text-bg-warning w-75 p-1">EMPLOYEE</span>
                                    @else
                                    <span class="badge text-bg-secondary w-75 p-1">COMMON</span>
                                    @endif
                                </td>
                                <td class="text-center">{{ $item->created_at->format('d M Y') }}</td>
                                <td>
                                    <div class="btn-group btn-group rounded-pill w-100" role="group" aria-label="Basic example">
                                        <a href="#" class="viewStaffDetail btn btn-sm text-primary-emphasis bg-primary-subtle" data="{{ $item->id }}">VIEW</a>
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
            <div class="card-header bg-primary">Staff Details</div>
            <div class="card-body isDisplay d-none">
                <div class="row userDetail">

                    <div class="col-md-3 mb-3">
                        <img class="rounded userProfile" src="{{ asset('assets/general/profile.png') }}" alt="" width="100">
                    </div>
                    <div class="col-md-9 mb-3 d-flex align-items-center">
                        <ul class="list-group w-100">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="badge text-bg-secondary">Name</span>
                                <span class="userName">-----</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="badge text-bg-secondary">Email</span>
                                <span class="userEmail">------</span>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-12">
                        <span class="fw-semibold">List of Permissions</span>
                    </div>
                    <form action="{{ route('staff.staff.permission_edit') }}" method="POST">
                        @csrf
                        <input type="hidden" name="user_id" id="userID">
                        <div class="col-md-12 mt-2 mb-2">
                            <div class="row perContainer">
                            </div>
                        </div>
                        <div class="col-md-12 d-flex justify-content-end align-items-end">
                            <button type="submit" class="btn btn-sm btn-primary">Edit Profile</button>
                        </div>
                    </form>

                </div>
            </div>
            <div class="card-body noDisplay d-flex justify-content-center align-items-center text-secondary display-6">
                No Display
            </div>
        </div>
    </div>
</div>

<!-- Model Start -->
<div class="modal fade " id="staffModel" tabindex="-1" aria-labelledby="adminModelLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title fs-5" id="adminModelLabel">Staff Information</h2>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">

            <div class="input-group mb-4">
                <label class="input-group-text" for="inputRole"><i class="las la-user-tie"></i></label>
                <select class="form-select" id="inputRole">
                    <option value="" selected disabled>-- Select Role --</option>
                    <option value="manager">Manager</option>
                    <option value="employee">Employee</option>
                    <option value="common">Common</option>
                </select>
            </div>

            <form action="{{ route('staff.staff.store') }}" method="POST" class="generalForm">
                @csrf
                <input type="hidden" name="position" value="" id="staffPosition">

                <div class="modal-body">
                    <div class="row">

                        <div class="col-md-6">
                            <div class="input-group flex-nowrap mb-3">
                                <span class="input-group-text" id="Name"><i class="las la-user"></i></span>
                                <input type="text" class="form-control" placeholder="Enter Name" aria-label="Name" aria-describedby="Name" name="name">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="input-group flex-nowrap mb-3">
                                <span class="input-group-text" id="Email"><i class="las la-envelope"></i></span>
                                <input type="email" class="form-control" placeholder="Enter Email" aria-label="Email" aria-describedby="Email" name="email">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="input-group flex-nowrap mb-3">
                                <span class="input-group-text" id="Password"><i class="las la-lock"></i></span>
                                <input type="password" class="form-control" placeholder="Enter Password" aria-label="Password" aria-describedby="Password" name="password">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="input-group flex-nowrap mb-3">
                                <span class="input-group-text" id="Phone"><i class="las la-phone-volume"></i></span>
                                <input type="number" class="form-control" placeholder="Enter Phone #" aria-label="Phone" aria-describedby="Phone" name="phone">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="input-group mb-3">
                                <label class="input-group-text" for="Gender"><i class="las la-user-friends"></i></label>
                                <select class="form-select" id="Gender" name="gender">
                                    <option value="male" selected>Male</option>
                                    <option value="2">Female</option>
                                    <option value="2">Other</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="input-group mb-3">
                                <label class="input-group-text" for="inputRole"><i class="las la-user-tie"></i></label>
                                <select class="form-select" id="inputRole" name="position">
                                    <option value="" selected disabled>-- Select Role --</option>
                                    <option value="manager">Manager</option>
                                    <option value="employee">Employee</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12 mb-3 mt-3">Access Application</div>
                        <!-- <div class="col-md-6 mb-3 mt-3 d-flex justify-content-end align-items-center">
                            <div class="btn-group btn-group-sm" role="group" aria-label="Basic example">
                                <button type="button" class="btn btn-primary isManager">Manager</button>
                                <button type="button" class="btn btn-primary isEmployee">Employee</button>
                            </div>
                        </div> -->

                    </div>
                    <div class="row">

                        <div class="col-3 mb-2">
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <input class="form-check-input me-1" name="player" type="checkbox" value="1" id="inputPlayerList">
                                    <label class="form-check-label" for="inputPlayerList">Client List</label>
                                </li>
                            </ul>
                        </div>

                        <div class="col-3 mb-2">
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <input class="form-check-input me-1" name="set_player" type="checkbox" value="1" id="inputSetPlayer">
                                    <label class="form-check-label" for="inputSetPlayer">Client Add</label>
                                </li>
                            </ul>
                        </div>

                        <div class="col-3 mb-2">
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <input class="form-check-input me-1" name="set_player_point" type="checkbox" value="1" id="inputSetPlayerPoint">
                                    <label class="form-check-label" for="inputSetPlayerPoint">Point Assign</label>
                                </li>
                            </ul>
                        </div>

                        <div class="col-3 mb-2">
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <input class="form-check-input me-1" name="machine" type="checkbox" value="1" id="inputMachine">
                                    <label class="form-check-label" for="inputMachine">Machine</label>
                                </li>
                            </ul>
                        </div>

                        <div class="col-3 mb-2">
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <input class="form-check-input me-1" name="winning" type="checkbox" value="1" id="inputWinning">
                                    <label class="form-check-label" for="inputWinning">Ticket</label>
                                </li>
                            </ul>
                        </div>

                        <div class="col-3 mb-2">
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <input class="form-check-input me-1" name="reading" type="checkbox" value="1" id="inputReading">
                                    <label class="form-check-label" for="inputReading">Reading</label>
                                </li>
                            </ul>
                        </div>

                        <div class="col-3 mb-2">
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <input class="form-check-input me-1" name="chat" type="checkbox" value="1" id="inputChat">
                                    <label class="form-check-label" for="inputChat">Chat</label>
                                </li>
                            </ul>
                        </div>

                        <div class="col-3 mb-2">
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <input class="form-check-input me-1" name="raffle" type="checkbox" value="1" id="inputRaffle">
                                    <label class="form-check-label" for="inputRaffle">Raffle</label>
                                </li>
                            </ul>
                        </div>

                        <div class="col-3 mb-2">
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <input class="form-check-input me-1" name="staff" type="checkbox" value="1" id="inputStaff">
                                    <label class="form-check-label" for="inputStaff">Staff</label>
                                </li>
                            </ul>
                        </div>

                        <div class="col-3 mb-2">
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <input class="form-check-input me-1" name="bank" type="checkbox" value="1" id="inputBank">
                                    <label class="form-check-label" for="inputBank">Bank</label>
                                </li>
                            </ul>
                        </div>

                        <div class="col-3 mb-2">
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <input class="form-check-input me-1" name="bank_all" type="checkbox" value="1" id="inputBankAll">
                                    <label class="form-check-label" for="inputBankAll">Bank All</label>
                                </li>
                            </ul>
                        </div>

                        <div class="col-3 mb-2">
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <input class="form-check-input me-1" name="transaction" type="checkbox" value="1" id="inputTransaction">
                                    <label class="form-check-label" for="inputTransaction">Transaction</label>
                                </li>
                            </ul>
                        </div>

                        <div class="col-3 mb-2">
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <input class="form-check-input me-1" name="setting" type="checkbox" value="1" id="inputSetting">
                                    <label class="form-check-label" for="inputSetting">Setting</label>
                                </li>
                            </ul>
                        </div>


                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default border" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Staff</button>
                </div>
            </form>

            <!-- COMMON STAFF -->
            <form action="{{ route('staff.staff.common.store') }}" method="POST" class="commonForm d-none">
                @csrf

                <div class="row">

                    <div class="col-md-6">
                        <div class="input-group flex-nowrap mb-3">
                            <span class="input-group-text" id="Name"><i class="las la-user"></i></span>
                            <input type="text" class="form-control" placeholder="Enter Name" aria-label="Name" aria-describedby="Name" name="name">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="input-group flex-nowrap mb-3">
                            <span class="input-group-text" id="Email"><i class="las la-envelope"></i></span>
                            <input type="email" class="form-control" placeholder="Enter Email" aria-label="Email" aria-describedby="Email" name="email">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="input-group flex-nowrap mb-3">
                            <span class="input-group-text" id="Phone"><i class="las la-phone-volume"></i></span>
                            <input type="number" class="form-control" placeholder="Enter Phone #" aria-label="Phone" aria-describedby="Phone" name="phone">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="input-group mb-3">
                            <label class="input-group-text" for="Gender"><i class="las la-user-friends"></i></label>
                            <select class="form-select" id="Gender" name="gender">
                                <option value="male" selected>Male</option>
                                <option value="2">Female</option>
                                <option value="2">Other</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="input-group flex-nowrap mb-3">
                            <span class="input-group-text" id="Hourly"><i class="las la-clock"></i></span>
                            <input type="number" class="form-control" placeholder="$ Hourly charge" aria-label="Hourly" aria-describedby="Hourly" name="hourly">
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default border" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Staff</button>
                </div>
            </form>
        </div>


    </div>
  </div>
</div>
<!-- Model End -->

@push('post_script')
<script>
    $(document).ready(function () {

        $('#staffTable').DataTable({
            responsive: true,
            lengthChange: false,
            pageLength: 10,
            processing: true,
        });

        $('#inputRole').on('change', function() {
            var Role = $(this).val();

            if( Role == 'common' ) {
                $('.generalForm').addClass('d-none');
                $('.commonForm').removeClass('d-none');
            } else {
                $('#staffPosition').val(Role);
                $('.generalForm').removeClass('d-none');
                $('.commonForm').addClass('d-none');
            }

            if(Role == 'manager') {
                $('#inputPlayerList').prop('checked', true);
                $('#inputSetPlayer').prop('checked', true);
                $('#inputSetPlayerPoint').prop('checked', true);
                $('#inputMachine').prop('checked', true);
                $('#inputWinning').prop('checked', true);
                $('#inputReading').prop('checked', true);
                $('#inputChat').prop('checked', true);
                $('#inputRaffle').prop('checked', true);
                $('#inputStaff').prop('checked', true);
                $('#inputSetting').prop('checked', false);
                $('#inputBank').prop('checked', true);
                $('#inputBankAll').prop('checked', false);
                $('#inputTransaction').prop('checked', true);
            } else if(Role == 'employee') {
                $('#inputPlayerList').prop('checked', true);
                $('#inputSetPlayer').prop('checked', true);
                $('#inputSetPlayerPoint').prop('checked', true);
                $('#inputMachine').prop('checked', false);
                $('#inputWinning').prop('checked', true);
                $('#inputReading').prop('checked', true);
                $('#inputChat').prop('checked', true);
                $('#inputRaffle').prop('checked', false);
                $('#inputStaff').prop('checked', false);
                $('#inputSetting').prop('checked', false);
                $('#inputBank').prop('checked', false);
                $('#inputBankAll').prop('checked', false);
                $('#inputTransaction').prop('checked', true);
            }
        });

        $('#staffTable tbody').on('click', '.viewStaffDetail', function(event) {
            event.preventDefault();
            const userId = $(this).attr('data');
            fetchUserDetails(userId);
        });

        function fetchUserDetails(userId) {
            const csrfToken = $('meta[name="csrf-token"]').attr('content');
            $('#userID').val(userId);
            $.ajax({
                url: '/staff/staff/detail/',
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                data: { id : userId },
                success: function(response) {

                    $('.isDisplay').removeClass('d-none');
                    $('.noDisplay').addClass('d-none');

                    $('.userEmail').text(response.user.email);
                    $('.userName').text(response.user.name);

                    $('.perContainer').empty();
                    $.each(response.permission, function(key, value) {
                        
                        var checked = value === 1 ? 'checked' : '';
                        
                        if      ( key === 'player' ) { var labelText = 'Client List';  }
                        else if ( key === 'set_player' ) { var labelText = 'Client Add';  }
                        else if ( key === 'set_player_point' ) { var labelText = 'Point Assign';  }
                        else if ( key === 'machine' ) { var labelText = 'Machine';  }
                        else if ( key === 'winning' ) { var labelText = 'Ticket';  }
                        else if ( key === 'reading' ) { var labelText = 'Reading';  }
                        else if ( key === 'chat' ) { var labelText = 'Chat';  }
                        else if ( key === 'staff' ) { var labelText = 'Staff';  }
                        else if ( key === 'setting' ) { var labelText = 'Settings';  }
                        else if ( key === 'raffle' ) { var labelText = 'Raffle';  }
                        else if ( key === 'bank' ) { var labelText = 'Bank';  }
                        else if ( key === 'bank_all' ) { var labelText = 'Bank All';  }
                        else if ( key === 'transaction' ) { var labelText = 'Transaction';  }
                        

                        var list = `<div class="col-6 mb-2">
                                        <ul class="list-group">
                                            <li class="list-group-item">
                                                <input class="form-check-input me-1" name="${key}" type="checkbox" value="1" id="${key}List" ${checked}>
                                                <label class="form-check-label" for="${key}List">${labelText}</label>
                                            </li>
                                        </ul>
                                    </div>`;
                        $('.perContainer').append(list);
                    });

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