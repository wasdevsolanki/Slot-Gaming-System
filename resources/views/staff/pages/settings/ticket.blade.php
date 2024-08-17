@extends('staff.layouts.master')
@section('title', isset($title) ? $title : 'Home')
@section('content')

<div class="row admin-section mb-2 h-100">

    <div class="col-md-2 h-100">
        <ul class="list-group">
            <li class="list-group-item">
                <a href="{{ route('staff.setting.general') }}">General</a>
            </li>
            <li class="list-group-item">
                <a href="{{ route('staff.setting.point') }}">Points</a>
            </li>
            <li class="list-group-item active" aria-current="true">
                <a href="{{ route('staff.setting.ticket') }}">Ticket</a>
            </li>
            <li class="list-group-item">Clients</li>
            <li class="list-group-item">Machines</li>
            <li class="list-group-item">Chats</li>
        </ul>
    </div>

    <div class="col-md-10 admin-right">
        <div class="card w-100">
            <div class="card-header bg-primary">Ticket Setting</div>
            <div class="card-body">
                <p class="card-text p-2">
                    Setting for player tikcet assign range, 
                    can set minimum and maximum range of ticket
                    that can be assigned to player.
                </p>
                <form action="{{ route('staff.setting.ticket.store') }}" method="post">
                    @csrf
                    <div class="row">
    
                        <div class="col-md-4 mb-3">
                            <div class="form-floating">
                                <input type="number" class="form-control" name="times_ticket" value="{{ allsetting('times_ticket') }}" id="minInput" placeholder="Times Ticket Range">
                                <label for="minInput">Ticket Times Point * 3</label>
                            </div>
                        </div>
    
                        <div class="col-md-4 mb-3">
                            <div class="form-floating">
                                <input type="number" class="form-control" name="max_ticket" value="{{ allsetting('max_ticket') }}" id="minInput" placeholder="Min Ticket Range">
                                <label for="minInput">Ticket Max</label>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3 border rounded d-flex align-items-center">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="repoint" value="1" id="repointInput" {{ allsetting('repoint') ? 'checked' : '' }}>
                                <label class="form-check-label" for="repointInput">
                                    Repoint to Player
                                </label>
                            </div>
                        </div>
    
                        <div class="col-md-12 mb-3">
                            <button type="submit" class="btn btn-lg btn-primary">Save Setting</button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

@endsection