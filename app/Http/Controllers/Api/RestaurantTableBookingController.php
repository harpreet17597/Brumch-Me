<?php

namespace App\Http\Controllers\Api;

use DB;
use App\Helpers\CommonHelper;
use App\Models\User;
use App\Models\Restaurant;
use App\Models\RestaurantMenu;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AvailabilityTimeSlotRequest;
use App\Traits\ApiMethodsTrait;
use Exception;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests\Api\RestaurantTableBookingRequest;
use App\Http\Requests\Api\BusinessTableBookingRequest;
use App\Http\Requests\Api\BookingConfirmRejectRequest;
use App\Models\BusinessAvailabilityAndTimeSlot;
use App\Models\RestaurantTableBooking;

class RestaurantTableBookingController extends Controller
{
    use ApiMethodsTrait, ApiResponser;

    /**
     * **************************************************************
     *   RESTAURANT TABLE BOOKING
     * **************************************************************
     * */
    public function restaurant_table_booking(RestaurantTableBookingRequest $request)
    {
        try {

            $data = $request->all(); // Get request data
            $user = auth()->user(); // Get the user

            if ($user) {
                if ($user->isProfileSuspended()) {
                    return $this->returnErrorResponse('Your profile is suspended please contact admin.');
                }
                if ($user->role() == 'business') {
                    if (!$user->hasVerifiedProfile()) {
                        return $this->returnErrorResponse('Your profile is not approved yet.');
                    }
                }
               
            }

            /**
             * GET RESTAURANT AND BUSINESS DETAIL
             */
            $restaurant = Restaurant::where('id', $data['restaurant_id'])->first();
            if (!$restaurant) {
                return $this->returnErrorResponse('restaurant does not found!');
            }
            $business_detail = $restaurant->business_detail;
            if (!$business_detail) {
                return $this->returnErrorResponse('business detail does not found!');
                if ($business_detail->has_free_trial == '0') {
                    return $this->returnErrorResponse('Business free trial peroid is expired.');
                    
                }
                if(is_null($business_detail->subscription_status)) {
                    return $this->returnErrorResponse('Business does not have any active plan.');
                }
            }

            /**
             * CHECK BOOKING TIME SLOT IS AVAILABLE
             */
            /**
             * ========================================================================
             */
            $restaurant_opening_time = null;
            $restaurant_closing_time = null;

            $restaurant_opening_time = $restaurant->restaurant_opening_time;
            $restaurant_closing_time = $restaurant->restaurant_closing_time;
            

            /*handle in restaurant model modify opening and closing time before getting data*/
            // $today_available_slot = BusinessAvailabilityAndTimeSlot::where([
            //                                      'business_id' => $business_detail->id,'availability_date' => $data['booking_date']
            //                                     ])->first();
            // if($today_available_slot) {
            //     $restaurant_opening_time = $today_available_slot->time_slot_from;
            //     $restaurant_closing_time = $today_available_slot->time_slot_to;
            // } 
            // else {
            //     $restaurant_opening_time = $business_detail->restaurant_opening_time;
            //     $restaurant_closing_time = $business_detail->restaurant_closing_time;
            // } 
            
            if (!empty($restaurant_opening_time) && !empty($restaurant_closing_time) && !empty($data['booking_from_time']) && !empty($data['booking_to_time']) ) {
                if (!is_null($restaurant_opening_time) && !is_null($restaurant_closing_time) && !is_null($data['booking_from_time'])  && !is_null($data['booking_to_time'])) {
                    
                    if (!CommonHelper::isTimeBetween($restaurant_opening_time, $restaurant_closing_time, $data['booking_from_time'])) {
                        return $this->returnErrorResponse('time slot is not available');
                    }
                    if (!CommonHelper::isTimeBetween($restaurant_opening_time, $restaurant_closing_time, $data['booking_to_time'])) {
                        return $this->returnErrorResponse('time slot is not available');
                    }
                }
            } else {
                return $this->returnErrorResponse('error while booking a table');
            }
            /**
             * ========================================================================
             */


            /**
             * CREATE REQUEST FOR BOOKING
             */
            $bookingDateString     = $data['booking_date'];
            $bookingFromTimeString = $data['booking_from_time'];
            $bookingToTimeString   = $data['booking_to_time'];

            $combinedFromDateTimeObj   = Carbon::parse($bookingDateString . ' ' . $bookingFromTimeString);
            $bookingFromDateTimeString = $combinedFromDateTimeObj->format('Y-m-d H:i:s');

            $combinedToDateTimeObj   = Carbon::parse($bookingDateString . ' ' . $bookingToTimeString);
            $bookingToDateTimeString = $combinedToDateTimeObj->format('Y-m-d H:i:s');

            $table_booking_request = RestaurantTableBooking::create([

                'booking_number'         => CommonHelper::generateBookingId(),
                'customer_id'            => $user->id,
                'business_id'            => $business_detail->id,
                'restaurant_id'          => $restaurant->id,
                'booking_from_date_time' => $bookingFromDateTimeString,
                'booking_to_date_time'   => $bookingToDateTimeString,
                'number_of_persons'      => $data['number_of_persons'],
                'status'                 => RestaurantTableBooking::BOOKING_PENDING
            ]);

            if ($table_booking_request) {

                if($business_detail->isLoggedIn() == '1') {

                    if (filled($business_detail->fcm_token)) {
                        $notificationMessage = "New table booking request."; 
                        $additionalData = [
                            'user_id'          => $business_detail->id,
                            'title'            => 'New Booking',
                            'body'             => $notificationMessage,
                            'type'             => 1,
                            'role'             => 'business',
                            'booking_number'   => $table_booking_request->booking_number,
                            'notificationType' => 'new_table_booking_request',
                            'restaurant_id'    => $table_booking_request->restaurant_id
                        ];
    
                        CommonHelper::sendCurlPushNotification($business_detail->fcm_token, $additionalData);
                    }
                }


                return $this->returnSuccessResponse('Your booking request has been sent successfully.', $table_booking_request);
            }

            return $this->returnErrorResponse('unable to book a table');
        } catch (Exception $exception) {
            report($exception);
            return $this->returnErrorResponse($exception->getMessage());
        }
    }
    
