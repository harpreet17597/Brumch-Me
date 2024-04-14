@extends('admin.layouts.master')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Product Images List</h3>
    </div>
    <div class="card-body">
    <table id="example2" class="table table-bordered table-hover">
                  <thead>
                  <tr>
                    <th>ID</th>
                    <th>Product Name</th>
                    <th>Image</th>
                    <th>Action</th>
                  </tr>
                  </thead>
                  <tbody>
                    @if($product)
                     @foreach($product->images as $image)
                      <tr>
                      <td>{{$loop->index+1}}</td>
                      <td>{{$product->name}}</td>
                      <td><img src="{{url($image->image)}}" alt="" width="100"></td>
                      <td>
                            <form action="{{route('admin.product.images.destroy',[$product->id,$image->id])}}" style="display:inline" method="post">
                                  @csrf
                                  @method('delete')
                                  <button class="btn btn-sm btn-danger">delete</button>
                            </form>
                      </td>
                      </tr>
                     @endforeach
                    @endif
                  </tbody>
                  <tfoot>
                  <tr>
                     <th>ID</th>
                    <th>Product Name</th>
                    <th>Image</th>
                    <th>Action</th>
                  </tr>
                  </tfoot>
                </table>
    </div>
    <!-- /.card-body -->
    <div class="card-footer">
       Users List
    </div>
    <!-- /.card-footer-->
</div>
@endSection