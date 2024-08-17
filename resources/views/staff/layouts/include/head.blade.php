<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Staff</title>
    <meta name="description" content="description" /> 
    <meta name="keywords" content="Keywords" />
    <meta name="author" content="DZN Solutions" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <link rel="shortcut icon" href="{{ asset(IMG_FAVICON_PATH) }}" type="image/png">

    <!-- jQuery file  -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/uikit/3.0.2/js/uikit.min.js"></script>
    
    <!-- css file  -->
    <link rel="stylesheet" href="{{asset('assets/vendor/bootstrap/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/staff/styles.css')}}">
    
    <!-- bootstrap icon file -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap-font/bootstrap-icons.min.css') }}">


    <!-- Datatables.net -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/uikit/3.0.2/css/uikit.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/2.0.3/css/dataTables.uikit.css" rel="stylesheet">
    <link rel= "stylesheet" href= "https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css" >

    <!-- Toastr files -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/toastr/toastr.min.css') }}">

    <!-- Select2 CDN -->
    <link href="{{ asset('assets/vendor/select2/dist/css/select2.min.css') }}" rel="stylesheet" />

    <!-- Face API -->
    <script defer src="{{ asset('assets/vendor/face-api/face-api.min.js') }}"></script>
    
</head>
