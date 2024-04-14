@extends('admin.layouts.master')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit Vendor</h3>
    </div>
    <div class="card-body ">
        <form action="{{route('admin.vendors.update',[$user->id])}}" method="post">
            @csrf
            @method('patch')
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">Name :</label>
                        <input type="name" class="form-control" name="name" placeholder="Enter name" value="{{old('name') ?? $user->name}}">
                        @error('name')<p>{{$message}}</p>@enderror
                    </div>
                    <div class="form-group">
                        <label for="email">Email :</label>
                        <input type="email" class="form-control" name="email" placeholder="Enter email" value="{{old('email') ?? $user->email}}">
                        @error('email')<p>{{$message}}</p>@enderror
                    </div>
                    <div class="form-group">
                        <label for="password">Password :</label>
                        <input type="password" class="form-control" name="password" placeholder="Enter password" value="{{old('password') ?? ''}}">
                        @error('password')<p>{{$message}}</p>@enderror
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">Confirm Password :</label>
                        <input type="password" class="form-control" name="password_confirmation" placeholder="Enter confirm password">
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="active" name="active" value="1" @if(old('is_active') =='1' || $user->is_active =='1'){{'checked'}}@endif> 
                        <label class="custom-control-label" for="active">Active/Inactive</label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </div>
        </form>
    </div>
    <!-- /.card-body -->
    <div class="card-footer">
       Edit Vendor
    </div>
    <!-- /.card-footer-->
</div>
@endSection