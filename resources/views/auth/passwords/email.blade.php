@extends('layouts.app')

@section('content')
<div class="login-box">
    <div class="login-logo">
        <a href="../../index2.html"><b>{{ __('Reset Password') }}</a>
    </div>
    <!-- /.login-logo -->
    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg"></p>

            <form action="{{ route('password.email') }}" method="post">
                @method('post')
                @csrf
                <div class="input-group mb-3">
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                        placeholder="{{ __('Email Address') }}" value="{{ old('email') }}" required autocomplete="email" autofocus>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                </div>
                <div>
                    @error('email')
                    <p>{{$message}}</p>
                    @enderror
                </div>
                <div class="row">
                    <div class="col-8">
                        <button type="submit" class="btn btn-primary btn-block">{{ __('Send Password Reset Link')
                            }}</button>
                    </div>
                    <!-- /.col -->
                </div>
            </form>
        </div>
        <!-- /.login-card-body -->
    </div>
</div>
@endsection