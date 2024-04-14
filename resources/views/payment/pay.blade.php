<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{env('APP_NAME')}}</title>
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="{{asset('dist/css/adminlte.min.css')}}">
    <style>
        .cs_loader {
            position: fixed;
            left: 0;
            top: 0;
            right: 0;
            bottom: 0;
            z-index: 99999;
            background-color: rgba(0, 0, 0, 0.8);
            }
            .cs_loader img {

                width: 70px;
                height: 70px;
                position: absolute;
                left: 50%;
                top: 50%;
                transform: translate(-50%, -50%);
            }
            * {
            font-family: "Helvetica Neue", Helvetica;
            font-size: 15px;
            font-variant: normal;
            padding: 0;
            margin: 0;
            }

            html {
            height: 100%;
            }

            body {
            background: #E6EBF1;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100%;
            }

            form {
            width: 480px;
            margin: 20px 0;
            }

            .group {
            background: white;
            box-shadow: 0 7px 14px 0 rgba(49, 49, 93, 0.10), 0 3px 6px 0 rgba(0, 0, 0, 0.08);
            border-radius: 4px;
            margin-bottom: 20px;
            }

            label {
            position: relative;
            color: #8898AA;
            font-weight: 300;
            height: 40px;
            line-height: 40px;
            margin-left: 20px;
            display: flex;
            flex-direction: row;
            }

            .group label:not(:last-child) {
            border-bottom: 1px solid #F0F5FA;
            }

            label > span {
            width: 120px;
            text-align: right;
            margin-right: 30px;
            }

            .field {
            background: transparent;
            font-weight: 300;
            border: 0;
            color: #31325F;
            outline: none;
            flex: 1;
            padding-right: 10px;
            padding-left: 10px;
            cursor: text;
            }

            .field::-webkit-input-placeholder {
            color: #CFD7E0;
            }

            .field::-moz-placeholder {
            color: #CFD7E0;
            }

            button {
            float: left;
            display: block;
            background: #666EE8;
            color: white;
            box-shadow: 0 7px 14px 0 rgba(49, 49, 93, 0.10), 0 3px 6px 0 rgba(0, 0, 0, 0.08);
            border-radius: 4px;
            border: 0;
            margin-top: 20px;
            font-size: 15px;
            font-weight: 400;
            width: 100%;
            height: 40px;
            line-height: 38px;
            outline: none;
            }

            button:focus {
            background: #555ABF;
            }

            button:active {
            background: #43458B;
            }

            .outcome {
            float: left;
            width: 100%;
            padding-top: 8px;
            min-height: 24px;
            text-align: center;
            }

            .success,
            .error {
            /* display: none; */
            font-size: 13px;
            }

            .success.visible,
            .error.visible {
            display: inline;
            }

            .error {
            color: #E4584C;
            }

            .success {
            color: #666EE8;
            }

            .success .token {
            font-weight: 500;
            font-size: 13px;
            }

    </style>
    @stack('styles')
</head>
<body class="hold-transition login-page">
    <div class="cs_loader"  style="display:none;">
    <img src="{{asset('uploads/loadergif.gif')}}">
    </div>
    @include('message.success-message')
    @include('message.error-message')
    @include('message.status-message')
    <div class="container">
        <div class="row justify-content-center">
            <!-- <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Make a payment</div>
                    <div class="card-body"> -->
                        <form action="{{ route('pay') }}" method="POST" id="paymentForm">
                            @csrf
                            <div class="row mt-3">
                                <div class="col">
                                    @includeIf ('components.stripe-collapse',['user' => $user])
                                </div>
                            </div>
                            <div class="text-center">
                                <button type="submit" id="payButton" class="">Pay Now $100</button>
                            </div>
                            <div class="outcome">
                              <div class="error" id="cardErrors"></div>
                            </div>
                        </form>
                    <!-- </div>
                </div>
            </div>-->
        </div> 
    </div>
    @stack('scripts')
</body>
</html>