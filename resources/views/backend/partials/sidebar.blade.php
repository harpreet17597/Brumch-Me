<div class="sidebar-menu">
    <div class="sidebar-header">
        <div class="logo">
            <a href="{{ route('admin.home') }}">
                <img src="{{asset('assets/backend/images/brunch-me.jpg')}}" />
                {{-- @if (get_static_option('site_admin_dark_mode') == 'off')
                    {!! render_image_markup_by_attachment_id(get_static_option('site_logo')) !!}
                @else
                    {!! render_image_markup_by_attachment_id(get_static_option('site_white_logo')) !!}
                @endif --}}
            </a>
        </div>
    </div>
    <div class="main-menu">
        <div class="menu-inner">
            <nav>
                <ul class="metismenu" id="menu">
                    <li class="{{ active_menu('admin-home') }}">
                        <a href="{{ route('admin.home') }}" aria-expanded="true">
                            <i class="ti-dashboard"></i>
                            <span>@lang('dashboard')</span>
                        </a>
                    </li>
                    <li class="{{ active_menu('admin-home/dynamic-page') }}">
                        <a href="{{ route('admin.page') }}" aria-expanded="true">
                            <i class="ti-dashboard"></i>
                            <span>@lang('Pages')</span>
                        </a>
                    </li>
                    <li class="main_dropdown
                        @if (request()->is(['admin-home/frontend/all-business', 'admin-home/frontend/featued-subscription', 'admin-home/frontend/business-profile-view/*'])) active @endif
                        ">
                        <a href="javascript:void(0)" aria-expanded="true"><i class="ti-dashboard"></i>
                            <span>{{ __('Business Management') }}</span></a>
                        <ul class="collapse">
                            <li class="{{ active_menu('admin-home/frontend/all-business') }}"><a href="{{route('admin.all.frontend.business')}}">{{ __('All Business') }}</a></li>
                            <li class="{{ active_menu('admin-home/frontend/featued-subscription') }}"><a href="{{route('admin.all.frontend.featured.business')}}">{{ __('Featured Subscription') }}</a></li>
                            <!-- <li class="{{ active_menu('admin-home/frontend/all-business') }}"><a
                                            href="{{route('admin.all.frontend.business')}}?type=premium">{{ __('Premium Business') }}</a></li>       -->
                        </ul>
                    </li>
                    <li class="main_dropdown
                        @if (request()->is(['admin-home/frontend/all-customer', 'admin-home/frontend/customer-profile-view/*'])) active @endif
                        ">
                        <a href="javascript:void(0)" aria-expanded="true"><i class="ti-dashboard"></i>
                            <span>{{ __('Customer Management') }}</span></a>
                        <ul class="collapse">
                            <li class="{{ active_menu('admin-home/frontend/all-customer') }}">
                                <a href="{{route('admin.all.frontend.customer')}}">{{ __('All Customer') }}</a>
                            </li>
                        </ul>
                    </li>
                    <li class="main_dropdown
                        @if (request()->is(['admin-home/restaurant/table/bookings', 'admin-home/frontend/restaurant/table/bookings/*'])) active @endif
                        ">
                        <a href="javascript:void(0)" aria-expanded="true"><i class="ti-dashboard"></i>
                            <span>{{ __('Booking Management') }}</span></a>
                        <ul class="collapse">
                            <li class="{{ active_menu('admin-home/restaurant/table/bookings') }}">
                                <a href="{{route('admin.all.restaurant.table.bookings')}}">{{ __('All Bookings') }}</a>
                            </li>
                            <li class="{{ active_menu('admin-home/restaurant/table/bookings?status=pending') }}">
                                <a href="{{route('admin.all.restaurant.table.bookings')}}?status=pending">{{ __('Pending') }}</a>
                            </li>
                            <li class="{{ active_menu('admin-home/restaurant/table/bookings?status=confirmed') }}">
                                <a href="{{route('admin.all.restaurant.table.bookings')}}?status=confirmed">{{ __('Confirmed') }}</a>
                            </li>
                            <li class="{{ active_menu('admin-home/restaurant/table/bookings?status=cancelled') }}">
                                <a href="{{route('admin.all.restaurant.table.bookings')}}?status=cancelled">{{ __('Cancelled') }}</a>
                            </li>
                        </ul>
                    </li>
                    <li class="{{ active_menu('admin-home/frontend/all-banner') }}">
                        <a href="{{ route('admin.all.frontend.banner') }}" aria-expanded="true">
                            <i class="ti-dashboard"></i>
                            <span>@lang('Banner')</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>