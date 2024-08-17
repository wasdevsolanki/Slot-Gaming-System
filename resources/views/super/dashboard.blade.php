@extends('super.layouts.master')
@section('title', isset($title) ? $title : 'Home')
@section('content')

<!-- Start Super Admin List -->
<div class="row p-5 text-center">
    <h3 class="mb-4">Welcome, Super Dashboard</h3>
    <div class="col-md-3">
        <div class="card text-bg-light">
            <div class="card-body text-center p-5">
                <span class="display-4">ADMIN</span>
            </div>
            <div class="card-footer">
                <span class="badge bg-primary p-3 w-100 fs-6">{{ $admin }}</span>
            </div>
        </div>
    </div>
</div>

@endsection 