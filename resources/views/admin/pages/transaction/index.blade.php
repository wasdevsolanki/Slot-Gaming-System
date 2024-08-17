@extends('admin.layouts.master')
@section('title', isset($title) ? $title : 'Home')
@section('content')

<div class="row admin-section h-100">

    <div class="col-md-12 h-100">
        <div class="card w-100 h-100">

            <div class="card-header bg-primary">
                Transaction List
            </div>

            <div class="card-body">
                <table id="transactionTable" class="table table-sm table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Total</th></th>
                            <th>Note</th>
                        </tr>
                    </thead>
                    <tbody>

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


});
</script>
@endpush
@endsection