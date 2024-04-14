@extends('admin.layouts.master')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Create New Vendor</h3>
    </div>
    <div class="card-body ">
        <form action="{{route('admin.vendors.store')}}" method="post">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">Name :</label>
                        <input type="name" class="form-control" name="name" placeholder="Enter name" value="{{old('name') ?? ''}}">
                        @error('name')<p>{{$message}}</p>@enderror
                    </div>
                    <div class="form-group">
                        <label for="email">Email :</label>
                        <input type="email" class="form-control" name="email" placeholder="Enter email" value="{{old('email') ?? ''}}">
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
                        <input type="checkbox" class="custom-control-input" id="active" name="active"> 
                        <label class="custom-control-label" for="active">Active/Inactive</label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </form>
    </div>
    <!-- /.card-body -->
    <div class="card-footer">
       Create New Vendor
    </div>
    <!-- /.card-footer-->
</div>
@endSection