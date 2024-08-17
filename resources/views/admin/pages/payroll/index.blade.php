@extends('admin.layouts.master')
@section('title', isset($title) ? $title : 'Home')
@section('content')
<div class="row h-100">
    <div class="col-md-8 col-sm-12 h-100" style="padding-right: 0px; ">
        <div class="card h-100">
            <div class="card-header bg-primary">All Staff List</div>
            <div class="card-body">
                <table class="table table-hover payrollTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Checkin</th>
                            <th>Checkout</th>
                            <th>Shift</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $index = 1; $diff = 0; @endphp
                        @foreach($staffs as $staff)
                            @php $diff = 0; @endphp
                            @if(! is_null($staff->payroll->first()) )
                                @php
                                    $checkin = $staff->payroll->first()->checkin;
                                    $checkinTime = \Carbon\Carbon::parse($checkin);

                                    $checkout = $staff->payroll->first()->checkout;
                                    $checkoutTime = \Carbon\Carbon::parse($checkout);
                                    
                                    $charge = $staff->payroll->first()->hourly;

                                    $hours = $checkinTime->diffInHours($checkoutTime);
                                    $inMinute = $checkinTime->diffInMinutes($checkoutTime);
                                    $getMinute = $inMinute - ($hours * 60);

                                    $perMinCharge = $charge / 60;
                                    $amount = $perMinCharge * $inMinute;

                                @endphp
                            @endif

                            <tr>
                                <td>{{ $index++ }}</td>
                                <td>{{ $staff->name }}</td>
                                <td>
                                    @if(! is_null($staff->payroll->first()) && $checkinTime )
                                    <span class="badge bg-primary p-2 border rounded-pill w-100 mb-1">
                                        {{ $checkinTime->format('h:i:s A') }}
                                    </span>
                                    @endif
                                </td>
                                <td>
                                    @if(! is_null($staff->payroll->first()) && $checkout && $checkoutTime )
                                    <span class="badge bg-primary p-2 border rounded-pill w-100">
                                        {{ $checkoutTime->format('h:i:s A') }}
                                    </span>
                                    @endif
                                </td>
                                <td>
                                    @if(! is_null($staff->payroll->first()) && $checkin && $checkout )
                                        @if( $hours > 0 )
                                        {{ $hours }} hr, {{ $getMinute }} min
                                        @else
                                        {{ $getMinute }} min
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    @if(! is_null($staff->payroll->first()) && $checkin && $checkout )
                                        ${{ number_format($amount, 2) }}
                                    @endif
                                </td>
                                <td>
                                    @if(! is_null($staff->payroll->first()) )
                                        @if( $staff->payroll->first()->status == 0 )
                                            <span class="badge text-bg-danger p-2 rounded-pill w-100">UPAID</span>
                                        @else
                                            <span class="badge text-bg-success p-2 rounded-pill w-100">PAID</span>
                                        @endif
                                    @else
                                    @endif
                                </td>
                                <td>

                                    <div class="btn-group btn-group-sm rounded-pill w-100" role="group" aria-label="Basic example">

                                        <a href="#" class="viewStaff btn btn-sm text-primary-emphasis bg-primary-subtle" data="{{ $staff->id }}">View</a>

                                        @if(! is_null($staff->payroll->first()) && $checkin && $checkout && $staff->payroll->first()->status == 0 )
                                        <a href="{{ route('admin.payroll.status', encrypt($staff->payroll->first()->id)) }}" 
                                            class="btn btn-sm text-success-emphasis bg-success-subtle">PAID
                                        </a>
                                        @endif

                                        @if( is_null($staff->payroll->first()) )
                                        <a href="#" class="btn btn-sm text-success-emphasis bg-success-subtle checkinStaff" data="{{ $staff->id }}">IN</a>
                                        @endif

                                        @if(! is_null($staff->payroll->first()) && $staff->payroll->first()->checkout == null )
                                        <a href="#" class="btn btn-sm text-danger-emphasis bg-danger-subtle checkoutStaff" slug="{{ $staff->payroll->first()->id }}" data="{{ $staff->id }}">OUT</a>
                                        @endif
                                    </div>

                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
    <div class="col-md-4 col-sm-12 h-100 admin-right">
        <div class="card h-100 w-100"> 
            <div class="card-header bg-primary">View Info</div>
            <div class="card-body isDisplay d-none">

                <div class="row userDetail">
                    <div class="col-md-3 mb-3 d-flex align-items-center">
                        <img class="rounded userProfile" src="{{ asset('assets/general/profile.png') }}" alt="" width="100">
                    </div>
                    <div class="col-md-9 mb-3 d-flex align-items-center">
                        <ul class="list-group w-100">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="badge text-bg-secondary">Name</span>
                                <span class="userName fw-semibold">-----</span>
                            </li>
                            {{-- <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="badge text-bg-secondary">Email</span>
                                <span class="userEmail fw-semibold">------</span>
                            </li> --}}
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="badge text-bg-secondary">Rate</span>
                                <span class="userCharge fw-semibold">------</span>
                            </li>
                        </ul>
                    </div>

                    <h5 class="fw-semibold">History of Staff</h5>
                    <div class="col-md-12 History">

                        <table class="table table-sm table-bordered table-hover text-left">
                            <thead>
                                <tr>
                                    <th>Shift</th>
                                    <th>Hour</th>
                                    <th>Payment</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody class="payrollSection">
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

    $('.payrollTable').DataTable({
        responsive: true,
        lengthChange: false,
        pageLength: 10,
    });


    function getCurrentDate(){

        let now = new Date();
        let year = now.getFullYear();
        let month = String(now.getMonth() + 1).padStart(2, '0');
        let date = String(now.getDate()).padStart(2, '0');
        let hours = String(now.getHours()).padStart(2, '0');
        let minutes = String(now.getMinutes()).padStart(2, '0');
        let seconds = String(now.getSeconds()).padStart(2, '0');

        let formattedDateTime = `${year}-${month}-${date} ${hours}:${minutes}:${seconds}`;
        return formattedDateTime;
    }

    // CHECKIN STAFF
    $('.payrollTable tbody').on('click', '.checkinStaff', function(event) {
        event.preventDefault();
        const userId = $(this).attr('data');

        if(userId) {

            var currentTime = getCurrentDate();
            const csrfToken = $('meta[name="csrf-token"]').attr('content');

            $.ajax({
                url: '/admin/payroll/checkin/',
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                data: { id : userId, time : currentTime },
                success: function(response) {
                    if(response.status == 'success') {
                        window.location.href = '/admin/payroll/staff_list';
                    }
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });

        }
    });

    // CHECKOUT STAFF
    $('.payrollTable tbody').on('click', '.checkoutStaff', function(event) {
        event.preventDefault();
        const userId = $(this).attr('data');
        const userSlug = $(this).attr('slug');

        if(userId) {

            var currentTime = getCurrentDate();
            const csrfToken = $('meta[name="csrf-token"]').attr('content');
            
            $.ajax({
                url: '/admin/payroll/checkout/',
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                data: { id : userId, time : currentTime, slug : userSlug },
                success: function(response) {
                  
                    if(response.status == 'success') {
                        window.location.href = '/admin/payroll/staff_list';
                    }
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });

        }
    });


    // VIEW INFO OF STAFF
    $('.payrollTable tbody').on('click', '.viewStaff', function(event) {
        event.preventDefault();
        const userId = $(this).attr('data');

        fetchUser(userId);
    });

    function fetchUser(userId) {
            const csrfToken = $('meta[name="csrf-token"]').attr('content');
            $('#userID').val(userId);
            $.ajax({
                url: '/admin/payroll/detail/',
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
                    $('.userCharge').text('$'+ response.user.hourly + '/hr');
                    
                    $('.payrollSection').empty();

                    $.each(response.payroll, function(key, value) {

                        if( value.checkin && value.checkout) {
                            
                            var checkin = new Date(value.checkin);
                            var checkout = new Date(value.checkout);

                            var timeDiff = checkout - checkin;
                            var hoursWorked = Math.floor(timeDiff / (1000 * 60 * 60));
                            var minutesWorked = Math.floor((timeDiff % (1000 * 60 * 60)) / (1000 * 60));
                            var payment = (hoursWorked + minutesWorked / 60) * value.hourly;
    
                            // var formattedCheckinDate = checkin.toLocaleString('en-US', { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit', hour12: true });
                            // var formattedCheckoutDate = checkout.toLocaleString('en-US', { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit', hour12: true });
    
                            var inDate = `${checkin.toLocaleDateString('en-US', { day: '2-digit' })}`;
                            var inTime = `${checkin.toLocaleDateString('en-US', { month: 'short', hour: '2-digit', minute: '2-digit', hour12: true })}`;
    
                            var outDate = `${checkout.toLocaleDateString('en-US', { day: '2-digit' })}`;
                            var outTime = `${checkout.toLocaleDateString('en-US', { month: 'short', hour: '2-digit', minute: '2-digit', hour12: true })}`;

                            var hoursWorkedText = `${hoursWorked} hour${hoursWorked !== 1 ? 's' : ''}`;
                            var minutesWorkedText = `${minutesWorked} minute${minutesWorked !== 1 ? 's' : ''}`;

                            var Status = '';
                            if( value.status == 0 ) {
                                Status = '<span class="badge text-bg-danger rounded-pill p-1 w-100">UPAID</span>';
                            } else {
                                Status = '<span class="badge text-bg-success rounded-pill p-1 w-100">PAID</span>';
                            }

                            if( hoursWorked == 0 ) {
                                hoursWorkedText = '';
                            }
    
                            var list = `
                                <tr>
                                    <td>
                                        <span>${inDate} ${inTime}</span></br>
                                        <span>${outDate} ${outTime}</span>
                                    </td>
                                    <td>${hoursWorkedText} ${minutesWorkedText}</td>
                                    <td>$ ${payment.toFixed(2)}</td>
                                    <td>${Status}</td>
                                
                                </tr>`;
                            $('.payrollSection').append(list);
                        }
                    });

                    if (response.payroll.length === 0) {
                        var noRecord = '<tr><td colspan="4">No record found</td></tr>';
                        $('.payrollSection').append(noRecord);
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