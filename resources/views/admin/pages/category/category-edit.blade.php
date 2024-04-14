@extends('admin.layouts.master')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit Category</h3>
    </div>
    <div class="card-body ">
        <form action="{{route('admin.category.update',[$category->id])}}" method="post" enctype="multipart/form-data">
            @csrf
            @method('patch')
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">Category Name :</label>
                        <input type="name" class="form-control" name="name" placeholder="Enter name" value="{{old('name') ?? $category->name}}">
                        @error('name')<p>{{$message}}</p>@enderror
                    </div>
                    <div class="form-group">
                        <label for="description">Category Description :</label>
                        <textarea name="description" id="description" class="form-control" placeholder="Enter description">{{old('description') ?? $category->description}}</textarea>
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
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
                <div class="col-md-6">
                    <img src="{{url($category->image)}}" width="250px"/>
                </div>
            </div>
        </form>
    </div>
    <!-- /.card-body -->
    <div class="card-footer">
       Edit Category
    </div>
    <!-- /.card-footer-->
</div>
@endSection