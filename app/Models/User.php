<?php

namespace App\Models;

use App\Interfaces\Auth\MustVerifyPhone;
use App\Traits\Auth\MustVerifyPhone as PhoneVerifiedTraits;
use App\Traits\Auth\MustVerifyProfile as ProfileVerifiedTraits;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;
use App\Helpers\CommonHelper;
use App\Models\Restaurant;
use App\Models\RestaurantWishlist;
use App\Models\RestaurantTableBooking;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class User extends Authenticatable implements MustVerifyPhone
{
    use HasApiTokens, 
        HasFactory, 
        Notifiable,
        HasRoles,
        PhoneVerifiedTraits,
        ProfileVerifiedTraits;

    protected $table = 'users';
    protected $imageFolderPath = '/uploads/avatar/';
    
    // const VERIFIED_USER   = '1';
    // const UNVERIFIED_USER = '0';
    // const ADMIN_ROLE      = 'admin';
    // const USER_ROLE       = 'user';
    // const VENDOR_ROLE     = 'vendor';
    // const ACTIVE_USER     = '1';
    // const INACTIVE_USER   = '0';
    
    /**
     * Profile status constants.
    */
    public const STATUS_ACCOUNT_CREATED   = 1;
    public const STATUS_PROFILE_COMPLETED = 2;
    public const STATUS_PROFILE_DELETED   = 3;
    public const STATUS_DELETED           = 4;

    public const ACCOUNT_UNSUSPENED   = 0;
    public const ACCOUNT_SUSPENED     = 1;
    public const ACCOUNT_UNDER_REVIEW = 2;

    /**
     * Gender constats
    */
    public const GENDER_MALE   = 1;
    public const GENDER_FEMALE = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
    */
    protected $fillable = [

           'name',
           'display_name',
           'dob',
           'email',
           'email_verified_at',
           'email_verify_token',
           'username',
           'password',
           'phone_country',
           'country_code',
           'phone',
           'phone_verified_at',
           'timezone',
           'profile_image',
           'profile_background',
           'about',
           'user_type',
           'is_verified',
           'verified_at',
           'is_suspended',
           'suspended_at',
           'account_status',
           'country_id',
           'state_id',
           'country',
           'state',
           'suburb',
           'city',
           'post_code',
           'current_location',
           'street_address',
           'lat',
           'lng',
           'terms_condition',
           'app_id',
           'device_os',
           'apple_id',
           'fcm_token',
           'last_seen',
           'restaurant_opening_time',
           'restaurant_closing_time',
           'restaurant_max_table',

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function generateVerificationToken() {
        return Str::random(40);
    }

     /**
     * Generate OTP for the user.
     *
     * @return int
     */
    public function generateSendOtp()
    {
        $phone = $this->phone_country . $this->phone;
        $smsOtp = CommonHelper::generateOtp($this->phone_country, $this->phone);
        CommonHelper::sendSms($smsOtp, $phone);

        return $smsOtp->otp;
    }

    public function updateCustomerStatus($id, $status)
    {
        $user = self::where('id', $id)->first();
        $user->account_status = $status;
    }

    public function updateProfileImage($id,$file)
    {
        $filename = CommonHelper::uploadImage('avatar/',$file);
        self::where('id', $id)->update(['profile_image' => $filename]);
        
    }

    public function deleteProfileImage() {
    
        $obj       = new User();
        $folder    = $obj->imageFolderPath;
        $arr       = explode('/', $this->profile_image);
        $filename  = end($arr);
        $file_path = public_path($folder.$filename);
    
        if (File::exists($file_path)) {
            File::delete($file_path);
        }
    }

    public function role() {
        return $this->user_type;
    }

    public function restaurant() {
        return $this->hasOne(Restaurant::class,'user_id');
    }

    public function jsonResponse(){
         
            $json['id']                 = $this->id;
            $json['user_type']          = $this->user_type;
            $json['name']               = $this->name;
            $json['dob']                = $this->dob;
            $json['email']              = $this->email;
            $json['email_verified_at']  = $this->email_verified_at;
            $json['phone_country']      = $this->phone_country;
            $json['country_code']       = $this->country_code;
            $json['phone']              = $this->phone;
            $json['phone_verified_at']  = $this->phone_verified_at;
            $json['profile_image']      = $this->profile_image;
            $json['is_suspended']       = $this->is_suspended;
            $json['suspended_at']       = $this->suspended_at;
            $json['street_address']     = $this->street_address;
            $json['lat']                = $this->lat;
            $json['lng']                = $this->lng;
            $json['is_verified']        = $this->is_verified;
            $json['verified_at']        = $this->verified_at;
            $json['created_at']         = $this->created_at->toDateTimeString();
            $json['updated_at']         = $this->updated_at->toDateTimeString();
            if($json['user_type'] == 'business') {
                $json['restaurant_opening_time'] = $this->restaurant_opening_time;
                $json['restaurant_closing_time'] = $this->restaurant_closing_time;
                $json['restaurant_max_table']    = $this->restaurant_max_table;
            }

            return $json;
    }

    public static function getUserByPhone($phone) {
        return self::where('phone',$phone)->first();
    }

    /**
     * **************************************************************
     *  ACCESSOR TO GET FULL IMAGE PATH
     * **************************************************************
    * */
    public function getProfileImageAttribute($value) {
        if(!is_null($value) && !empty($value)) {
           
            $folder = $this->imageFolderPath;
            $file_path = public_path($folder.$value);
            if (File::exists($file_path)) {
                return asset($folder.$value);    
            }
        }
        return asset('uploads/no-image.png');
    }

    /**
     * **************************************************************
     * USER WISHLIST RESTAURANTS (ONE TO MANY RELATION)
     * **************************************************************
    * */
    public function wishlist_restaurants() {
        return $this->hasMany(RestaurantWishlist::class,'user_id');
    }

    /**
     * **************************************************************
     * BUSINESS RESTAURANTS TABLE BOOKING LIST
     * **************************************************************
    * */
    public function business_restaurant_table_booking_list() {

        return $this->hasMany(RestaurantTableBooking::class,'business_id')->orderBy('id','desc');;
    }

    /**
     * **************************************************************
     * CUSTOMER RESTAURANTS TABLE BOOKING LIST
     * **************************************************************
    * */
    public function customer_restaurant_table_booking_list() {

        return $this->hasMany(RestaurantTableBooking::class,'customer_id')->orderBy('id','desc');;
    }

    /**
     * **************************************************************
     * GET USER SUBSCRIPTION
     * **************************************************************
    * */
    public function subscription()
    {
        return $this->hasOne(UserSubscription::class,'user_id')->latest();
    }

    /**
     * **************************************************************
     * GET USER SUBSCRIPTION
     * **************************************************************
    * */
    public function subscription_status()
    {
        return $this->hasOne(SubscriptionStatus::class,'user_id')->where('status','active')->latest();
    }

    public function featured_subscription_status() {
        return $this->hasOne(UserFeaturedSubscriptionStatus::class,'user_id')->latest();
    }
    
}
