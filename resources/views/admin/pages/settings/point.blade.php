@extends('admin.layouts.master')
@section('title', isset($title) ? $title : 'Home')
@section('content')

<div class="row admin-section mb-2 h-100">

    <div class="col-md-2 h-100">
        <ul class="list-group">
            <li class="list-group-item">
                <a href="{{ route('admin.setting.general') }}">General</a>
            </li>
            <li class="list-group-item active" aria-current="true">
                <a href="{{ route('admin.setting.point') }}">Points</a>
            </li>
            <li class="list-group-item">
                <a href="{{ route('admin.setting.ticket') }}">Ticket</a>
            </li>
            <li class="list-group-item">Clients</li>
            <li class="list-group-item">Machines</li>
            <li class="list-group-item">Chats</li>
        </ul>
    </div>

    <div class="col-md-10 admin-right">
        <div class="card w-100">
            <div class="card-header bg-primary">Point Setting</div>
            <div class="card-body">
                <p class="card-text p-2">
                    Setting for player bonus point assign range, 
                    can set minimum and maximum range of bonus point
                    that can be assigned to player.
                </p>
                <form action="{{ route('admin.setting.point.store') }}" method="post">
                    @csrf
                    <div class="row">
    
                        <div class="col-md-4 mb-3">
                            <div class="form-floating">
                                <input type="number" class="form-control" name="min_bonus" value="{{ allsetting('min_bonus') }}" id="minInput" placeholder="Min Bonus Range">
                                <label for="minInput">Point Bonus Min</label>
                            </div>
                        </div>
    
                        <div class="col-md-4 mb-3">
                            <div class="form-floating">
                                <input type="number" class="form-control" name="max_bonus" value="{{ allsetting('max_bonus') }}" id="minInput" placeholder="Min Bonus Range">
                                <label for="minInput">Point Bonus Max</label>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="form-floating">
                                <input type="number" class="form-control" name="bonus_hour" value="{{ allsetting('bonus_hour') }}" id="bonusHourInput" placeholder="Bonus Hour">
                                <label for="bonusHourInput">Bonus After Hour</label>
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