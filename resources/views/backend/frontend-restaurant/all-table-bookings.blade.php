@extends('backend.admin-master')
@section('style')
<x-summernote.css />
<x-datatable.css />
@endsection
@section('site-title')
{{__('All Bookings')}}
@endsection

@section('content')

<div class="col-lg-12 col-ml-12 padding-bottom-30">
    <div class="row">
        <div class="col-lg-12 mt-5">
            <div class="card">
                <div class="card-body">
                    <div class="checkbox-inlines">
                        <label><strong>{{$booking_type ?? 'ALL'}}</strong></label>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 mt-5">
            <div class="card">
                <div class="card-body">
                    <div class="col-12 mt-5">
                        <div class="card">
                            <div class="card-body">
                                <x-msg.success />
                                <x-msg.error />
                                <h4 class="header-title">{{__('All Bookings')}}</h4>
                                <div class="data-tables datatable-primary table-wrap">
                                    <table class="text-center" id="booking_table">
                                        <thead class="text-capitalize">
                                            <tr>
                                                <th>{{__('Booking Number')}}</th>
                                                <th>{{__('Booking From Date')}}</th>
                                                <th>{{__('Booking To Date')}}</th>
                                                <th>{{_('Customer Name')}}</th>
                                                <th>{{_('Customer Phone')}}</th>
                                                <th>{{_('Restaurant Name')}}</th>
                                                <th>{{_('Status')}}</th>
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
<x-datatable.js />
<script type="text/javascript">
    const urls = {

        dataTable: "{{ route('admin.all.restaurant.table.bookings.datatable') }}?status={{request()->get('status')}}",

    }
</script>
<script src="{{asset('dist/js/common/ajax.js')}}"></script>
<script src="{{asset('dist/js/pages/booking.js')}}"></script>
@endsection