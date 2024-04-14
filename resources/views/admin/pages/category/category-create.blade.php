@extends('admin.layouts.master')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Create New Category</h3>
    </div>
    <div class="card-body ">
        <form name="category-form" action="{{route('admin.category.store')}}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">Category Name :</label>
                        <input type="text" class="form-control" name="name" placeholder="Enter name" value="{{old('name') ?? ''}}">
                        @error('name')<p>{{$message}}</p>@enderror
                    </div>
                    <div class="form-group">
                        <label for="description">Category Description :</label>
                        <textarea name="description" id="description" class="form-control" placeholder="Enter description">{{old('description') ?? ''}}</textarea>
                        @error('description')<p>{{$message}}</p>@enderror
                    </div>
                    <div class="form-group">
                        <label for="image">File input</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="image" name="image">
                                <label class="custom-file-label" for="image">Choose file</label>
                            </div>
                            <div class="input-group-append">
                               <span class="input-group-text">Upload</span>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </form>
    </div>
    <!-- /.card-body -->
    <div class="card-footer">
       Create New Category
    </div>
    <!-- /.card-footer-->
</div>
@endSection
@section('js')
<script src="{{asset('admin/validation.js')}}"></script>
@endSection