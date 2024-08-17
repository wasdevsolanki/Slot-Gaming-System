<header>    
    <nav class="navbar navbar-dark ">
        <div class="container-fluid">
            <div class="row w-100 d-flex justify-content-between align-items-center">
                
                <div class="col-md-4 d-flex justify-content-between align-items-center">
                    <a class="navbar-brand fw-semibold" href="{{ route('admin.dashboard') }}">
                        <img src="{{ asset(IMG_FAVICON_PATH) }}" alt="Logo" width="30" height="30" class="d-inline-block align-text-top">
                        Game Station
                    </a>
                </div>

                <div class="col-md-8 d-flex justify-content-end align-items-center">
                    @if( isset($install) )
                    @else

                    @if( isset($install) )
                    @else
                    <div class="nav-button mx-1">
                        <button type="button" class="btn btn-primary btn-sm text-light" data-bs-toggle="modal" data-bs-target="#addPlayerModal">
                            Add Clients
                        </button>
                    </div>
                    @endif

                    <!-- Gaming Room here -->
                    <div class="dropdown room">
                        <button class="btn btn-sm btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ room()->name }}
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">

                            @foreach( getAllRoom() as $item )
                            <li><a class="dropdown-item" href="{{ route('admin.room', encrypt($item->id) ) }}">{{ $item->name }}</a></li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- <a href="#" class="facelock playerFaceModel" data-bs-toggle="modal" data-bs-target="#faceLockModal">
                        <i class="bi bi-person-bounding-box"></i>
                    </a> -->

                    <a href="{{ route('admin.point.image') }}" class="facelock">
                        <i class="bi bi-person-bounding-box"></i>
                    </a>

                    <button class="btn text-light notification border-0 position-relative" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight">
                        <i class="bi bi-bell-fill"></i>
                        @if( newPlayers()->count() )
                        <span class="position-absolute count start-75 translate-middle badge rounded-pill bg-warning text-dark">
                            {{ newPlayers()->count() }}
                            <span class="visually-hidden">unread messages</span>
                        </span>
                        @endif
                    </button>

                    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
                        <div class="offcanvas-header">
                            <h5 class="offcanvas-title" id="offcanvasRightLabel">Notification</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                        </div>
                        <div class="offcanvas-body">

                            <ul class="list-group">
                                @foreach (newPlayers() as $item)
                                <li class="list-group-item">
                                    <div class="row">
                                        <div class="col-12 d-flex justify-content-between align-items-center">
                                            <strong>{{ $item->name }}</strong>
                                            <small>{{ $item->created_at->diffForHumans() }}</small>
                                        </div>
                                        <div class="col-12 p-2">
                                            <span class="badge text-bg-secondary rounded-pill">Bonus: {{ $item->bonus }}</span>
                                        </div>
                                        <a href="{{ route('admin.player.status', encrypt($item->id)) }}" class="btn btn-sm text-bg-primary">Approve</a>
                                    </div>
                                </li>
                                @endforeach
                            </ul>

                        </div>
                    </div>
                    @endif

                    <!-- Profile Dropdown -->
                    <div class="dropdown">
                        <button class="btn dropdown-toggle text-light border-0" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="{{ asset(LOGO) }}" alt="Profile Image" width="30" height="30" class="rounded-circle me-2">
                            {{ admin() ? admin()->name : 'ADMIN' }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                            @if( isset($install) )
                            @else
                            <!-- <li><a class="dropdown-item" href="#">Profile</a></li>
                            <li><a class="dropdown-item" href="#">Settings</a></li> -->
                            {{-- <li><hr class="dropdown-divider"></li> --}}
                            @endif
                            <li><a class="dropdown-item " href="{{ route('admin.logout') }}">Logout</a></li>
                        </ul>
                    </div>


                </div>
            </div>
        </div>
    </nav>
    @if( isset($install) )
    @else
    <div class="container-fluid sub-header">
        <a href="{{ route('admin.dashboard')}}" class="btn btn-sm m-1 sub-header-item "><i class="las la-home"></i> Home</a>
        <a href="{{ route('admin.player.list')}}" class="btn btn-sm m-1 sub-header-item"><i class="las la-user"></i> Clients</a>
        <a href="{{ route('admin.machine.list') }}" class="btn btn-sm m-1 sub-header-item"><i class="las la-gamepad"></i> Machine</a>
        <a href="{{ route('admin.ticket.list') }}" class="btn btn-sm m-1 sub-header-item"><i class="las la-user-tie"></i> Ticket</a>
        <a href="{{ route('admin.reading.list') }}" class="btn btn-sm m-1 sub-header-item"><i class="las la-table"></i> Reading</a>
        <!-- <a href="#" class="btn btn-sm m-1 btn-primary sub-header-item"><i class="las la-sms"></i> Chat</a> -->
        <!-- <a href="#" class="btn btn-sm m-1 btn-primary sub-header-item"><i class="las la-user-tie"></i> Raffle</a> -->
        <a href="{{ route('admin.bank.list') }}" class="btn btn-sm m-1 sub-header-item"><i class="las la-wallet"></i> Bank</a>
        <a href="{{ route('admin.transaction.list') }}" class="btn btn-sm m-1 sub-header-item"><i class="lab la-buffer"></i> Transaction</a>
        <a href="{{ route('admin.staff.list') }}" class="btn btn-sm m-1 sub-header-item"><i class="las la-user-tie"></i> Staff</a>
        <a href="{{ route('admin.report.list') }}" class="btn btn-sm m-1 sub-header-item"><i class="las la-file-alt"></i> Reports</a>
        <a href="{{ route('admin.payroll.list') }}" class="btn btn-sm m-1 sub-header-item"><i class="las la-coins"></i> Payroll</a>
        <a href="{{ route('admin.setting.general') }}" class="btn btn-sm m-1 sub-header-item"><i class="las la-cog"></i> Setting</a>
        <!-- <a href="/main" target="_blank" class="btn btn-sm m-1 sub-header-item"><i class="las la-cog"></i> Window</a> -->
    </div>
    @endif

</header>

<!-- Modal Player -->
<div class="modal fade" id="addPlayerModal" tabindex="-1" aria-labelledby="addPlayerModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

        <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">Add new Player</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <form action="{{ route('admin.player.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="ref_id" id="refId" value="">
            <div class="modal-body">
                <div class="row">

                    <div class="col-md-12 mb-4">
                        <span class="fs-5 fw-semibold mb-0">Player Information</span><br>
                        <span>Please enter below information to register new player</span>
                    </div>

                    <div class="col-md-6 mb-3">
                        <input type="text" class="form-control" name="name" placeholder="Name">
                    </div>

                    <div class="col-md-6 mb-3">
                        <input type="email" class="form-control" name="email" placeholder="Email">
                    </div>

                    <div class="col-md-6 mb-3">
                        <input type="number" class="form-control" name="phone" placeholder="Phone">
                    </div>

                    <div class="col-md-6 mb-3">
                        <select name="gender" class="form-select">
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <input type="text" class="form-control" name="driving_license" placeholder="Driving License Number">
                    </div>

                    <div class="col-md-6 mb-3">
                        <input type="text" class="form-control selectReference" name="reference" placeholder="Reference">
                        <ul class="list-group userList">

                        </ul>
                    </div>

                    <div class="col-md-12 p-3">
                        <div class="divider"></div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label mb-1" for="inputDOB">Date of Birth</label>
                        <input type="date" class="form-control" id="inputDOB" name="dob" placeholder="Date of Birth">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label mb-1" for="inputSSN">Social Security Number</label>
                        <input type="text" class="form-control" id="inputSSN" name="ssn" placeholder="Social Security Number">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label mb-1" for="inputDocument">Personal Document</label>
                        <input type="file" class="form-control" id="inputDocument" name="document">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label mb-1" for="inputBonus">Bonus Amount  
                            <span class="badge text-bg-secondary"> MIN : {{ allsetting('min_bonus') }}</span>  
                            <span class="badge text-bg-secondary"> MAX : {{ allsetting('max_bonus') }}</span>  
                        </label>
                        <input type="number" class="form-control" id="inputbonus" name="bonus" placeholder="Enter Bonus Amount">
                    </div>

                </div>
            </div>

            <div class="modal-footer p-3">
                <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-success btn-sm">Save</button>
            </div>
        </form>

    </div>
  </div>
</div>

<!-- Modal Point -->
<div class="modal fade" id="addPointModal" tabindex="-1" aria-labelledby="addPointModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

        <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">Add up points</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
            <div class="row">
                
                <div class="col-md-6 mb-4">
                    <span class="fs-5 fw-semibold mb-0">Player Point</span><br>
                    <span>Entries of Point for Player</span>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="form-check form-check-reverse">
                        <input class="form-check-input shadow-none" type="checkbox" value="" id="RequestPoint">
                        <label class="form-check-label" for="RequestPoint">
                            Request Points
                        </label>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="input-group mb-3 border">
                        <div class="form-floating">
                            <input type="text" class="form-control shadow-none border-0 searchPlayer" name="user" id="InputUserHeader" placeholder="" autocomplete="off">
                            <label for="InputUserHeader">Enter User ID, Name FACE ID</label>
                        </div>
                        <button type="button" class="input-group-text bg-white playerFaceModel" data-bs-toggle="modal" data-bs-target="#faceLockModal">
                            <i class="bi bi-person-bounding-box fs-3 text-secondary"></i>
                        </button>
                    </div>
                    <ul class="list-group playerList">
                        <!-- Populated playerlist -->
                    </ul>

                </div>

                <div class="col-md-6">
                    <div class="form-floating mb-3">
                        <input type="number" class="form-control shadow-none" id="BonusHeaderInput" value="35" readonly>
                        <label for="BonusHeaderInput">Bonus Value</label>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-floating mb-3">
                        <input type="number" class="form-control shadow-none" id="AddPointHeaderInput">
                        <label for="AddPointHeaderInput">Add up Points</label>
                    </div>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Add Point</button>
            </div>

        </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="faceLockModal" tabindex="-1" aria-labelledby="faceLockModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">

        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="faceLockModalLabel">
                    <i class="bi bi-person-bounding-box fs-3 text-secondary"></i>
                    Face Lock
                </h5>
                <button type="button" class="btn-close playerFaceClose" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('admin.point.store') }}" method="POST">
                @csrf
                <input type="hidden" name="player_id" value="" id="playerInput">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-body">
                                    <video id="video" autoplay>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row d-flex">
                                        <div class="col-md-12 mb-3">
                                            <label for="FaceInputName" class="form-label">Player Name</label>
                                            <input type="text" id="FaceInputName" class="form-control" name="player_name" value="" placeholder="Enter Player Name" readonly>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="FaceInputBonus" class="form-label">Player Bonus</label>
                                            <input type="text" id="FaceInputBonus" class="form-control" name="player_bonus" value="" placeholder="Player Bonus" readonly>
                                        </div>

                                        <div class="col-md-6 mb-4">
                                            <label for="FaceInputPoint" class="form-label">Add up Points</label>
                                            <input type="text" id="FaceInputPoint" class="form-control" name="amount" value="" placeholder="Enter Player Points">
                                        </div>

                                        <div class="col-md-12 mb-4">
                                            <div class="divider"></div>
                                        </div>

                                        <div class="col-md-12 mb-3">
                                            <div class="form-check form-check-reverse">
                                                <input class="form-check-input shadow-none" type="checkbox" value="" id="RequestFacePoint">
                                                <label class="form-check-label" for="RequestFacePoint">
                                                    Request Points
                                                </label>
                                            </div>
                                        </div>

                                        <div class="col-md-12 mb-3" id="pointsInputField" style="display: none;">
                                            <!-- Input field will be added here -->
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-sm playerFaceClose" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary btn-sm">Add Points</button>
                </div>
            </form>

        </div>

    </div>
</div>
<!-- Modal -->