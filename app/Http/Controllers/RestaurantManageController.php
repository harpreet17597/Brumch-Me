<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Carbon;
use App\Models\RestaurantTableBooking;

class RestaurantManageController extends Controller
{
    use ApiResponser;
    protected $path = 'backend.frontend-restaurant.';


    public function __construct()
    {  
    
    }

    /**
     * **************************************************************
     *  Business-List
     * **************************************************************
     * */
    public function all_restaurant_table_bookings(Request $request)
    {
        $booking_type = 'All';
        if($request->has('status')) {

            if(in_array($request->get('status'),[
                RestaurantTableBooking::BOOKING_PENDING,
                RestaurantTableBooking::BOOKING_CANCELLED,
                RestaurantTableBooking::BOOKING_CONFIRMED])) 
            {
                $booking_type = strtoupper($request->get('status'));
            }
        }
   
        return view($this->path .'all-table-bookings',compact('booking_type'));
    }


    /**
     * **************************************************************
     *  Business-Datatable
     * **************************************************************
     * */
    public function restaurant_table_bookings_datatable(Request $request)
    {
        $data = RestaurantTableBooking::with(['customer_details','restaurant_details'])->latest();
        
        if($request->has('status') && in_array($request->get('status'),[
            RestaurantTableBooking::BOOKING_PENDING,
            RestaurantTableBooking::BOOKING_CANCELLED,
            RestaurantTableBooking::BOOKING_CONFIRMED])) 
        {
           $data = $data->where('status',$request->get('status'));
        }

        $data = $data->get();

        $dataTable = DataTables::of($data)
        ->addColumn('status', function ($data) {
            $button = '';
            if($data->status == RestaurantTableBooking::BOOKING_PENDING) {
                $button = '<span class=" btn btn-warning btn-xs">'.$data->status.'<span>';
            }
            if($data->status == RestaurantTableBooking::BOOKING_CANCELLED) {
                $button = '<span class=" btn btn-danger btn-xs">'.$data->status.'<span>';
            }
            if($data->status == RestaurantTableBooking::BOOKING_CONFIRMED) {
                $button = '<span class=" btn btn-success btn-xs">'.$data->status.'<span>';
            }
            return $button;
        })
        ->rawColumns(['status'])
        ->make(true);

        return $dataTable;
    }
}

