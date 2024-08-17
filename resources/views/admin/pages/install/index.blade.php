@extends('admin.layouts.master')
@section('title', isset($title) ? $title : 'Home')
@section('content')
@push('post_css')
<style>
    #videoElement {
        width: 100%;
        max-width: 100%;
    }
    #canvas {
        display: none;
    }
    #captureBtn {
        margin-top: 10px;
        cursor: pointer;
    }
    .installation .nav-item button.active {
        background-color: #5F80F8;
    }
    .installation .nav-item button {
        color: black
    }
</style>
@endpush

<div class="row d-flex justify-content-center align-items-center">
    <div class="col-md-8 text-center p-3">
        <h3 class="text-dark fw-semibold"><i class="las la-cube"></i> Software installation</h3>
    </div>

    <div class="col-md-6">

        <ul class="nav nav-pills mb-3 installation" id="pills-tab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="pills-security-tab" data-bs-toggle="pill" data-bs-target="#pills-security" type="button" role="tab" aria-controls="pills-security" aria-selected="true"><i class="las la-user-lock"></i> Account Security</button>
            </li>

            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pills-room-tab" data-bs-toggle="pill" data-bs-target="#pills-room" type="button" role="tab" aria-controls="pills-room" aria-selected="false"><i class="las la-landmark"></i> Gaming Rooms</button>
            </li>
        </ul>

        <form id="installationForm" action="{{ route('install.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <!-- FaceID Input file -->
        <input type="file" name="profile" class="form-control d-none" value="" id="ProfileImg">
        <div class="tab-content" id="pills-tabContent">

            <!-- Account Security -->
            <div class="tab-pane fade show active" id="pills-security" role="tabpanel" aria-labelledby="pills-security-tab" tabindex="0">
                <div class="card">
                    <div class="card-header p-3">Admin Login</div>
                    <div class="card-body">
                        <div class="row">

                            <p class="card-text">
                                <i class="las la-arrow-right"></i> Enter your Admin account new details.
                            </p>

                            <div class="col-md-6 mb-4">
                                <div class="form-floating">
                                    <input type="number" class="form-control inputPin" name="pincode" id="floatingPincode" placeholder="Enter New Pincode" readonly required>
                                    <label for="floatingPincode">Enter New Pincode</label>
                                </div>
                            </div>
                            <div class="col-md-3 mb-4">
                                <div class="form-floating">
                                    <select class="form-select pinRange" id="PincodeRangeSelect" aria-label="Pincode Range">
                                        <option value="4" selected>4</option>
                                        <option value="6">6</option>
                                        <option value="8">8</option>
                                        <option value="10">10</option>
                                        <option value="12">12</option>
                                    </select>
                                    <label for="PincodeRangeSelect">Pincode Range</label>
                                </div>
                            </div>

                            <div class="col-md-3 mb-4 d-flex">
                                <button type="button" class="btn w-100 btn-primary generatePin"><i class="las la-sync"></i> Create PIN</button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Gaming Room -->
            <div class="tab-pane fade" id="pills-room" role="tabpanel" aria-labelledby="pills-room-tab" tabindex="0">
                <div class="card">
                    <div class="card-header">Gaming Room</div>
                    <div class="card-body">
                        <div class="row">
                            <p class="card-text">
                            <i class="las la-arrow-right"></i> Please Enter the Room title, Quantity
                            </p>
                            @foreach( $admin->rooms as $room )
                            <div class="col-md-6 mb-3">
                                <div class="form-floating">
                                    <input  type="text" 
                                            class="form-control" 
                                            name="{{ $room->id }}" id="floatingRoom{{ $room->id }}" 
                                            placeholder="Enter Room Name" 
                                            required>

                                    <label for="floatingRoom{{ $room->id }}">{{ $room->name }}</label>

                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-floating">
                                    <input  type="number" 
                                            class="form-control" 
                                            id="floatingMachine{{ $room->id }}" 
                                            name="qty_{{ $room->id }}" 
                                            placeholder="Enter Machine Qunatity" 
                                            required>
                                    <label for="floatingMachine{{ $room->id }}">Machine Quantity</label>
                                </div>
                            </div>
                            @endforeach

                            <div class="col-md-12 d-flex justify-content-center align-items-center">
                                <button type="submit" class="btn btn-primary w-50">Save All</button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>


</div>
@push('post_script')
<script>

$('.generatePin').on('click', function() {
    var Range = $('.pinRange').val();
    if (Range) {

        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        $.ajax({
            url: '/install/pincode',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            data: { range : Range },
            success: function(response) {
                $('.inputPin').val(response);
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
