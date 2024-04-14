@extends('admin.layouts.master')
@section('content')

  <!-- DataTables -->
  <link rel="stylesheet" href="{{asset('/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
  <link rel="stylesheet" href="{{asset('/plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
  <link rel="stylesheet" href="{{asset('/plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">

<div class="card">
  <div class="card-header">
    <h3 class="card-title">Business List</h3>
  </div>
  <div class="card-body">
    <table id="business_table" class="table table-bordered table-hover">
      <thead>
        <tr>
          <th>id</th>
          <th>Profile Image</th>
          <th>Name</th>
          <th>Email</th>
          <th>Restaurant Opening Time</th>
          <th>Restaurant Closing Time</th>
          <th>Profile Verification</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>

      </tbody>
      <tfoot>
        <tr>
          <th>id</th>
          <th>Profile Image</th>
          <th>Name</th>
          <th>Email</th>
          <th>Restaurant Opening Time</th>
          <th>Restaurant Closing Time</th>
          <th>Profile Verification</th>
          <th>Actions</th>
        </tr>
      </tfoot>
    </table>
  </div>
  <!-- /.card-body -->
  <div class="card-footer">
    Business List
  </div>
  <!-- /.card-footer-->
</div>
@endSection

@section('js')
<script src="{{asset('/plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('/plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('/plugins/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>
<script src="{{asset('/plugins/datatables-buttons/js/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('/plugins/datatables-buttons/js/buttons.bootstrap4.min.js')}}"></script>
<script src="{{asset('/plugins/datatables-buttons/js/buttons.html5.min.js')}}"></script>
<script src="{{asset('/plugins/datatables-buttons/js/buttons.print.min.js')}}"></script>
<script src="{{asset('/plugins/datatables-buttons/js/buttons.colVis.min.js')}}"></script>
<script src="{{asset('/plugins/sweetalert2/sweetalert2.all.min.js')}}"></script>

<script type="text/javascript">
    const urls = {
   
        dataTable : "{{ route('admin.business.datatable') }}",
        changeProfileVerificationStatus : "{{ route('admin.business.change-profile-verification-status','id') }}"
 
    }
</script>
<script src="{{asset('dist/js/common/ajax.js')}}"></script>
<script src="{{asset('dist/js/pages/business.js')}}"></script>

@endSection