    /**
     * **************************************************************
     *  BOOKING DETAIL
     * **************************************************************
     * */
    public function restaurant_table_booking_detail(Request $request) {
       
        $rules = ['booking_number' => ['required','string','min:5','max:500']];
        $this->validate($request,$rules);
        try {

            $data = $request->all(); // Get request data
            $user = auth()->user(); // Get the user
            $booking = RestaurantTableBooking::where('booking_number', $data['booking_number'])->with('customer_details','business_details','restaurant_details')->first();
            if ($booking) {
                return $this->returnSuccessResponse('Booking detail',$booking);
            }
            return $this->returnErrorResponse('booking not found.');
        }
        catch (Exception $exception) {
            report($exception);
            return $this->returnErrorResponse($exception->getMessage());
        }

    }


    /**
     * **************************************************************
     *  CUSTOMER BOOKING TABLE LIST
     * **************************************************************
     * */
    public function customer_restaurant_table_booking_list(BusinessTableBookingRequest $request)
    {

        try {
            $data     = $request->all(); // Get request data
            $user     = auth()->user(); // Get the user
            $bookings = $user->customer_restaurant_table_booking_list(); // get business booking

            $dataArr = [];

            if (isset($data['type'])) {

                $currentDate = Carbon::now()->toDateString();

                if ($data['type'] == 'past') {
                    $bookings = $bookings->whereDate('booking_from_date_time', '<', $currentDate);
                }

                if ($data['type'] == 'current') {
                    $bookings = $bookings->whereDate('booking_from_date_time', $currentDate);
                }

                if ($data['type'] == 'upcoming') {
                    $bookings = $bookings->whereDate('booking_from_date_time', '>', $currentDate);
                }
            }

            if (isset($data['booking_type'])) {

                if ($data['booking_type'] == RestaurantTableBooking::BOOKING_PENDING) {

                    $bookings = $bookings->where('status', RestaurantTableBooking::BOOKING_PENDING);
                }

                if ($data['booking_type'] == RestaurantTableBooking::BOOKING_CANCELLED) {
                    $bookings = $bookings->where('status', RestaurantTableBooking::BOOKING_CANCELLED);
                }

                if ($data['booking_type'] == RestaurantTableBooking::BOOKING_CONFIRMED) {
                    $bookings = $bookings->where('status', RestaurantTableBooking::BOOKING_CONFIRMED);
                }

                if (
                    isset($data['data_type']) &&
                    isset($data['date']) &&
                    in_array($data['booking_type'], [
                        RestaurantTableBooking::BOOKING_PENDING,
                        RestaurantTableBooking::BOOKING_CANCELLED,
                        RestaurantTableBooking::BOOKING_CONFIRMED
                    ])
                ) {
                    if ($data['data_type'] == 'before') {
                        $bookings = $bookings->whereDate('booking_from_date_time', '<', $data['date']);
                    }

                    if ($data['data_type'] == 'after') {
                        $bookings = $bookings->whereDate('booking_from_date_time', '>=', $data['date']);
                    }
                }
            }

            $bookings = $bookings->with(['restaurant_details.images'])->get()->toArray();

            $bookings = array_map(function ($record) {

                $restaurant_details = $record['restaurant_details'];
                $images = collect($restaurant_details['images'])->pluck('restaurant_image')->toArray();
                $record['restaurant_details']['images'] = $images;
                return $record;
            }, $bookings);

            //collection
            $bookings = collect($bookings);
            
            $result = $this->paginate($bookings); //paginate result
            return $this->returnSuccessResponse('booking listing', $result);
        } catch (Exception $exception) {
            report($exception);
            return $this->returnErrorResponse($exception->getMessage());
        }
    }

