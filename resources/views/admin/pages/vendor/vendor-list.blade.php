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
                    <th>Email</th>
                    <th>Active</th>
                    <th>Actions</th>
                  </tr>
                  </thead>
                  <tbody>
                    @if($users && $users->count())
                      @foreach($users as $user)
                        <tr>
                            <td>{{$user->name}}@if($user->is_active == '1')
                               <button class="btn btn-xs btn-success">Active</button>
                              @else
                               <button class="btn btn-xs btn-danger">Inactive</button>
                              @endif</td>
                            <td>{{$user->email}}</td>
                            <td>
                              <div class="form-group">
                                  <div class="custom-control custom-switch">
                                  <input type="checkbox" class="custom-control-input change-user-active-status" id="active-{{$user->id}}" data-user-id="{{$user->id}}" name="active" @if($user->is_active =='1'){{'checked'}}@endif> 
                                  <label class="custom-control-label" for="active-{{$user->id}}"></label>
                                  </div>
                                </div>
                            </td>
                            <td>
                                <a href="{{route('admin.vendors.edit',[$user->id])}}" class="btn btn-sm btn-primary">edit</a>
                                <form action="{{route('admin.vendors.destroy',[$user->id])}}" style="display:inline" method="post">
                                  @csrf
                                  @method('delete')
                                  <button class="btn btn-sm btn-danger">delete</button>
                                </form>
                                
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
                    <th>Email</th>
                    <th>Active</th>
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

@section('js')
<script>
  $('document').ready(function(){

      $('.change-user-active-status').on('click',function(){
          
          let ele = $(this);
          let user_id = ele.data('user-id');
          let active = ele.is(':checked') ? '1' : '0';
          $.ajax({
              
               url:"{{route('admin.users.change_active_status')}}",
               method:"post",
               headers: {
                    'X-CSRF-TOKEN': "{{csrf_token()}}"
                },
               data:{user_id,active},
               success:function(){

                    let tdEle = ele.parents('tr').first('td');
                    if(active == '1') {
                        tdEle.find('button:first').removeClass('btn-danger').addClass('btn-success').text('Active')
                    }else {
                      tdEle.find('button:first').removeClass('btn-success').addClass('btn-danger').text('Inactive')
                    } 
               },
               error:function() {

               }
          }) 
      });
  })
</script>
@endSection