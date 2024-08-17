<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rooms</title>
    <link rel="stylesheet" href="{{asset('assets/vendor/bootstrap/css/bootstrap.min.css')}}">

    <style>

        :root {
            --primary-color: #5F80F8;
            --grey-color: #373737;
            --red-color: #DE1E29;
            --light-color: #F2F4F6;
            --white-color: #FFFFFF;
            --black-color: #000000;
            --green-color: #7F9F80;
        }
        .btn {
            border-radius: 0;
            border-color: var(--primary-color);
        }
        .btn.room-btn {
            padding: 25px 20px 25px 20px;
        }
        .btn-primary {
            background-color: var(--primary-color);
            color: var(--light-color);
        }
        .bg-primary {
            background-color: var(--primary-color) !important;
            color: var(--light-color) !important;
        }
        .card {
            border-radius: 0 !important;
        }
        .card-header {
            border-radius: 0 !important;
        }
        .text-primary {
            color: var(--primary-color);
        } 

        .room-container {
            height: 99vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
    </style>
</head>
<body>
    
    <div class="container-fluid border">
        <div class="container">
            <div class="row d-flex justify-content-center align-items-center">
                <div class="col-md-6 room-container">
                    <div class="card w-100">
                        <div class="card-header bg-primary">Select Gameroom</div>
                        <div class="card-body">

                            @foreach ($rooms as $item)
                            <a href="{{ route('admin.room', encrypt($item->id)) }}" class="btn room-btn btn-primary btn-lg">{{ $item->name }}</a>
                            @endforeach

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
</body>
</html>