    /**
     * **************************************************************
     *  BUSINESS BOOKING TABLE LIST
     * **************************************************************
     * */
    public function business_restaurant_table_booking_list(BusinessTableBookingRequest $request)
    {

        try {
            $data     = $request->all(); // Get request data
            $user     = auth()->user(); // Get the user
            $bookings = $user->business_restaurant_table_booking_list(); // get business booking
            $dataArr = [];

            if (isset($data['type'])) {

                $currentDate = Carbon::now()->toDateString();

                if ($data['type'] == 'past') {
                    $bookings = $bookings->whereDate('booking_from_date_time', '<', $currentDate);
                }

                if ($data['type'] == 'current') {
                    $bookings = $bookings->whereDate('booking_from_date_time', $currentDate);
                }

                if ($data['type'] == 'upcoming') {
                    $bookings = $bookings->whereDate('booking_from_date_time', '>', $currentDate);
                }
            }

            if (isset($data['booking_type'])) {

                if ($data['booking_type'] == RestaurantTableBooking::BOOKING_PENDING) {

                    $bookings = $bookings->where('status', RestaurantTableBooking::BOOKING_PENDING);
                }

                if ($data['booking_type'] == RestaurantTableBooking::BOOKING_CANCELLED) {
                    $bookings = $bookings->where('status', RestaurantTableBooking::BOOKING_CANCELLED);
                }

                if ($data['booking_type'] == RestaurantTableBooking::BOOKING_CONFIRMED) {
                    $bookings = $bookings->where('status', RestaurantTableBooking::BOOKING_CONFIRMED);
                }

                if (
                    isset($data['data_type']) &&
                    isset($data['date']) &&
                    in_array($data['booking_type'], [
                        RestaurantTableBooking::BOOKING_PENDING,
                        RestaurantTableBooking::BOOKING_CANCELLED,
                        RestaurantTableBooking::BOOKING_CONFIRMED
                    ])
                ) {

                    if ($data['data_type'] == 'before') {
                        $bookings = $bookings->whereDate('booking_from_date_time', '<', $data['date']);
                    }

                    if ($data['data_type'] == 'after') {
                        $bookings = $bookings->whereDate('booking_from_date_time', '>=', $data['date']);
                    }
                }
            }

            $bookings = $bookings->get();

            if ($bookings->count() > 0) {

                foreach ($bookings as $booking) {

                    $booking_details_arr  = [];
                    $customer_details_arr = [];
                    $booking_details_arr  = [

                        "booking_number"         =>  $booking->booking_number,
                        "booking_from_date_time" =>  $booking->booking_from_date_time,
                        "booking_to_date_time"   =>  $booking->booking_to_date_time,
                        "number_of_persons"      =>  $booking->number_of_persons,
                        "status"                 =>  $booking->status,
                    ];

                    $customer_details = $booking->customer_details;
                    if ($customer_details) {
                        $customer_details_arr = [

                            "id"             => $customer_details->id,
                            "name"           => $customer_details->name,
                            "dob"            => $customer_details->dob,
                            "email"          => $customer_details->email,
                            "phone_country"  => $customer_details->phone_country,
                            "country_code"   => $customer_details->country_code,
                            "phone"          => $customer_details->phone,
                            "profile_image"  => $customer_details->profile_image,
                            "street_address" => $customer_details->street_address,
                        ];
                    }

                    $booking_details_arr['customer_details'] = $customer_details_arr;
                    $dataArr[] = $booking_details_arr;
                }
            }

            $result = $this->paginate(collect($dataArr)); //paginate result
            return $this->returnSuccessResponse('booking listing', $result);
        } catch (Exception $exception) {
            report($exception);
            return $this->returnErrorResponse($exception->getMessage());
        }
    }
    
