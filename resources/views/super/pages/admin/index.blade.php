@extends('super.layouts.master')
@section('title', isset($title) ? $title : 'Home')
@section('content')

<div class="row mb-2 admin-section h-100">
    <div class="col-md-8 h-100">
        <div class="card rounded-0 h-100">
            <div class="card-header bg-primary d-flex justify-content-between align-items-center">
                Admin List
                <button type="button" class="btn btn-sm btn-dark" data-bs-toggle="modal" data-bs-target="#adminModel" data-bs-whatever="@mdo"><i class="las la-plus"></i> Add Admin</button>
            </div>
            <div class="card-body">
                <table id="pendingTable" class="table table-sm table-responsive table-hover pending-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Gender</th>
                            <th>Passcode</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach( $admins as $item )
                        <tr>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->key }}</td>
                            <td>{{ $item->gender }}</td>
                            <td>
                                @if($item->status == 1)
                                <span class="p-2 badge bg-success">Active</span>
                                @else
                                <span class="p-2 badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <a href="#" data="{{ encrypt($item->location) }}" class="viewPlayer">View</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>            
        </div>    
    </div>

    <div class="col-md-4 admin-right">
        <div class="card w-100 rounded-0">
            <div class="card-header bg-primary">
                Admin Profile
            </div>
            <div class="card-body d-flex justify-content-center align-items-center" style="overflow-wrap:anywhere;">

                <!-- <h1 class="display-6">No Display</h1> -->
                <p class="isDisplay"></p>

            </div>
        </div>
    </div>
</div>

<!-- Model Start -->
<div class="modal fade" id="adminModel" tabindex="-1" aria-labelledby="adminModelLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title fs-5" id="adminModelLabel">Admin Information</h2>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="{{ route('super.admin.store') }}" method="POST">
            @csrf
            <div class="modal-body">

                <div class="input-group flex-nowrap mb-3">
                    <span class="input-group-text" id="DateRange"><i class="las la-calendar-week"></i></span>
                    <input type="text" class="form-control" id="daterange" aria-label="Date" aria-describedby="Date">
                    <input type="hidden" name="start_date" id="startDate">
                    <input type="hidden" name="end_date" id="endDate">
                </div>

                <div class="input-group flex-nowrap mb-3">
                    <span class="input-group-text" id="Room"><i class="las la-home"></i></span>
                    <input type="number" class="form-control" placeholder="Enter Game Rooms quantity" aria-label="Room" aria-describedby="Room" name="rooms">
                </div>

                <div class="input-group flex-nowrap mb-3">
                    <span class="input-group-text" id="Name"><i class="las la-user"></i></span>
                    <input type="text" class="form-control" placeholder="Enter Name" aria-label="Name" aria-describedby="Name" name="name">
                </div>

                <div class="input-group flex-nowrap mb-3">
                    <span class="input-group-text" id="Phone"><i class="las la-phone-volume"></i></span>
                    <input type="number" class="form-control" placeholder="Enter Phone Number" aria-label="Phone" aria-describedby="Phone" name="phone">
                </div>

                <div class="input-group mb-3">
                    <label class="input-group-text" for="Gender"><i class="las la-user-friends"></i></label>
                    <select class="form-select" id="Gender" name="gender">
                        <option value="male" selected>Male</option>
                        <option value="2">Female</option>
                        <option value="2">Other</option>
                    </select>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default border" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Add Admin</button>
            </div>
        </form>
    </div>
  </div>
</div>
<!-- Model End -->

@push('post_script')
<script>
    // DataTables.net
    $(document).ready(function () {
        $('#pendingTable, #acceptTable').DataTable({
            responsive: true,
            pageLength: 10,
            lengthChange: false
        });
    });

    $('.viewPlayer').on('click', function(event) {
        event.preventDefault();
        const userId = $(this).attr('data');
        $('.isDisplay').empty();
        $('.isDisplay').text(userId);
        console.log(userId);
    });

    // Date Range
    $('#daterange').daterangepicker();
    $('#daterange').on('apply.daterangepicker', function(ev, picker) {
        const start_date = picker.startDate.format('YYYY-MM-DD');
        const end_date = picker.endDate.format('YYYY-MM-DD');
        $('#startDate').attr('value', start_date);
        $('#endDate').attr('value', end_date);
        
    });
</script>
@endpush
@endsection