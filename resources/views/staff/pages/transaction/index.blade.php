@extends('staff.layouts.master')
@section('title', isset($title) ? $title : 'Home')
@section('content')

<div class="row admin-section h-100">

    <div class="col-md-3 h-100 d-flex">
        <div class="card w-100">
            <div class="card-header bg-primary">Add Transaction</div>
            <div class="card-body">
                <form action="{{ route('staff.transaction.store') }}" method="post">
                    @csrf
                    
                    <div class="row">

                        <div class="col-md-12 mb-4 d-flex justify-content-between align-items-center">
                            <span class="badge text-bg-success p-2 d-block fs-6 fw-semibold">
                                Starting Balance:
                                <span class="">{{ staffBalance() ? staffBalance()->total : 0 }}</span>
                            </span>
                            <span class="badge text-bg-warning p-2 d-block fs-6 fw-semibold">
                                Remains:
                                <span class="">{{ remainBalance() }}</span>
                            </span>
                        </div>

                        <div class="col-md-12 mb-4 d-flex justify-content-start">
                            <ul class="list-group w-100">
                                <li class="list-group-item">
                                    <input class="form-check-input me-1" type="checkbox" value="" id="inputTransfer">
                                    <label class="form-check-label" for="inputTransfer">Transfer</label>
                                </li>
                            </ul>
                        </div>

                        <div class="col-md-12 mb-3 isStaff d-none">
                            <select class="form-control select2" name="reciever_id" style="width:100%">
                                <option value="" selected>Select an option</option>
                                @foreach( $staff as $item )
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="inputAmount" class="form-label">Expense Amount</label>
                            <input type="number" class="form-control" id="inputAmount" name="amount" placeholder="Enter Expense Amount">
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="inputNote" class="form-label">Expense Note</label>
                            <textarea id="" class="form-control" id="inputNote" name="note" placeholder="Short Note"></textarea>
                        </div>

                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary w-100">Save Transaction</button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-9 admin-right h-100">
        <div class="card w-100">

            <div class="card-header bg-primary">
                Transaction List
            </div>

            <div class="card-body">
                <table id="transactionTable" class="table table-sm table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th class="text-center">Staff</th>
                            <th class="text-center">Transaction</th>
                            <th class="text-center">Amount</th>
                            <th>Note</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach( $transactions as $t )
                            <tr>
                                <td>{{ Auth::user()->name }}</td>
                                <td class="text-center">
                                    @if( $t['transfer'] && ! is_null($t['transfer']) )
                                        @if(! isset($t['transfer_by']) )
                                        <span class="badge text-bg-secondary p-2">{{ $t['transfer']['name'] }}</span>
                                        @else
                                        <span class="badge text-bg-secondary p-2">{{ $t['transfer_by']['name'] }}</span>
                                        @endif
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if( $t['type'] == 'bank' )
                                    <span class="badge text-bg-success rounded-pill p-2 w-50">BANK <i class="las la-arrow-down"></i></span>
                                    @elseif( $t['type'] == 'transfer' && ! isset($t['transfer_by']) )
                                    <span class="badge text-bg-warning rounded-pill p-2 w-50">TRANSFER <i class="las la-arrow-up"></i></span>
                                    @elseif( $t['type'] == 'expense' && ! isset($t['transfer_by']) )
                                    <span class="badge text-bg-danger rounded-pill p-2 w-50">EXPENSE <i class="las la-arrow-up"></i></span>
                                    @else
                                    <span class="badge text-bg-info rounded-pill p-2 w-50">Recieve <i class="las la-arrow-down"></i></span>
                                    @endif
                                </td>
                                <td class="text-center fw-semibold">{{ $t['amount'] }}</td>
                                <td>{{ $t['note'] }}</td>
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

    $('#transactionTable').DataTable({
        responsive: true,
        lengthChange: false
    });

    
    $('#inputTransfer').change(function() {
        if(this.checked) {
            $('.isStaff').removeClass('d-none');
        } else {
            $('.isStaff').addClass('d-none');
        }
    });

    $('.select2').select2({
        width: 'resolve',
        placeholder: 'Select an option',
    });

});
</script>
@endpush
@endsection