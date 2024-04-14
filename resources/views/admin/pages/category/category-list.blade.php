@extends('admin.layouts.master')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Category List</h3>
    </div>
    <div class="card-body">
    <table id="example2" class="table table-bordered table-hover">
                  <thead>
                  <tr>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Description</th>
                    <th>Image</th>
                    <th>Actions</th>
                  </tr>
                  </thead>
                  <tbody>
                    @if($categories && $categories->count())
                      @foreach($categories as $category)
                        <tr>
                            <td>{{$category->name}}</td>
                            <td>{{$category->slug}}</td>
                            <td>{{$category->description}}</td>
                            <td><img src="{{url($category->image)}}" alt="" width="100"></td>
                            <td>
                                <a href="{{route('admin.category.edit',[$category->id])}}" class="btn btn-sm btn-primary">edit</a>
                                <form action="{{route('admin.category.destroy',[$category->id])}}" style="display:inline" method="post">
                                  @csrf
                                  @method('delete')
                                  <button class="btn btn-sm btn-danger">delete</button>
                                </form>
                            </td>
                        </tr>
                      @endforeach
                    @else
                     <tr>
                      <td colspan="4" style="text-align:center;">No Data Found!</td>
                     </tr> 
                    @endif
                  </tbody>
                  <tfoot>
                  <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Actions</th>
                  </tr>
                  </tfoot>
                </table>
    </div>
    <!-- /.card-body -->
    <div class="card-footer">
       Category List
    </div>
    <!-- /.card-footer-->
</div>
@endSection