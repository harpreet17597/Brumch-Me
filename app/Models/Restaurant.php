<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\RestaurantImage;
use App\Models\RestaurantMenu;
use App\Helpers\CommonHelper;
use App\Models\User;
use App\Models\Tag;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\BusinessAvailabilityAndTimeSlot;

class Restaurant extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'restaurant_name', 'restaurant_description', 'restaurant_latitude', 'restaurant_longitude', 'restaurant_address', 'restaurant_opening_time', 'restaurant_closing_time'];
    
    // Define the scope for active products
    public function scopeActive($query)
    {
        return $query->where('active_status', '1');
    }

    /**
     * **************************************************************
     * RESTAURANT IMAGES 
     * **************************************************************
     * */
    public function images()
    {
        return $this->hasMany(RestaurantImage::class, 'restaurant_id');
    }

    /**
     * **************************************************************
     * RESTAURANT MENU'S 
     * **************************************************************
     * */
    public function menus()
    {
        return $this->hasMany(RestaurantMenu::class, 'restaurant_id');
    }

    /**
     * **************************************************************
     * RESTAURANT BUSINESS DETAILS
     * **************************************************************
     * */
    public function business_detail()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * **************************************************************
     * RESTAURANT TAGS
     * **************************************************************
     * */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'restaurant_tags', 'restaurant_id', 'tag_id')->withPivot('tag_id');
    }

    /**
     * **************************************************************
     * IS FAVORITE RESTAURANT
     * **************************************************************
     * */
    public function is_favorite($user_id)
    {

        $record = DB::table('restaurant_wishlists')->where('user_id', $user_id)->where('restaurant_id', $this->id)->first();
        if ($record) {
            return true;
        }
        return false;
    }

    /**
     * **************************************************************
     *  ACCESSOR TO GET ACTUAL STARTING TIME 
     * **************************************************************
     * */
    public function getRestaurantOpeningTimeAttribute($value)
    {
        $restaurant = $this;
        $business_detail = $restaurant->business_detail;
        if ($business_detail) {
            $currentDate = Carbon::now()->format('Y-m-d');
            $today_available_slot = BusinessAvailabilityAndTimeSlot::where(['business_id' => $business_detail->id, 'availability_date' => $currentDate])->first();
            if ($today_available_slot) {
                return $today_available_slot->time_slot_from;
            }
        }
    
        return $value;
    }

    /**
     * **************************************************************
     *  ACCESSOR TO GET ACTUAL CLOSING TIME 
     * **************************************************************
     * */
    public function getRestaurantClosingTimeAttribute($value)
    {
        $restaurant = $this;
        $business_detail = $restaurant->business_detail;
        if ($business_detail) {
            $currentDate = Carbon::now()->format('Y-m-d');
            $today_available_slot = BusinessAvailabilityAndTimeSlot::where(['business_id' => $business_detail->id, 'availability_date' => $currentDate])->first();
            if ($today_available_slot) {
                return $today_available_slot->time_slot_to;
            }
        }
       
        return $value;
    }

    /**
     * **************************************************************
     * JSON RESPONSE
     * **************************************************************
     * */
    public function jsonResponse()
    {
        $json["id"]                      = $this->id;
        $json["user_id"]                 = $this->user_id;
        $json["restaurant_name"]         = $this->restaurant_name;
        $json["restaurant_description"]  = $this->restaurant_description;
        $json["restaurant_address"]      = $this->restaurant_address;
        $json["restaurant_opening_time"] = $this->restaurant_opening_time;
        $json["restaurant_closing_time"] = $this->restaurant_closing_time;
        $json["restaurant_latitude"]     = $this->restaurant_latitude;
        $json["restaurant_longitude"]    = $this->restaurant_longitude;
        $json["restaurant_rating"]       = $this->restaurant_rating;
        $json["has_dress_code"]          = $this->has_dress_code;
        $json["dress_code"]              = $this->dress_code;
        if (auth()->check()) {
            $json["is_favorite"] = $this->is_favorite(auth()->user()->id);
            $tags = $this->tags;
            if ($tags->count() > 0) {
                $json["tags"] = $tags->map(function ($item) {
                    return ['id' => $item->id,'name' => $item->name,'is_selected' => true];
                });           
         }
        }
        $images = $this->images;
        if ($images->count() > 0) {
            $json["images"] = $images->pluck('restaurant_image');
        }
        $json["menus"] = $this->menus;

        return $json;
    }
}
