@extends('admin.layouts.master')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Reset Password</h3>
    </div>
    <div class="card-body ">
        <form action="{{route('admin.settings.reset-password')}}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="current_password">Current  Password:</label>
                        <input type="password" class="form-control" name="current_password" placeholder="Enter current password" value="{{old('current_password') ?? ''}}">
                        @error('current_password')<p>{{$message}}</p>@enderror
                    </div>
                    <div class="form-group">
                        <label for="new_password">New Password:</label>
                        <input type="password" class="form-control" name="new_password" placeholder="Enter new password" value="{{old('new_password') ?? ''}}">
                        @error('new_password')<p>{{$message}}</p>@enderror
                    </div>
                    <div class="form-group">
                        <label for="confirm_new_password">Confirm New Password:</label>
                        <input type="password" class="form-control" name="confirm_new_password" placeholder="Enter new password" value="{{old('confirm_new_password') ?? ''}}">
                        @error('confirm_new_password')<p>{{$message}}</p>@enderror
                    </div>
                    <button type="submit" class="btn btn-primary">Change Password</button>
                </div>
            </div>
        </form>
    </div>
    <!-- /.card-body -->
    <div class="card-footer">
    Reset Password
    </div>
    <!-- /.card-footer-->
</div>
@endSection