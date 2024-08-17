<!DOCTYPE html>
<html lang="en">
    @include('admin.layouts.include.head')
    @stack('post_css')
    <body>

        <!-- loader Area Start -->
        <div id="preloader">
            <div id="status">
                <span class="loader"></span>
            </div>
        </div>
        <!-- loader Area End -->

        @include('admin.layouts.include.header')
        <div class="container-fluid wrapper">
            @yield('content')
        </div>

        @if( isset($install) )
        @else
            @include('admin.layouts.include.footer')
        @endif
        
        @include('admin.layouts.include.script')
        @stack('post_script')

        
<script>

    /*----------------------------
     Preloader
    ------------------------------ */
    $(window).on("load", function() {
      $("#status").fadeOut();
      $("#preloader")
        .delay(350)
        .fadeOut("slow");
    });

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
    // Toastr script end
</script>

    </body>
</html>