<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{env('APP_NAME')}}</title>
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="{{asset('dist/css/adminlte.min.css')}}">
</head>
<body class="hold-transition login-page">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                     @if(request()->has('message'))
                       {{request()->get('message')}}
                     @else
                     Thanks for your payment.
                     @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>