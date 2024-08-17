@extends('admin.layouts.master')
@section('title', isset($title) ? $title : 'Home')
@section('content')


<form action="{{ route('install.reading.store') }}" method="post">
    @csrf
    <div class="row d-flex justify-content-center align-items-center">

        <div class="col-md-12 text-center p-4">
            <h3 class="text-dark fw-semibold mb-0"><i class="las la-cube"></i> Machine installation</h3>
        </div>
        <div class="col-md-12 mb-3 text-center alertMessage d-none">
            <span class="text-danger">This serial number already in use in this room</span>
        </div>
    
        @foreach( $rooms as $room )
        <div class="col-md-8 mb-3">
            <div class="card">
                <div class="card-header bg-light fw-semibold">
                    <i class="lab la-gitter"></i> {{ $room->name }}
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach( $room->machines as $machine )
                        <div class="col-md-2 mb-3">
                            <div class="form-floating">
                                <input type="number" class="form-control serialInput" data="{{$room->id}}" name="data[{{$room->id}}][{{$machine->id}}][serial]" id="InputSerial{{ $machine->id }}" required>
                                <label for="InputSerial{{ $machine->id }}">Serial</label>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="form-floating">
                                <input type="text" class="form-control" name="data[{{$room->id}}][{{$machine->id}}][type]" id="InputType{{ $machine->id }}" required>
                                <label for="InputType{{ $machine->id }}">Machine Type</label>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="form-floating">
                                <input type="text" class="form-control" name="data[{{$room->id}}][{{$machine->id}}][game]" id="InputGame{{ $machine->id }}" required>
                                <label for="InputGame{{ $machine->id }}">Game</label>
                            </div>
                        </div>
                        <div class="col-md-2 mb-3">
                            <div class="form-floating">
                                <input type="number" class="form-control" name="data[{{$room->id}}][{{$machine->id}}][in_reading]" id="InputIn{{ $machine->id }}" required>
                                <label for="InputIn{{ $machine->id }}">In Reading</label>
                            </div>
                        </div>
                        <div class="col-md-2 mb-3">
                            <div class="form-floating">
                                <input type="number" class="form-control" name="data[{{$room->id}}][{{$machine->id}}][out_reading]" id="inputOut{{ $machine->id }}" required>
                                <label for="inputOut{{ $machine->id }}">Out Reading</label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endforeach

        <div class="col-md-12 text-center mt-4 mb-3">
            <button class="btn btn-primary p-2">Save Reading</button>
        </div>

    </div>
</form>

@push('post_script')
<script>

$('.serialInput').on('input', function() {
    var currentValue = parseFloat($(this).val());
    var roomId = $(this).attr('data');

    var $inputs = $('.serialInput').not(this);
    var hasConflict = $inputs.toArray().some(function(input) {
        var otherValue = parseFloat($(input).val());
        var otherRoomId = $(input).attr('data');
        return !isNaN(otherValue) && otherValue === currentValue && otherRoomId === roomId;
    });

    if (hasConflict) {
        $(this).addClass('is-invalid');
        $('.alertMessage').removeClass('d-none');
    } else {
        $(this).removeClass('is-invalid');
        $('.alertMessage').addClass('d-none');
    }
});


</script>
@endpush
@endsection
