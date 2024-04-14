@extends('backend.admin-master')
@section('style')
    <x-summernote.css/>
    <x-datatable.css/>
@endsection
@section('site-title')
    {{__('All Pages')}}
@endsection

@section('content')
    <div class="col-lg-12 col-ml-12 padding-bottom-30">
        <div class="row">
            <div class="col-12 mt-5">
                <div class="card">
                    <div class="card-body">
                        <div class="col-12 mt-5">
                            <div class="card">
                                <div class="card-body">
                                  <x-msg.success/>
                                  <x-msg.error/>
                                    <h4 class="header-title">{{__('All Pages')}}</h4>
                                    <div class="data-tables datatable-primary table-wrap">
                                        <table class="text-center" id="customer_table">
                                            <thead class="text-capitalize">
                                            <tr>
                                                <th>{{__('ID')}}</th>
                                                <th>{{__('Title')}}</th>
                                                <th>{{__('Actions')}}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                              @if(isset($all_pages))
                                               @forelse($all_pages as $page)
                                                <tr>
                                                    <td>{{ $loop->index+1 }}</td>
                                                    <td>{{ $page->title}}</td>
                                                    <td>
                                                        <a class="btn btn-primary btn-xs mb-3 mr-1" href="{{route('admin.page.edit',$page->id)}}">
                                                           <i class="ti-pencil"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                @empty 
                                                <tr>
                                                    <td colspan=4>No Data Found!</td>
                                                </tr>
                                               @endforelse
                                              @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Primary table end -->
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
<x-datatable.js/>
<script>
    (function($){
        "use strict";
        $(document).ready(function() {
            $('.table-wrap > table').DataTable( {
                "order": [[ 1, "desc" ]],
                'columnDefs' : [{
                    'targets' : 'no-sort',
                    "orderable" : false
                }]
            } );
        } );

    })(jQuery)
</script>
@endsection
