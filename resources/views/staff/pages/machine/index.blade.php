@extends('staff.layouts.master')
@section('title', isset($title) ? $title : 'Home')
@section('content')
@push('post_css')
<style>
.wrapper {
    height: 80.5vh !important;
}
</style>
@endpush
<div class="row admin-section h-100">

    <div class="col-md-8 d-flex h-100">
        <div class="card w-100">

            <div class="card-header bg-primary d-flex justify-content-between align-items-center">
                Machine List
                <button type="button" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#addMachineModel" data-bs-whatever="@mdo"> Add Machine</button>
            </div>

            <div class="card-body">
                <table id="machineTable" class="table table-sm table-hover pending-table">
                    <thead>
                        <tr>
                            <th>MID#</th>
                            <th>Type</th>
                            <th>Factor</th>
                            <th>Game</th>
                            <th>Reading</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach( $machines as $item )
                        <tr>
                            <td>{{ $item->serial }}</td>
                            <td>{{ $item->type }}</td>
                            <td>1.00</td>
                            <td>{{ $item->game }}</td>
                            <td>
                                @if( Rcurr($item->id) && Rcurr($item->id)->in && is_null(Rcurr($item->id)->out))
                                    <span class="badge text-bg-success p-1 ">IN</span>                         
                                @elseif(Rcurr($item->id) && Rcurr($item->id)->in && Rcurr($item->id)->out)
                                    <span class="badge text-bg-success p-1 ">Complete</span>                   
                                @endif
                            </td>
                            <td>
                                @if( $item->status == 1 )
                                    ACTIVE
                                @else
                                    BLOCK
                                @endif
                            </td>
                            <td>  
                                <div class="btn-group btn-group rounded-pill w-100" role="group" aria-label="Basic example">
                                    <a href="#" class="displayInfo btn btn-sm text-primary-emphasis bg-primary-subtle" data="{{ $item->id }}">VIEW</a>
                                    @if( $item->status == 1 )
                                    <a href="{{ route('staff.machine.block', encrypt($item->id)) }}" class="btn btn-sm text-danger-emphasis bg-danger-subtle">BLOCK</a>
                                    @else
                                    <a href="{{ route('staff.machine.active', encrypt($item->id)) }}" class="btn btn-sm text-success-emphasis bg-success-subtle">ACTIVE</a>
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

    <div class="col-md-4 admin-right">
        <div class="card w-100">
            <div class="card-header bg-primary">Machine Details</div>
            <div class="card-body isDisplay d-none">
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Machine ID #
                        <span class="MID">0.00</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Prev. Reading(In)
                        <span class="prevIn">0.00</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Prev. Reading(Out)
                        <span class="prevOut">0.00</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Curr. Reading(In)
                        <span class="curIn">0.00</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Curr. Reading(Out)
                        <span class="curOut">0.00</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Status
                        <span class="mStatus">--------</span>
                    </li>
                </ul>

                <div class="p-2 mt-3">
                    
                    <form action="{{ route('staff.machine.edit') }}" method="post">
                        @csrf
                        <input type="hidden" class="machineId" name="machine_id">
                        <input type="hidden" name="today" value="" class="currentTimeInput">

                        <div class="row">

                            <div class="col-12 d-flex justify-content-center align-items-center">
                                <h5 class="fw-semibold">Edit Machine Details</h5>
                            </div>

                            <!-- <div class="col-6 mb-2">
                                <label for="TypeInput" class="form-label">Machine Type</label>
                                <input type="text" class="form-control" name="type" id="TypeInput" placeholder="Enter Machine Type" aria-label="Machine Type">
                            </div>

                            <div class="col-6 mb-2">
                                <label for="GameInput" class="form-label">Machine Game</label>
                                <input type="text" class="form-control" name="game" id="GameInput" placeholder="Enter Machine Game" aria-label="Machine Name">
                            </div> -->

                            <div class="col-6 mb-3 InInputDiv">
                                <label for="InInput" class="form-label">Current In</label>
                                <input type="number" class="form-control" name="in" id="InInput" placeholder="Enter Current In" aria-label="Current In">
                            </div>

                            <div class="col-6 mb-3 OutInputDiv">
                                <label for="OutInput" class="form-label">Current Out</label>
                                <input type="number" class="form-control" name="out" id="OutInput" placeholder="Enter Current Out" aria-label="Current Out">
                            </div>

                            <div class="col-12 float-end saveBtn">
                                <button type="submit" class="btn btn-primary float-end">Save Machine</button>
                            </div>
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

