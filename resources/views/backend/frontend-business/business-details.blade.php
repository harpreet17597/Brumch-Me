@extends('backend.admin-master')
@section('site-title')
{{__('Business Details')}}
@endsection
@section('content')
<div class="col-lg-12 col-ml-12 padding-bottom-30">
    @if(!empty($business_details))
    @php($restaurant = $business_details->restaurant)
    @php($images = $restaurant ? $restaurant->images : null)
    @php($menus = $restaurant ? $restaurant->menus : null)
    <div class="row mt-5">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="checkbox-inlines">
                        <label><strong>{{ __('Business ID:') }} </strong>#{{ $business_details->id }}</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-5 mt-5">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        <h5>{{ __('Business Details') }}</h5>
                    </div>
                    <table class="table table-bordered">
                        <tr>
                            <td>{{ __('Name:') }}</td>
                            <td>{{ $business_details->name }}</td>
                        </tr>
                        <tr>
                            <td>{{ __('Email:') }}</td>
                            <td>{{ $business_details->email }}</td>
                        </tr>
                        <tr>
                            <td>{{ __('Country Code:') }} </td>
                            <td>{{ $business_details->country_code }}</td>
                        </tr>
                        <tr>
                            <td>{{ __('Phone:') }}</td>
                            <td>{{ $business_details->phone_country }} {{ $business_details->phone }}</td>
                        </tr>
                        <tr>
                            <td>{{ __('DOB:') }} </td>
                            <td>{{ $business_details->dob }}</td>
                        </tr>
                        <tr>
                            <td>{{ __('Country Code:') }} </td>
                            <td>{{ $business_details->country_code }}</td>
                        </tr>
                        <tr>
                            <td>{{ __('Address:') }} </td>
                            <td>{{ $business_details->street_address }}</td>
                        </tr>
                        <tr>
                            <td>{{ __('City:') }} </td>
                            <td>{{ $business_details->name }}</td>
                        </tr>
                        <tr>
                            <td>{{ __('Status:') }} </td>
                            <td><span id="verified_status">{{ ($business_details->is_verified == '1') ? 'verified' : 'not verified'; }}</span></label>
                                <a tabindex="0" class="btn btn-warning btn-xs btn-sm mr-1 ml-3 swal_profile_verify_status_change" data-user-id="{{$business_details->id}}"><i class="ti-pencil"></i></a>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-7 mt-5">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        <h5>{{ __('Restaurant Details') }}</h5>
                    </div>
                    @if(!empty($restaurant))
                    <table class="table table-bordered">
                        <tr>
                            <td>{{ __('Restaurant Name:') }} </td>
                            <td>{{ $restaurant->restaurant_name }}</td>
                        </tr>
                        <tr>
                            <td>{{ __('Restaurant Description:') }} </td>
                            <td>{{ $restaurant->restaurant_description }}</td>
                        </tr>
                        <tr>
                            <td>{{ __('Restaurant Opening Time:') }}</td>
                            <td>{{ $restaurant->restaurant_opening_time }}</td>
                        </tr>
                        <tr>
                            <td>{{ __('Restaurant Closing Time:') }}</td>
                            <td>{{ $restaurant->restaurant_closing_time }}</td>
                        </tr>
                        <tr>
                            <td>{{ __('Restaurant Description:') }} </td>
                            <td>{{ $restaurant->restaurant_description }}</td>
                        </tr>
                        <tr>
                            <td>{{ __('Tags:')}} </td>
                            <td>{{$restaurant->tags->pluck('name')->implode(',') }}</td>
                        </tr>
                        <tr>
                            <td>{{ __('Restaurant Address:') }} </td>
                            <td>{{ $restaurant->restaurant_address }}</td>
                        </tr>
                    </table>
                    @else
                    <p>NO DATA FOUND!</p>
                    @endif
                </div>
            </div>
        </div>

    </div>
    <div class="row mt-">
        <div class="col-lg-12 mt-5">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                       <h5>{{ __('Restaurant Images') }}</h5><br>
                    </div>
                    @if($images)
                    <div class="single-checbox">
                        <div class="checkbox-inlines">
                            @foreach($images as $image)
                            <img src="{{$image->restaurant_image}}" width="120px" />
                            @endforeach
                        </div>
                    </div>
                    @else
                    <p>NO DATA FOUND!</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 mt-5">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                       <h5>{{ __('Restaurant Menu\'s')}}</h5>
                    </div>
                    @if($menus)
                    <table class="table table-bordered">
                        <thead> 
                            <tr>
                                <th>{{ __('Menu Image') }}</th>
                                <th>{{ __('Menu Name') }}</th>
                                <th>{{ __('Menu Price') }}</th>
                                <th>{{ __('Menu Quantity') }}</th>
                                <th>{{ __('Menu Description') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($menus as $menu)
                            <tr>
                                <td> <img src="{{$menu->restaurant_menu_image}}" width="100px" /></td>
                                <td>{{ $menu->restaurant_menu_name }}</td>
                                <td>{{ $menu->restaurant_menu_price }}</td>
                                <td>{{ $menu->restaurant_menu_quantity }}</td>
                                <td>{{ $menu->restaurant_menu_description }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <p>NO DATA FOUND!</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @endif
</div>
@endsection

@section('script')
<script src="{{asset('dist/js/common/ajax.js')}}"></script>
<script type="text/javascript">
    const urls = {

        dataTable: "{{ route('admin.all.frontend.business.datatable') }}",
        changeProfileVerificationStatus: "{{ route('admin.frontend.business.profile.verify','id') }}"

    }
</script>
<script>
    $(document).ready(function() {

        $(document).on('click', '.swal_profile_verify_status_change', function(e) {
            e.preventDefault();

            let user_id = $(this).data('user-id');

            Swal.fire({
                title: 'Are you sure to change status?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, change it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    if (user_id) {
                        let action_url = urls.changeProfileVerificationStatus;
                        action_url = action_url.replace('id', user_id);
                        dynamicAjax(action_url, 'POST', {}).then((successRes) => {

                                console.log(successRes.data.is_verified);
                                if (successRes.data.is_verified == 0) {
                                    $('#verified_status').html('not verifeid');
                                }

                                if (successRes.data.is_verified == 1) {
                                    $('#verified_status').html('verified');
                                }
                            })
                            .catch((errorRes) => {
                                console.log(errorRes);
                            })
                    } else {
                        alert('dd');
                    }
                }
            });
        });

    });
</script>

@endsection