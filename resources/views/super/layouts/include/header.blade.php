<header>    
    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            <div class="row w-100">
                <div class="col-md-4 d-flex justify-content-between align-items-center">
                    <a class="navbar-brand fw-semibold" href="{{ route('super.dashboard') }}">
                        <img src="{{ asset(IMG_FAVICON_PATH) }}" alt="Logo" width="30" height="30" class="d-inline-block align-text-top">
                        Super Admin
                    </a>
                </div>

                <div class="col-md-4 d-flex justify-content-center align-items-center">
                    <!-- <div class="input-group input-group-sm">
                        <input type="text" class="form-control border-0 shadow-none" placeholder="Search keyword" aria-label="Recipient's username" aria-describedby="button-addon2">
                        <button class="btn bg-white text-dark border-0" type="button" id="button-addon2"><i class="bi bi-search"></i></button>
                    </div> -->
                </div>

                <div class="col-md-4 d-flex justify-content-end align-items-center">
                    <button class="btn text-light notification border-0 position-relative" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight">
                        <i class="bi bi-bell-fill"></i>
                        <span class="position-absolute count start-75 translate-middle badge rounded-pill bg-warning text-dark">
                            +
                            <span class="visually-hidden">unread messages</span>
                        </span>
                    </button>

                    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
                        <div class="offcanvas-header">
                            <h5 class="offcanvas-title" id="offcanvasRightLabel">Notification</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                        </div>
                        <div class="offcanvas-body">

                        </div>
                    </div>

                    <!-- Dropdown here -->
                    <div class="dropdown">
                        <button class="btn dropdown-toggle text-light border-0" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="{{ asset(LOGO) }}" alt="Profile Image" width="30" height="30" class="rounded-circle me-2">
                            {{Auth::user()->name}}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                            <li><a class="dropdown-item" href="#">Profile</a></li>
                            <li><a class="dropdown-item" href="#">Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('super.logout') }}">Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid sub-header shadow-sm">
        <a href="{{ route('super.admin.list') }}" class="btn btn-sm m-1 btn-primary sub-header-item"><i class="las la-user"></i> Admin</a>
        <a href="" class="btn btn-sm m-1 btn-primary sub-header-item"><i class="las la-cog"></i> Setting</a>
    </div>

</header>