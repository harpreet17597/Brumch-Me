@if(Session::has('error'))
 <div class="alert alert-danger alert-dismissible">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    <strong></strong> {{session('error')}}
  </div>
@endif