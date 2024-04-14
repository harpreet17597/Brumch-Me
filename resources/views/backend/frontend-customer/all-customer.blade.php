@extends('backend.admin-master')
@section('style')
    <x-summernote.css/>
    <x-datatable.css/>
@endsection
@section('site-title')
    {{__('All Customers')}}
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
                                    <h4 class="header-title">{{__('All Customers')}}</h4>
                                    <div class="data-tables datatable-primary table-wrap">
                                        <table class="text-center" id="customer_table">
                                            <thead class="text-capitalize">
                                            <tr>
                                                <th>{{__('ID')}}</th>
                                                <th>{{__('Profile Image')}}</th>
                                                <th>{{__('Name')}}</th>
                                                <th>{{__('Phone Country')}}</th>
                                                <th>{{__('Phone')}}</th>
                                                <th>{{__('Email')}}</th>
                                                <th>{{__('DOB')}}</th>
                                                <th>{{__('Address')}}</th>
                                                <th>{{__('Account Status')}}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            
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
<script type="text/javascript">
    const urls = {
   
        dataTable : "{{ route('admin.all.frontend.customer.datatable') }}",
        changeProfileVerificationStatus : "{{ route('admin.frontend.customer.profile.verify','id') }}",
        changeAccountStatus : "{{ route('admin.frontend.customer.account.status','id') }}"
 
    }
</script>
<script src="{{asset('dist/js/common/ajax.js')}}"></script>
<script src="{{asset('dist/js/pages/customer.js')}}"></script>
@endsection
