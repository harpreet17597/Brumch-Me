@extends('backend.admin-master')
@section('site-title')
    {{__('Dashboard')}}
@endsection

@section('style')
    <style>
        .bg_card_color_one{
            background: rgb(2,0,36);
            background: linear-gradient(90deg, rgba(2,0,36,1) 0%, rgba(65,107,125,1) 35%, rgba(0,212,255,1) 100%); 
        }
        .bg_card_color_two{
            background: rgb(34,193,195);
            background: linear-gradient(0deg, rgba(34,193,195,1) 0%, rgba(50,120,119,1) 100%);  
        }



.orders-child:nth-child(4n+2) .single-orders {
  background: #1dbf73;
}
.orders-child:nth-child(4n+2) .single-orders .icon {
  color: #1dbf73;
}

.orders-child:nth-child(4n+3) .single-orders {
  background: #C71F66;
}
.orders-child:nth-child(4n+3) .single-orders .icon {
  color: #C71F66;
}

.orders-child:nth-child(4n+4) .single-orders {
  background: #6560FF;
}
.orders-child:nth-child(4n+4) .single-orders .icon {
  color: #6560FF;
}
  
.single-orders {
  background: #FF6B2C;
  padding: 35px 30px;
  border-radius: 10px;
  position: relative;
  z-index: 2;
  overflow: hidden;
}
@media (min-width: 1200px) and (max-width: 1399.98px) {
  .single-orders {
    padding: 20px 20px;
  }
}
.single-orders .orders-shapes img {
  position: absolute;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  z-index: -1;
}
.single-orders .orders-flex-content {
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-align: center;
  -ms-flex-align: center;
  align-items: center;
  gap: 30px;
}
@media (min-width: 1200px) and (max-width: 1399.98px) {
  .single-orders .orders-flex-content {
    display: block;
    text-align: center;
  }
}
.single-orders .orders-flex-content .icon {
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-align: center;
  -ms-flex-align: center;
  align-items: center;
  -webkit-box-pack: center;
  -ms-flex-pack: center;
  justify-content: center;
  height: 67px;
  width: 67px;
  font-size: 40px;
  background: #fff;
  color: #FF6B2C;
  border-radius: 50%;
}
@media (min-width: 1200px) and (max-width: 1399.98px) {
  .single-orders .orders-flex-content .icon {
    margin: 0 auto;
    text-align: center;
  }
}
.single-orders .orders-flex-content .contents .order-titles {
  font-size: 35px;
  font-weight: 700;
  line-height: 55px;
  color: #fff;
  margin: 0;
}
.single-orders .orders-flex-content .contents .order-para {
  font-size: 18px;
  font-weight: 500;
  line-height: 20px;
  color: #fff;
}

@media (min-width: 1400px) and (max-width: 1730px) {
  .single-orders {
    padding: 20px 20px;
  }

  .single-orders .orders-flex-content {
    display: block;
    text-align: center;
  }

  .single-orders .orders-flex-content .icon {
    margin: 0 auto;
    text-align: center;
  }
}
         
</style>
@endsection