<!-- Model Machine Add Start -->
<div class="modal fade" id="addMachineModel" tabindex="-1" aria-labelledby="addMachineModelLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title fs-5" id="addMachineModelLabel">Machine Details</h2>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="{{ route('staff.machine.store') }}" method="POST">
            @csrf
            <div class="modal-body">

                <div class="form-floating mb-3">
                    <input type="text" class="form-control" name="type" id="typeInput" placeholder="Machine Type" required>
                    <label for="typeInput">Machine Type</label>
                </div>

                <div class="form-floating mb-3">
                    <input type="text" class="form-control" name="game" id="gameInput" placeholder="Machine Game" required>
                    <label for="gameInput">Game Name</label>
                </div>

                <div class="form-floating mb-3">
                    <input type="number" class="form-control" name="serial" id="serialInput" placeholder="Machine Serial" required>
                    <label for="serialInput">Serial Number</label>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default border" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Add Machine</button>
            </div>
        </form>
    </div>
  </div>
</div>
<!-- Model End -->

@push('post_script')
<script>
$(document).ready(function () {

    $('#machineTable').DataTable({
        responsive: true,
        lengthChange: false,
        pageLength: 10,
        processing: true,
    });

    $('#machineTable tbody').on('click', '.displayInfo', function(event) {
        event.preventDefault();
        const itemId = $(this).attr('data');
        
        // Today
        let now = new Date();
        let year = now.getFullYear();
        let month = String(now.getMonth() + 1).padStart(2, '0');
        let day = String(now.getDate()).padStart(2, '0');
        let hours = String(now.getHours()).padStart(2, '0');
        let minutes = String(now.getMinutes()).padStart(2, '0');
        let seconds = String(now.getSeconds()).padStart(2, '0');

        let DateTime = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
        $('.currentTimeInput').val(DateTime);

        if(itemId) {
            const csrfToken = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                url: '/staff/machine/info',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                data: { id : itemId },
                success: function(response) {

                    $('.InInputDiv').addClass('d-none');
                    $('.OutInputDiv').addClass('d-none');

                    $('.isDisplay').removeClass('d-none');
                    $('.noDisplay').addClass('d-none');
                    
                    $('.MID').text(response.machine.serial);
                    if( response.machine.status == 1 ) {
                        $('.mStatus').text('ACTIVE');
                    } else {
                        $('.mStatus').text('BLOCK');
                    }
                    $('.machineId').val(response.machine.id);
                    // $('#TypeInput').val(response.machine.type);
                    // $('#GameInput').val(response.machine.game);
                    
                    if(response.previous != null) {
                        $('.prevIn').text(response.previous.in);
                        $('.prevOut').text(response.previous.out);
                    } else {
                        $('.prevIn').text('0.00');
                        $('.prevOut').text('0.00');
                    }
                    
                    if(response.current != null) {

                        $('.curIn').text(response.current.in);
                        $('.curOut').text(response.current.out);

                        if( response.current.in != null && response.current.out == null ) {
                            $('.OutInputDiv').removeClass('d-none');
                        } else {
                            $('.saveBtn').addClass('d-none');
                        }

                    } else {
                        $('.InInputDiv').removeClass('d-none');
                        $('.curIn').text('0.00');
                        $('.curOut').text('0.00');
                    }

                },
                error: function(xhr, status, error) {
                    console.error(error);
                }

            });
        }

    });

});
</script>
@endpush
@endsection