     /**
     * **************************************************************
     *  BUSINESS BOOKING TABLE LIST ALL TYPES
     * **************************************************************
     * */
    public function business_restaurant_table_booking_list_all(Request $request) {

        try {

            $data     = $request->all(); // Get request data
            $user     = auth()->user(); // Get the user
            $currentDate = Carbon::now()->toDateString();
            
            $past_bookings = $user->business_restaurant_table_booking_list()
                                  ->where('status',RestaurantTableBooking::BOOKING_CONFIRMED)
                                  ->whereDate('booking_from_date_time', '<', $currentDate)
                                  ->with(['customer_details'])
                                  ->orderBy('id','desc')
                                  ->take(5)
                                  ->get()->toArray();

            // $past_bookings = array_map(function ($record) {



            //     $restaurant_details = $record['restaurant_details'];
            //     $images = collect($restaurant_details['images'])->pluck('restaurant_image')->toArray();
            //     $record['restaurant_details']['images'] = $images;
            //     return $record;
            // }, $past_bookings);                         

            $current_bookings = $user->business_restaurant_table_booking_list()
                                     ->where('status',RestaurantTableBooking::BOOKING_CONFIRMED)
                                     ->whereDate('booking_from_date_time', $currentDate)
                                     ->with(['customer_details'])
                                     ->orderBy('id','desc')
                                     ->take(5)
                                      ->get()->toArray();

            // $current_bookings = array_map(function ($record) {

            //     $restaurant_details = $record['restaurant_details'];
            //     $images = collect($restaurant_details['images'])->pluck('restaurant_image')->toArray();
            //     $record['restaurant_details']['images'] = $images;
            //     return $record;
            // }, $current_bookings);   

            $upcoming_bookings = $user->business_restaurant_table_booking_list()
                                      ->where('status',RestaurantTableBooking::BOOKING_CONFIRMED)
                                      ->whereDate('booking_from_date_time', '>', $currentDate)
                                      ->with(['customer_details'])
                                      ->orderBy('id','desc')
                                      ->take(5)
                                       ->get()->toArray();

            // $upcoming_bookings = array_map(function ($record) {

            //     $restaurant_details = $record['restaurant_details'];
            //     $images = collect($restaurant_details['images'])->pluck('restaurant_image')->toArray();
            //     $record['restaurant_details']['images'] = $images;
            //     return $record;
            // }, $upcoming_bookings); 

            $data = [

                [
                    'type'     => 'past bookings',
                    'bookings' => $past_bookings
                ],
                [
                    'type'     => 'current bookings',
                    'bookings' => $current_bookings
                ],
                [
                    'type'     => 'upcoming bookings',
                    'bookings' => $upcoming_bookings
                ]
           ];
           return $this->returnSuccessResponse('booking listing', $data);

        } catch (Exception $exception) {
            report($exception);
            return $this->returnErrorResponse($exception->getMessage());
        }
    }

    /**
     * **************************************************************
     *  BUSINESS ACCEPT REJECT BOOKING
     * **************************************************************
     * */
    public function accept_reject_booking(BookingConfirmRejectRequest $request)
    {

        try {

            $data = $request->all(); // Get request data
            $user = auth()->user(); // Get the user

            $booking = RestaurantTableBooking::where('booking_number', $data['booking_number'])->first();
            $customer_details = $booking->customer_details;

            if ($booking) {

                if (RestaurantTableBooking::checkIsBusinessBooking($booking->id, $user->id)) {

                    $booking->status = $data['status'];

                    if ($booking->save()) {
                        $message = 'booking accepted successfully.';
                        if ($booking->status == RestaurantTableBooking::BOOKING_CANCELLED) {
                            $message = 'booking cancelled successfully.';
                        }

                        if($customer_details->isLoggedIn() == '1') {

                            if (filled($customer_details->fcm_token)) {
                                
                                $notificationMessage = ''; 
                                if(($booking->status == RestaurantTableBooking::BOOKING_CANCELLED)) {
                                    $notificationMessage = 'Your booking request is cancelled';
                                }
                                else {
                                    $notificationMessage = 'Your booking request is confirmed';
                                }
                                
                                $additionalData = [
                                    'user_id'          => $customer_details->id,
                                    'title'            =>  'Booking',
                                    'body'             =>  $notificationMessage,
                                    'type'             =>  1,
                                    'role'             => 'customer',
                                    'notificationType' => 'booking_approval',
                                    'booking_number'   => $booking->booking_number,
                                    'restaurant_id'    => $booking->restaurant_id
                                ];
            
                                CommonHelper::sendCurlPushNotification($customer_details->fcm_token, $additionalData);
                            }
                        }

                        return $this->returnSuccessResponse($message);
                    }

                    return $this->returnErrorResponse('You are not authorized for this booking');
                }

                return $this->returnErrorResponse('You are not authorized for this booking');
            }

            return $this->returnErrorResponse('booking not found.');
        } catch (Exception $exception) {
            report($exception);
            return $this->returnErrorResponse($exception->getMessage());
        }
    }
}
