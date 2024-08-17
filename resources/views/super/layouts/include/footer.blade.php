<footer>
    <div class="container-fluid border">
        <div class="row footer-body">
            <div class="col-md-12 footer-content">
                <div>
                    <span class="fw-semibold">Super Admin <i class="las la-angle-right"></i></span>
                    <span>Welcome, {{ Auth::user()->name }}</span>
                </div>
                <a href="" class="btn text-light">TERMINATE SESSION</a>
            </div>
        </div>
    </div>
</footer>