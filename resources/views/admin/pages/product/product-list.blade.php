@extends('admin.layouts.master')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Users List</h3>
    </div>
    <div class="card-body">
    <table id="example2" class="table table-bordered table-hover">
                  <thead>
                  <tr>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Description</th>
                    <th>Quantity</th>
                    <th>Actions</th>
                  </tr>
                  </thead>
                  <tbody>
                    @if($products && $products->count())
                      @foreach($products as $product)
                        <tr>
                            <td>{{$product->name}}</td>
                            <td>{{$product->slug}}</td>
                            <td>{{$product->description}}</td>
                            <td>{{$product->quantity}}</td>
                            <td>
                                <a href="{{route('admin.product.edit',[$product->id])}}" class="btn btn-sm btn-primary">edit</a>
                                <form action="{{route('admin.product.destroy',[$product->id])}}" style="display:inline" method="post">
                                  @csrf
                                  @method('delete')
                                  <button class="btn btn-sm btn-danger">delete</button>
                                </form>
                                <a href="{{route('admin.product.images',[$product->id])}}" class="btn btn-sm btn-success">Images</a>
                            </td>
                        </tr>
                      @endforeach
                    @else
                     <tr>
                      <td colspan="3" style="text-align:center;">No Data Found!</td>
                     </tr> 
                    @endif
                  </tbody>
                  <tfoot>
                  <tr>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Description</th>
                    <th>Quantity</th>
                    <th>Actions</th>
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