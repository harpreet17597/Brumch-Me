<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestaurantTableBooking extends Model
{
    use HasFactory;
    protected $fillable = ['booking_number','customer_id','business_id','restaurant_id','booking_from_date_time','booking_to_date_time','number_of_persons','status'];

    const BOOKING_PENDING   = 'pending';
    const BOOKING_CANCELLED = 'cancelled';
    const BOOKING_CONFIRMED = 'confirmed';

    public function customer_details() {
        return $this->belongsTo(User::class,'customer_id');
    }

    public function business_details() {
        return $this->belongsTo(User::class,'business_id');
    }

    public function restaurant_details() {
        return $this->belongsTo(Restaurant::class,'restaurant_id','id');
    }

    public static function checkIsBusinessBooking($booking_id,$business_id) {
    
        $booking = self::where('id',$booking_id)->first();
        if($booking) {
           if($booking->business_id == $business_id) {
               return true;
           }
        }
        return false;
    }

}