@section('content')
    
    <div class="main-content-inner">
        <div class="row">
            <!-- <div class="col-xl-3 col-md-6 margin-top-30 orders-child">
                <div class="single-orders">
                    <div class="orders-shapes">
                        <img src="{{ asset('assets/frontend/img/static/orders-shapes.png') }}" alt="">
                    </div>
                    <a href="">
                        <div class="orders-flex-content">
                            <div class="icon">
                                <i class="las la-user-circle"></i>
                            </div>
                            <div class="contents">
                                <h2 class="order-titles">{{$users_count['admin'] ?? 0}}</h2>
                                <span class="order-para">{{ __('Total Admin') }} </span>
                            </div>
                        </div>
                    </a>
                </div>
            </div> -->
            <div class="col-xl-3 col-md-6 margin-top-30 orders-child">
                <div class="single-orders">
                    <div class="orders-shapes">
                        <img src="{{ asset('assets/frontend/img/static/orders-shapes2.png') }}" alt="">
                    </div>
                    <a href="{{route('admin.all.frontend.customer')}}">
                        <div class="orders-flex-content">
                            <div class="icon">
                                <i class="las la-user-circle"></i>
                            </div>
                            <div class="contents">
                                <h2 class="order-titles">{{$users_count['customer'] ?? 0}}</h2>
                                <span class="order-para"> {{ __('Total Customers') }} </span>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 margin-top-30 orders-child">
                <div class="single-orders">
                    <div class="orders-shapes">
                        <img src="{{ asset('assets/frontend/img/static/orders-shapes3.png') }}" alt="">
                    </div>
                    <a href="{{route('admin.all.frontend.business')}}">
                        <div class="orders-flex-content">
                            <div class="icon">
                                <i class="las la-user-circle"></i>
                            </div>
                            <div class="contents">
                                <h2 class="order-titles">{{$users_count['business'] ?? 0}}</h2>
                                <span class="order-para"> {{ __('Total Business') }}</span>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 margin-top-30 orders-child">
                <div class="single-orders">
                    <div class="orders-shapes">
                        <img src="{{ asset('assets/frontend/img/static/orders-shapes.png') }}" alt="">
                    </div>
                    <a href="{{route('admin.all.restaurant.table.bookings')}}?status=pending">
                    <div class="orders-flex-content">
                        <div class="icon">
                            <i class="las la-tasks"></i>
                        </div>
                        <div class="contents">
                            <h2 class="order-titles">{{$bookings_count['pending'] ?? 0}}</h2>
                            <span class="order-para">{{ __('Booking Pending') }} </span>
                        </div>
                    </div>
                    </a>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 margin-top-30 orders-child">
                <div class="single-orders">
                    <div class="orders-shapes">
                        <img src="{{ asset('assets/frontend/img/static/orders-shapes2.png') }}" alt="">
                    </div>
                    <a href="{{route('admin.all.restaurant.table.bookings')}}?status=confirmed">
                        <div class="orders-flex-content">
                            <div class="icon">
                                <i class="las la-handshake"></i>
                            </div>
                            <div class="contents">
                                <h2 class="order-titles">{{$bookings_count['confirmed'] ?? 0}}</h2>
                                <span class="order-para">{{ __('Booking Confirmed')}} </span>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 margin-top-30 orders-child">
                <div class="single-orders">
                    <div class="orders-shapes">
                        <img src="{{ asset('assets/frontend/img/static/orders-shapes3.png') }}" alt="">
                    </div>
                    <a href="{{route('admin.all.restaurant.table.bookings')}}?status=cancelled">
                        <div class="orders-flex-content">
                            <div class="icon">
                                <i class="las la-dollar-sign"></i>
                            </div>
                            <div class="contents">
                                <h2 class="order-titles">{{$bookings_count['cancelled'] ?? 0}}</h2>
                                <span class="order-para"> {{ __('Booking Cancelled') }} </span>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-md-6">
                <h4 class="mb-3 earning-title">{{ __('Latest Customers') }}</h4>
                <div class="table-wrap table-responsive">
                    <table class="table table-bordered">
                        <thead>
                        <th>{{__('ID')}}</th>
                        <th>{{__('Name')}}</th>
                        <th>{{__('Email')}}</th>
                        <th>{{__('Phone')}}</th>
                        </thead>
                        <tbody>
                           @if($latest_customer_users)
                            @foreach($latest_customer_users as $customer)
                            <tr>
                                <td>{{$loop->index + 1}}</td>
                                <td>{{$customer->name}}</td>
                                <td>{{$customer->email}}</td>
                                <td>{{$customer->phone}}</td>
                            </tr>
                            @endforeach
                           @endif
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-6">
                <h4 class="mb-3 earning-title">{{ __('Latest Business') }}</h4>
                <div class="table-wrap table-responsive">
                    <table class="table table-bordered">
                        <thead>
                        <th>{{__('ID')}}</th>
                        <th>{{__('Name')}}</th>
                        <th>{{__('Email')}}</th>
                        <th>{{__('Phone')}}</th>
                        </thead>
                        <tbody>
                        @if($latest_business_users)
                            @foreach($latest_business_users as $business)
                            <tr>
                                <td>{{$loop->index + 1}}</td>
                                <td><a href="{{route('admin.frontend.business.profile.view',$business->id)}}">{{$business->name}}</a></td>
                                <td>{{$business->email}}</td>
                                <td>{{$business->phone}}</td>
                            </tr>
                            @endforeach
                           @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <!-- <script src="{{asset('assets/backend/js/chart.js')}}"></script> -->
@endsection
