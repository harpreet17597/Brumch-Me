@if(Session::has('status'))
<div class="alert alert-success alert-dismissible">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    <strong></strong> {{session('status')}}
  </div>
@endif