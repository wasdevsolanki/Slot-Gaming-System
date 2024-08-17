@extends('admin.layouts.master')
@section('title', isset($title) ? $title : 'Home')
@section('content')

<div class="row admin-section h-100">

    <div class="col-md-4">
        <div class="card w-100 h-100">
            <div class="card-header bg-primary">Add Bank Entry</div>
            <div class="card-body p-4 d-flex flex-column">
                <form action="{{ route('admin.bank.store') }}" method="post">
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

                        
                            <div class="col-md-12 w-100 p-0 text-center mb-3 fw-bold">
                                Denomination
                            </div>
                        
                        
                        <div class="col-md-12 mb-3">
                            <span class="badge text-bg-secondary p-3 d-block fs-6">Total: <span class="totalAmount">0</span></span>
                        </div>

                        <div class="d-flex flex-wrap gap-1 flex-sm-column flex-lg-row justify-content-between">
                            <div class="col mb-2">
                                <div class="input-group input-group-sm ">
                                    <span class="input-group-text" id="inputOne">($) 1</span>
                                    <input type="number" class="form-control stackInput" data=1 name="stack_1" aria-label="1" aria-describedby="inputOne">
                                </div>
                            </div>
                            <div class="col mb-2">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text" id="inputFive">($) 5</span>
                                    <input type="number" class="form-control stackInput" data=5 name="stack_5" aria-label="5" aria-describedby="inputFive">
                                </div>
                            </div>
                            <div class="col mb-2">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text" id="inputTen">($) 10</span>
                                    <input type="number" class="form-control stackInput" data=10 name="stack_10" aria-label="10" aria-describedby="inputTen">
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap gap-2 flex-sm-column flex-lg-row justify-content-between">
                            <div class="col mb-2">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text" id="inputTwenty">($) 20</span>
                                    <input type="number" class="form-control stackInput" data=20 name="stack_20" aria-label="20" aria-describedby="inputTwenty">
                                </div>
                            </div>
                            <div class="col mb-2">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text" id="inputFifty">($) 50</span>
                                    <input type="number" class="form-control stackInput" data=50 name="stack_50" aria-label="50" aria-describedby="inputFifty">
                                </div>
                            </div>
                            <div class="col mb-3">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text" id="inputHundred">($) 100</span>
                                    <input type="number" class="form-control stackInput" data=100 name="stack_100" aria-label="100" aria-describedby="inputHundred">
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex flex-wrap gap-2 flex-col">
                            <div class="col w-100 mb-3">
                                <textarea id="" class="form-control" name="note" rows="5" cols="1" placeholder="Short Note"></textarea>
                            </div>
                            
                        </div>
                        <div class="col">
                            <button type="submit" class="btn btn-primary w-100">Add Entry</button>
                        </div>
                       

                        

                        <div class="row">
                            
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-8" style="padding-left: 0px;">
        <div class="card w-100 h-100">

            <div class="card-header bg-primary">
                Bank List
            </div>

            <div class="card-body">
                <table id="bankTable" class="table table-sm table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>($) 1</th>
                            <th>($) 5</th>
                            <th>($) 10</th>
                            <th>($) 20</th>
                            <th>($) 50</th>
                            <th>($) 100</th>
                            <th>($) Total</th></th>
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
        lengthChange: false,
        pageLength: 10,
    });

    $('.select2').select2({
        width: 'resolve',
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