@if(session()->has('msg'))
    <div class="alert alert-{{session('type')}}">
        <button type="button" class="btn-close close" data-dismiss="alert" aria-label="Close">&times;</button>
        {!! (session('msg')) !!}
    </div>
@endif
