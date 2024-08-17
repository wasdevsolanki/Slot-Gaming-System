<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Login</title>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap-font/bootstrap-icons.min.css') }}">

        
        <!-- Toastr files -->
        <link rel="stylesheet" href="{{ asset('assets/vendor/toastr/toastr.min.css') }}">

        <style>
            body {
                background-color: #5F80F8;
            }
            .login-container {
                height: 90vh;
            }
            .login-container .card {
                background: none;
                border: none;
            }
            .login-container .card .login-btn {
                display: flex;
                justify-content: center;
                align-items: center;
            }
            .login-container .card .login-btn button {
                background-color: black;
                border: none;
                color: white;
                width: 40%;
                padding: 10px;
            }
            .form-control:focus {
                box-shadow: none;
                outline: none;
            }
            .login-container .card .login-pin a {
                color: white;
                text-decoration: none;
                font-size: 17px;
            }
            .login-option {
                display: flex;
                justify-content: center;
                align-items: center;
            }
            .login-option .nav-link {
                color: white;
            }
            .login-option .nav-link.active {
                background-color: white;
                color: black;
            }
            canvas {
                position: absolute;
            }

        </style>

    </head>
    <body>


    <div class="container login-container">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="company-logo p-3 d-flex justify-content-center mb-3">
                            <img src="{{ asset(LOGO) }}" alt="" width="80" height="80">
                        </div>
                        <form action="{{ route('super.login.post') }}" method="POST">
                            @csrf
                            <div class="form-floating mb-4" >
                                <input type="email" class="form-control" name="email" id="InputEmail" placeholder="name@example.com" autocomplete="off">
                                <label for="InputEmail">Email</label>
                            </div>
                            <div class="input-group mb-4">
                                <div class="form-floating">
                                    <input type="password" class="form-control border-0" name="password" id="InputPassword" placeholder="Password" autocomplete="off">
                                    <label for="InputPassword">Password</label>
                                </div>
                            </div>
                            <div class="login-btn">
                                <button type="submit" class="btn btn-lg">Login</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>


    </div>

    <script src="{{asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>

    <!-- Toastr files -->
    <script src="{{ asset('assets/vendor/toastr/toastr.min.js') }}"></script>
    <script>
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": true,
            "progressBar": true,
            "positionClass": "toast-bottom-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }

        @if ($errors->any())
        toastr.error("@foreach ($errors->all() as $error) {{ $error}} </br> @endforeach");
        @endif

        @if (Session::has('success'))
        toastr.success("{{ session('success') }}");
        @endif
        @if (Session::has('error'))
        toastr.error("{{ session('error') }}");
        @endif
        @if (Session::has('info'))
        toastr.info("{{ session('info') }}");
        @endif
        @if (Session::has('warning'))
        toastr.warning("{{ session('warning') }}");
        @endif
    </script>
    <!-- Toastr end -->

  </body>
</html>