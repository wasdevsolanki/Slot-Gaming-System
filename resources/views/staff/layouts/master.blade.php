<!DOCTYPE html>
<html lang="en">
    @include('staff.layouts.include.head')
    @stack('post_css')
    <body>
        <!-- loader Area Start -->
        <div id="preloader">
            <div id="status">
                <span class="loader"></span>
            </div>
        </div>
        <!-- loader Area End -->

        @include('staff.layouts.include.header')
        <div class="container-fluid wrapper">
            @yield('content')
        </div>

        @if( isset($install) )
        @else
            @include('staff.layouts.include.footer')
        @endif
        
        @include('staff.layouts.include.script')
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

    $('#staffCheckinForm').submit(function(event) {
        event.preventDefault();

        let now = new Date();
        let year = now.getFullYear();
        let month = String(now.getMonth() + 1).padStart(2, '0');
        let date = String(now.getDate()).padStart(2, '0');
        let hours = String(now.getHours()).padStart(2, '0');
        let minutes = String(now.getMinutes()).padStart(2, '0');
        let seconds = String(now.getSeconds()).padStart(2, '0');

        let formattedDateTime = `${year}-${month}-${date} ${hours}:${minutes}:${seconds}`;
        $('#staffCheckin').val(formattedDateTime);

        $(this).unbind('submit').submit();
    });

</script>

    </body>
</html>