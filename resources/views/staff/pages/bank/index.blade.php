@extends('staff.layouts.master')
@section('title', isset($title) ? $title : 'Home')
@section('content')

<div class="row admin-section h-100">

    @if( checkPermit( Auth::id() )->bank_all == 1 )
    <div class="col-md-4 h-100 d-flex">
        <div class="card w-100">
            <div class="card-header bg-primary">Add Bank Entry</div>
            <div class="card-body">
                <form action="{{ route('staff.bank.store') }}" method="post">
                    @csrf
                    <input type="hidden" name="total" class="inputTotal">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <select class="form-control select2" name="reciever_id" required>
                                @foreach( $staff as $item )
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-12 text-center mb-3">
                            Cash in Stacks
                        </div>

                        <div class="col-md-12 mb-3">
                            <span class="badge text-bg-secondary p-3 d-block fs-6">Total: <span class="totalAmount">0</span></span>
                        </div>

                        <div class="col-md-4 mb-2">
                            <input type="number" class="form-control stackInput" data=1 name="stack_1" placeholder="Stack 1">
                        </div>
                        <div class="col-md-4 mb-2">
                            <input type="number" class="form-control stackInput" data=5 name="stack_5" placeholder="Stack 5">
                        </div>
                        <div class="col-md-4 mb-2">
                            <input type="number" class="form-control stackInput" data=10 name="stack_10" placeholder="Stack 10">
                        </div>
                        <div class="col-md-4 mb-2">
                            <input type="number" class="form-control stackInput" data=20 name="stack_20" placeholder="Stack 20">
                        </div>
                        <div class="col-md-4 mb-2">
                            <input type="number" class="form-control stackInput" data=50 name="stack_50" placeholder="Stack 50">
                        </div>
                        <div class="col-md-4 mb-2">
                            <input type="number" class="form-control stackInput" data=100 name="stack_100" placeholder="Stack 100">
                        </div>

                        <div class="col-md-12 mb-3">
                            <textarea id="" class="form-control" name="note" placeholder="Short Note"></textarea>
                        </div>

                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary w-100">Add Entry</button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <div class=" @if(checkPermit( Auth::id() )->bank_all == 1) col-md-8 admin-right @else col-md-12 @endif h-100">
        <div class="card w-100">

            <div class="card-header bg-primary">
                Bank List
            </div>

            <div class="card-body">
                <table id="bankTable" class="table table-sm table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>($) 1</th>
                            <th>Stack 5</th>
                            <th>Stack 10</th>
                            <th>Stack 20</th>
                            <th>Stack 50</th>
                            <th>Stack 100</th>
                            <th>Total</th></th>
                            <th>Note</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach( $banks as $item )
                            <tr>
                                <td>{{ $item->staff->first()->name }}</td>
                                <td>{{ $item['1'] }}</td>
                                <td>{{ $item['5'] }}</td>
                                <td>{{ $item['10'] }}</td>
                                <td>{{ $item['20'] }}</td>
                                <td>{{ $item['50'] }}</td>
                                <td>{{ $item['100'] }}</td>
                                <td>{{ $item['total'] }}</td>
                                <td>{{ $item['note'] }}</td>
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

    $('#bankTable').DataTable({
        responsive: true,
        lengthChange: false
    });

    $('.select2').select2({
        width: 'resolve',
        theme: "classic",
        placeholder: 'Select an option',
    });

    $('.stackInput').on('input', function() {

        let total = 0;
        $('.stackInput').each(function() {
            let value = parseFloat($(this).val());
            if (!isNaN(value)) {
                
                var note = $(this).attr('data');
                total += note*value;
            }
        });
        $('.inputTotal').val(total);
        $('.totalAmount').text(total);
    });


});
</script>
@endpush
@endsection