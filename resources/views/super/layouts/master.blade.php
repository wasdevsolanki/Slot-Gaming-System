<!DOCTYPE html>
<html lang="en">
    @include('super.layouts.include.head')
    @stack('post_css')
    <body>
        @include('super.layouts.include.header')
        <div class="container-fluid main-content">
            @yield('content')
        </div>

        @include('super.layouts.include.footer')
        @include('super.layouts.include.script')
        @stack('post_script')


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
<!-- Toastr script end -->


    </body>
</html>