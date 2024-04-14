@extends('backend.admin-master')
@section('style')
   
@endsection
@section('site-title')
    {{__('Edit Profile')}}
@endsection
@section('content')
    <div class="main-content-inner margin-top-30">
        <div class="row">
            <div class="col-lg-12">
                @include('backend.partials.message')
                <div class="card">
                    <div class="card-body">
                        @include('backend.partials.error')
                        <form action="{{route('admin.profile.update')}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label for="username">{{__('Username')}}</label>
                                <input type="text" class="form-control" readonly value="{{auth()->user()->username}} ">
                            </div>
                            <div class="form-group">
                                <label for="name">{{__('Name')}}</label>
                                <input type="text" class="form-control" id="name" name="name"
                                       value="{{auth()->user()->name}}">
                            </div>
                            <div class="form-group">
                                <label for="email">{{__('Email')}}</label>
                                <input type="email" class="form-control" id="email" name="email"
                                       value="{{auth()->user()->email}} ">
                            </div>
                            
                              <div class="form-group">
                                <label for="email">{{__('Designation')}}</label>
                                <input type="text" class="form-control" id="designation" name="designation"
                                       value="{{auth()->user()->designation}} ">
                            </div>

                            <div class="form-group">
                                <label for="email">{{__('Description')}}</label>

                                <textarea class="form-control" name="description" cols="5">{{ auth()->user()->description }}</textarea>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">{{__('Save changes')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<x-media.markup/>
@endsection
@section('script')
@endsection