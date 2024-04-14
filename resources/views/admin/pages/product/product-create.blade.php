@extends('admin.layouts.master')

@section('content')

@php($sizes = ["small","medium","large"])
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Create New Product</h3>
    </div>
    <div class="card-body ">
        <form name="product-form" action="{{route('admin.product.store')}}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">Product Name :</label>
                        <input type="name" class="form-control" name="name" placeholder="Enter name" value="{{old('name') ?? ''}}">
                        @error('name')<p>{{$message}}</p>@enderror
                    </div>
                    <div class="form-group">
                        <label for="description">Product Description :</label>
                        <textarea name="description" id="description" class="form-control" placeholder="Enter description">{{old('description') ?? ''}}</textarea>
                        @error('description')<p>{{$message}}</p>@enderror
                    </div>
                    <div class="form-group">
                        <label for="quantity">Product Quantity :</label>
                        <input type="number" class="form-control" name="quantity" placeholder="Enter quantity" value="{{old('quantity') ?? ''}}" />
                        @error('quantity')<p>{{$message}}</p>@enderror
                    </div>
                    <div class="form-group">
                        <label for="size">Size</label>
                        <select name="size" class="form-control custom-select">
                            <option selected="" disabled="">Select one</option>
                            @foreach($sizes as $size)
                              <option value="{{$size}}" @if(old('size') == $size){{'selected'}}@endif>{{ucfirst($size)}}</option>
                            @endforeach
                        </select>
                        @error('size')<p>{{$message}}</p>@enderror
                    </div>
                    <div class="form-group">
                        <label>Categories</label>
                        <select class="select2" multiple="multiple" data-placeholder="Select a Category" style="width: 100%;" name="categories">
                            @if($categories->count())
                             @foreach($categories as $category)
                               <option value="{{$category->id}}">{{$category->name}}</option>
                             @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="images">File input</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="images" name="images[]" multiple>
                                <label class="custom-file-label" for="images">Choose file</label>
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
       Create New Product
    </div>
    <!-- /.card-footer-->
</div>
@endSection

@section('js')
<script src="{{asset('admin/validation.js')}}"></script>
@endSection