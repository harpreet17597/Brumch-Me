@extends('backend.admin-master')
@section('style')

<link rel="stylesheet" href="{{asset('assets/backend/css/summernote-bs4.css')}}">
<link rel="stylesheet" href="{{asset('assets/backend/css/bootstrap-tagsinput.css')}}">
<style>
    .btn-group.note-insert {
  display: none;
}
</style>
@endsection
@section('site-title')
{{__('Edit Page')}}
@endsection
@section('content')
<div class="col-lg-12 col-ml-12 padding-bottom-30">
    <div class="row">
        <div class="col-lg-12">
            <div class="margin-top-40"></div>
            <x-msg.success />
            <x-msg.error />
        </div>
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="header-wrap d-flex justify-content-between">
                        <div class="left-content">
                            <h4 class="header-title">{{__('Edit Page')}} </h4>
                        </div>
                        <div class="header-title d-flex">
                            <div class="btn-wrapper-inner">
                                <a href="{{ route('admin.page') }}" class="btn btn-primary">{{__('All Pages')}}</a>
                            </div>
                        </div>
                    </div>
                    <form action="{{route('admin.page.update',$page_post->id)}}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="tab-content margin-top-40">

                            <div class="form-group">
                                <label for="title">{{__('Title')}}</label>
                                <input type="text" class="form-control" name="title" value="{{$page_post->title}}" id="title">
                            </div>

                            <div class="form-group classic-editor-wrapper @if(!empty($page_post->page_builder_status)) d-none @endif ">
                                <label>{{__('Content')}}</label>
                                <input type="hidden" name="page_content" value="{{$page_post->page_content}}">
                                <div class="summernote" data-content="{{ $page_post->page_content }}"></div>
                            </div>

                            <button type="submit" id="submit" class="btn btn-info mt-4 pr-4 pl-4">{{__('Update')}}</button>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
@section('script')
<script src="{{asset('assets/backend/js/bootstrap-tagsinput.js')}}"></script>
<script src="{{asset('assets/backend/js/summernote-bs4.js')}}"></script>
<script>
    (function($) {
        "use strict";
        $(document).ready(function() {

            $('.summernote').summernote({
                height: 400, //set editable area's height
                codemirror: { // codemirror options
                    theme: 'monokai'
                },
                callbacks: {
                    onChange: function(contents, $editable) {
                        $(this).prev('input').val(contents);
                    }
                }
            });
            if ($('.summernote').length > 0) {
                $('.summernote').each(function(index, value) {
                    $(this).summernote('code', $(this).data('content'));
                });
            }
        });
    })(jQuery);
</script>
@endsection