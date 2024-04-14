<?php

namespace App\Http\Controllers\Api;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use App\Models\User;
use App\Traits\ApiMethodsTrait;
use Exception;
use App\Traits\ApiResponser;
use App\Models\RestaurantImage;
use Illuminate\Support\Facades\Storage;
use App\Models\RestaurantMenu;
use App\Http\Requests\Api\RestaurantWishlistRequest;
use App\Http\Requests\Api\AvailabilityTimeSlotRequest;
use DB;
use App\Http\Requests\Api\RestaurantListingRequest;
use App\Http\Requests\Api\RestaurantSaveRequest;
use App\Models\Business;
use App\Models\BusinessAvailabilityAndTimeSlot;
use App\Models\Tag;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use App\Models\UserFeaturedSubscriptionStatus;
use App\Models\BannerImage;

class RestaurantController extends Controller
{
    use ApiMethodsTrait, ApiResponser;

    /**
     * **************************************************************
     *  RESTAURANT LISTING
     * **************************************************************
     * */
    public function restaurant_listing(RestaurantListingRequest $request)
    {

        try {

            /**
             * GET ALL BUSINESS HAVING RESTAURANT
             */
            $users = User::where(['user_type' => 'business', 'is_verified' => '1', 'is_suspended' => '0'])
                ->whereHas('restaurant');

            if ($users->count() > 0) {

                /*Filter users having free trial peroid otherwise have subscription plan*/
                $users = $users->get()->filter(function($user) {
                       
                            if($user->has_free_trial == '1') {return true;}
                            if($user->subscription_status) {return true;}
                            return false;
                            
                        });
        
                $restaurants = Restaurant::active()->whereIn('user_id', $users->pluck('id'))
                    ->with(['images', 'menus']);

                /**
                 * FILTER BUSINESS BASED ON LATITUDE OR LONGITUDE
                 */
                if ($request->has('type')) {

                    if ($request->get('type') == 'nearby') {

                        if ($request->has('latitude') && $request->has('longitude')) {

                            if (!empty($request->get('latitude')) && !empty($request->get('longitude'))) {

                                $latitude  = $request->get('latitude');
                                $longitude = $request->get('longitude');
                                $restaurants = $restaurants->select(DB::raw('*, ( 6367 * acos( cos( radians(' . $latitude . ') ) * cos( radians( restaurant_latitude ) ) * cos( 
                                    radians( restaurant_longitude ) - radians(' . $longitude . ') ) + sin( radians(' . $latitude . ') ) * sin( radians( restaurant_latitude ) ) ) ) AS 
                                    distance'))
                                    ->orderBy('distance', 'asc');
                            }
                        }
                    }

                    if ($request->get('type') == 'recomended') {
                        $restaurants = $restaurants->inRandomOrder();
                    }
                }

                $restaurants = $restaurants->get()->toArray();
                

                /**
                 * RESTAURANT FILTER BASED ON SEARCHING
                 */
                $search = $request->get('search');
                if (!is_null($search) && !empty($search)) {
                    $search = trim(strtolower($search));

                    $restaurants = array_filter($restaurants, function ($record) use ($search) {

                        if (stristr($record['restaurant_name'], $search)) {
                            return true;
                        }
                        // if (stristr($record['restaurant_description'], $search)) {
                        //     return true;
                        // }
                        return false;
                    });
                }

                /**
                 * RESTAURANT MAPPING IMAGES AND MENU'S
                 */
                $restaurants = array_map(function ($record) {
                    $images = collect($record['images'])->pluck('restaurant_image')->toArray();
                    $record['images'] = $images;
                    return $record;
                }, $restaurants);
                

                $restaurants = collect($restaurants);

                /**
                 * RESTAURANT PAGINATION
                 */
                $result = $this->paginate($restaurants);
                return $this->returnSuccessResponse('Restaurant listing', $result);
            }

            return $this->returnSuccessResponse('No Restaurant Found!', []);
        } catch (Exception $exception) {
            report($exception);
            return $this->returnErrorResponse($exception->getMessage());
        }
    }

    /**
     * **************************************************************
     *  GET MY RESTAURANT DETAIL
     * **************************************************************
     * */
    public function restaurant_get(Request $request)
    {
        try {
            $restaurant_id = auth()->user()->restaurant->id;
            if ($restaurant_id) {

                $restaurant = Restaurant::where('id', $restaurant_id)->first();
                if ($restaurant) {
                    $jsonResponse = $restaurant->jsonResponse();
                    $jsonResponse['business_detail'] = $restaurant->business_detail;
                    return $this->returnSuccessResponse('Restaurant Detail', $jsonResponse);
                }
            }
            return $this->returnErrorResponse('Restaurant does not found.');
        } catch (Exception $exception) {
            report($exception);
            return $this->returnErrorResponse($exception->getMessage());
        }
    }

    /**
     * **************************************************************
     *  GET  RESTAURANT DETAIL
     * **************************************************************
     * */
    public function restaurant_detail(Request $request)
    {
        $rules = [
            'restaurant_id' => 'required|integer'
        ];
        $this->validate($request, $rules);
        try {
            $restaurant_id = $request->get('restaurant_id');
            if ($restaurant_id) {
                $restaurant = Restaurant::where('id', $restaurant_id)->first();
                if ($restaurant) {
                    $jsonResponse = $restaurant->jsonResponse();
                    $jsonResponse['business_detail'] = $restaurant->business_detail;
                    return $this->returnSuccessResponse('Restaurant Detail', $jsonResponse);
                }
            }
            return $this->returnErrorResponse('Restaurant does not found.');
        } catch (Exception $exception) {
            report($exception);
            return $this->returnErrorResponse($exception->getMessage());
        }
    }

    /**
     * **************************************************************
     *  SAVE RESTAURANT DETAIL
     * **************************************************************
     * */
    public function restaurant_post(RestaurantSaveRequest $request)
    {

       // try {
            $user = null;

            // dd(auth()->user());

            if ($request->has('business_id')) {
                $business_id = $request->get('business_id');
                if (!empty($business_id) && !is_null($business_id)) {
                    $user = Business::where('id', $request->get('business_id'))->first();
                }
            }
            else {

                $token = $request->bearerToken();
                $user =  auth('sanctum')->user();
            }


            // Retrieve the Sanctum token from the request
            //    $token = $request->bearerToken();
            //    //    dd($token);

            //     if($token) {
            //        // Find the user associated with the token
            //          $user = User::where('api_token', $token)->first();

            //          dd($user);
            //     }

            if (!$user) {
                return $this->notAuthorizedResponse('You are not authorized');
            }

            $data = $request->all();
            $restaurant = $user->restaurant;

            if ($user) {
                if($user->account_status == User::STATUS_PROFILE_DELETED) {
                    return $this->returnErrorResponse('Your account is deactivated.');
                } 
                
                if ($user->isProfileSuspended()) {
                    return $this->returnErrorResponse('Your profile is suspended please contact admin.');
                }
                // if($user->role() == 'business') {
                //     if(!$user->hasVerifiedProfile()){
                //         return $this->returnErrorResponse('Your profile is not approved yet.');
                //     }
                // }
            }

            if ($restaurant === null) {
                /*create restaurant*/
                $restaurant = Restaurant::create([

                    'user_id'                 => $user->id,
                    'restaurant_name'         => $data['restaurant_name'],
                    'restaurant_description'  => $data['restaurant_description'] ?? '',
                    'restaurant_latitude'     => $user->lat ?? null,
                    'restaurant_longitude'    => $user->lng ?? null,
                    'restaurant_address'      => $user->street_address,
                    'restaurant_opening_time' => $user->restaurant_opening_time ?? null,
                    'restaurant_closing_time' => $user->restaurant_closing_time ?? null,
                    'has_dress_code'          => $r = (isset($data['has_dress_code']) && $data['has_dress_code'] =='yes') ? 'yes' : 'no',
                    'dress_code'              => ($r == 'yes' && isset($data['has_dress_code'])) ? $data['dress_code'] : null

                ]);
            } else {   /*update restaurant*/
                $restaurant->restaurant_name        = $data['restaurant_name'];
                $restaurant->restaurant_description = $data['restaurant_description'] ?? '';
                $restaurant->has_dress_code         = $r= (isset($data['has_dress_code']) && $data['has_dress_code'] == 'yes') ? 'yes' : 'no';
                $restaurant->dress_code             = ($r == 'yes' && isset($data['has_dress_code'])) ? $data['dress_code'] : null;

                $restaurant->save();
            }
            /*restaurant tags*/
            if (isset($data['tags']) && is_array($data['tags']) && count($data['tags'])) {
                $restaurant->tags()->sync($data['tags'], true);
            }

            /*restaurant images*/
            if ($request->has('restaurant_images')) {

                if ($restaurant->images->count() > 0) {

                    $restaurant->images->each(function ($record) {
                        /*check file exist*/
                        $arr = explode('/', $record->restaurant_image);
                        $filename = end($arr);
                        $file_path = 'uploads/restaurants/' . $filename;
                        $file_path = public_path($file_path);
                        if (File::exists($file_path)) {
                            File::delete($file_path);
                        }
                        // Storage::disk(config('constants.image_path.driver'))->delete($file_path);
                        $record->delete();
                    });
                }

                foreach ($request->file('restaurant_images') as $restaurant_image) {

                    $path = CommonHelper::uploadImage('restaurants/', $restaurant_image);
                    $newrestaurantImage = new RestaurantImage([
                        'restaurant_id'    => $restaurant->id,
                        'restaurant_image' => $path
                    ]);
                    $newrestaurantImage->save();
                }
            }
            /*restaurant menu*/
            if ($request->has('restaurant_menus')) {

                $menus = $request->get('restaurant_menus');

                $restaurant->menus->each(function ($record) {
                    /*check file exist*/
                    $arr = explode('/', $record->restaurant_menu_image);
                    $filename = end($arr);
                    $file_path = 'uploads/restaurants/menus/' . $filename;
                    $file_path = public_path($file_path);
                    if (File::exists($file_path)) {
                        File::delete($file_path);
                    }
                    // Storage::disk(config('constants.image_path.driver'))->delete($file_path);
                    $record->delete();
                });

                if (!empty($menus) && !is_null($menus)) {

                    $menu_images = $request->has('menu_images') ? $request->file('menu_images') : null;

                    foreach ($menus as $menu_key => $menu) {

                        $restaurant_menu_image_path = null;
                        if (!is_null($menu_images) && !empty($menu_images)) {

                            if (isset($menu_images[$menu_key]) && !empty($menu_images[$menu_key]) && !is_null($menu_images[$menu_key])) {

                                $restaurant_menu_image_path = CommonHelper::uploadImage('restaurants/menus/', $menu_images[$menu_key]);
                            }
                        }

                        $newrestaurantMenu = new RestaurantMenu([
                            'restaurant_id'               => $restaurant->id,
                            'restaurant_menu_name'        => $menu['name'],
                            'restaurant_menu_price'       => $menu['price'],
                            'restaurant_menu_quantity'    => $menu['quantity'],
                            'restaurant_menu_description' => $menu['description'],
                            'restaurant_menu_image'       => $restaurant_menu_image_path
                        ]);

                        $newrestaurantMenu->save();
                    }
                }
            }

            if (!$user->hasVerifiedProfile()) {
                return $this->returnSuccessResponse('Please wait for admin to approve your profile.');
            }
            return $this->returnSuccessResponse('Restaurant detail updated successfully.');
        // } catch (Exception $exception) {
        //     report($exception);
        //     return $this->returnErrorResponse($exception->getMessage());
        //     // return $this->respondInternalError($exception->getMessage());
        // }
    }

    /**
     * **************************************************************
     *  ADD-REMOVE RESTAURANT FROM WISHLIST
     * **************************************************************
     * */
    public function restaurant_wishlist(RestaurantWishlistRequest $request)
    {

        try {

            $data    = $request->all(); // Get request data
            $user    = auth()->user(); // Get the user
            $restaurant = Restaurant::where('id', $data['restaurant_id'])->first();
            if (!$restaurant) {
                return $this->returnErrorResponse('Restaurant not found!');
            }
            $record  = $user->wishlist_restaurants->where('restaurant_id', $restaurant->id)->first(); //get record if already added to wishlist
            $message = '';
            if ($record) {
                $record->delete();
                $message = 'Restaurant removed from wishlist.';
            } else {
                $user->wishlist_restaurants()->firstOrCreate([
                    'restaurant_id' => $data['restaurant_id']
                ]);
                $message = 'Restaurant added to wishlist.';
            }

            return $this->returnSuccessResponse($message, $restaurant->jsonResponse());
        } catch (Exception $exception) {
            report($exception);
            return $this->returnErrorResponse($exception->getMessage());
        }
    }

    /**
     * **************************************************************
     *  ADD-RESTAURANT AVAILABILITY TIME SLOTS
     * **************************************************************
     * */
    public function availability_time_slots(AvailabilityTimeSlotRequest $request)
    {

        try {

            $data = $request->all();
            $user = auth()->user();

            $record = BusinessAvailabilityAndTimeSlot::where(['business_id' => $user->id, 'availability_date' => $data['availability_date']])->first();
            if ($record) {
                $record->fill([
                    'time_slot_from'    => $data['time_slot_from'],
                    'time_slot_to'      => $data['time_slot_to']
                ])->update();
            } else {

                $record = BusinessAvailabilityAndTimeSlot::create([
                    'business_id'       => $user->id,
                    'availability_date' => $data['availability_date'],
                    'time_slot_from'    => $data['time_slot_from'],
                    'time_slot_to'      => $data['time_slot_to']
                ]);
            }
            if ($record) {
                return $this->returnSuccessResponse('record saved successfully.', $record);
            }
            return $this->returnErrorResponse('error while saving record');
        } catch (Exception $exception) {
            report($exception);
            return $this->returnErrorResponse($exception->getMessage());
        }
    }

    /**
     * **************************************************************
     *  Restaurant List Tag Wise
     * **************************************************************
     * */
    public function restaurant_tags_listingnew(RestaurantListingRequest $request) {

    
        try {

            /**
             * GET ALL BUSINESS HAVING RESTAURANT
             */
            $users = User::where(['user_type' => 'business', 'is_verified' => '1', 'is_suspended' => '0'])
                ->whereHas('restaurant');

            if ($users->count() > 0) {
                $users = $users->get();

                $restaurants = Restaurant::whereIn('user_id', $users->pluck('id'));

                if ($request->has('tag')) {
                    $tag = $request->get('tag');
                    $restaurants = $restaurants->whereHas('tags', function ($query) use ($tag) {
                        $query->where('tag_id', $tag);
                    });
                } else {
                    $restaurants = $restaurants->whereHas('tags');
                }

                $restaurants = $restaurants->with(['tags', 'images', 'menus']);


                /**
                 * FILTER BUSINESS BASED ON LATITUDE OR LONGITUDE
                 */
                if ($request->has('type')) {

                    if ($request->get('type') == 'nearby') {

                        if ($request->has('latitude') && $request->has('longitude')) {

                            if (!empty($request->get('latitude')) && !empty($request->get('longitude'))) {

                                $latitude  = $request->get('latitude');
                                $longitude = $request->get('longitude');
                                $restaurants = $restaurants->select(DB::raw('*, ( 6367 * acos( cos( radians(' . $latitude . ') ) * cos( radians( restaurant_latitude ) ) * cos( 
                                    radians( restaurant_longitude ) - radians(' . $longitude . ') ) + sin( radians(' . $latitude . ') ) * sin( radians( restaurant_latitude ) ) ) ) AS 
                                    distance'))
                                    ->orderBy('distance', 'asc');
                            }
                        }
                    }

                    if ($request->get('type') == 'recomended') {
                        $restaurants = $restaurants->inRandomOrder();
                    }
                }

                $restaurants = $restaurants->get()->toArray();

                /**
                 * RESTAURANT FILTER BASED ON SEARCHING
                 */
                $search = $request->get('search');
                if (!is_null($search) && !empty($search)) {
                    $search = trim(strtolower($search));

                    $restaurants = array_filter($restaurants, function ($record) use ($search) {

                        if (stristr($record['restaurant_name'], $search)) {
                            return true;
                        }
                        // if (stristr($record['restaurant_description'], $search)) {
                        //     return true;
                        // }
                        return false;
                    });
                }

                /**
                 * RESTAURANT MAPPING IMAGES AND MENU'S
                 */
                $restaurants = array_map(function ($record) {
                    $images = collect($record['images'])->pluck('restaurant_image')->toArray();
                    $record['images'] = $images;
                    return $record;
                }, $restaurants);

                $result = null;

                if (!$request->has('tag')) {
                    /**
                     * RESTAURANT MAPPING TAG WISE
                     */
                    $tagsArr = Tag::select('id', 'name')->get()->toArray();

                    $tagsArr = array_map(function ($record) {

                        $record['restaurants'] = [];
                        return $record;
                    }, $tagsArr);

                    foreach ($restaurants as $restaurant) {

                        foreach ($restaurant['tags'] as $tag) {
                            
                            $index = null;
                            
                            foreach($tagsArr as $i => $tArr) {

                                if($tArr['id'] == $tag['id']) {
                                    $index = $i;
                                    break;
                                }
                            }

                            if ($index) {
                                unset($restaurant['tags']);
                                if (isset($tagsArr[$index]['restaurants'])) {
                                    if(count($tagsArr[$index]['restaurants']) <= 2) {
                                        array_push($tagsArr[$index]['restaurants'], $restaurant);
                                    }
                                } else {
                                    $tagsArr[$index]['restaurants'][] = $restaurant;
                                }
                            }
                        }
                    }

                    $result = $tagsArr;

                    if ($request->has('type')) {
                        if ($request->get('type') == 'nearby') {
                            if ($request->has('latitude') && $request->has('longitude')) {
                                $newRequest = new RestaurantListingRequest();
                                $newRequest->merge([
                                    'type' => 'nearby',
                                    'latitude' => $request->get('latitude'),
                                    'longitude' => $request->get('longitude')
                                ]);
                                $response = $this->restaurant_listing($newRequest);
                                $dataRes  = $response->getData();
                                if($dataRes){
                                    $arr = $dataRes->data->data;
                                    array_unshift($result,['id' => -1,'name' => 'Restaurants near you','restaurants' => $arr]);    
                                }

                            }
                        }   
                    } 
                    
                } else {

                    $restaurants = array_map(function ($record) {
                        $record['tags'] = array_map(function ($r) {
                            return ['id' => $r['id'], 'name' => $r['name']];
                        }, $record['tags']);
                        return $record;
                    }, $restaurants);
                }

                if ($request->has('tag')) {
                    // $restaurants 
                    $restaurants = collect($restaurants);
                    /*** RESTAURANT PAGINATION*/
                    $result = $this->paginate($restaurants);
                }
                return $this->returnSuccessResponse('Restaurant listing', $result);
            }

            return $this->returnSuccessResponse('No Restaurant Found!', []);
        } catch (Exception $exception) {
            report($exception);
            return $this->returnErrorResponse($exception->getMessage());
        }
    }

    public function restaurant_tags_listing_new_old(Request $request)
    {
        
        $this->validate($request, ['tag' => 'sometimes|exists:tags,id']);
        try {

            /**
             * GET ALL BUSINESS HAVING RESTAURANT
             */
            $users = User::where(['user_type' => 'business', 'is_verified' => '1', 'is_suspended' => '0'])
                ->whereHas('restaurant');

            if ($users->count() > 0) {
                $users = $users->get();

                $restaurants = Restaurant::whereIn('user_id', $users->pluck('id'));

                if ($request->has('tag')) {
                    $tag = $request->get('tag');
                    $restaurants = $restaurants->whereHas('tags', function ($query) use ($tag) {
                        $query->where('tag_id', $tag);
                    });
                } else {
                    $restaurants = $restaurants->whereHas('tags');
                }

                $restaurants = $restaurants->with(['tags', 'images', 'menus']);


                /**
                 * FILTER BUSINESS BASED ON LATITUDE OR LONGITUDE
                 */
                if ($request->has('type')) {

                    if ($request->get('type') == 'nearby') {

                        if ($request->has('latitude') && $request->has('longitude')) {

                            if (!empty($request->get('latitude')) && !empty($request->get('longitude'))) {

                                $latitude  = $request->get('latitude');
                                $longitude = $request->get('longitude');
                                $restaurants = $restaurants->select(DB::raw('*, ( 6367 * acos( cos( radians(' . $latitude . ') ) * cos( radians( restaurant_latitude ) ) * cos( 
                                    radians( restaurant_longitude ) - radians(' . $longitude . ') ) + sin( radians(' . $latitude . ') ) * sin( radians( restaurant_latitude ) ) ) ) AS 
                                    distance'))
                                    ->orderBy('distance', 'asc');
                            }
                        }
                    }

                    if ($request->get('type') == 'recomended') {
                        $restaurants = $restaurants->inRandomOrder();
                    }
                }

                $restaurants = $restaurants->get()->toArray();

                /**
                 * RESTAURANT FILTER BASED ON SEARCHING
                 */
                $search = $request->get('search');
                if (!is_null($search) && !empty($search)) {
                    $search = trim(strtolower($search));

                    $restaurants = array_filter($restaurants, function ($record) use ($search) {

                        if (stristr($record['restaurant_name'], $search)) {
                            return true;
                        }
                        // if (stristr($record['restaurant_description'], $search)) {
                        //     return true;
                        // }
                        return false;
                    });
                }

                /**
                 * RESTAURANT MAPPING IMAGES AND MENU'S
                 */
                $restaurants = array_map(function ($record) {
                    $images = collect($record['images'])->pluck('restaurant_image')->toArray();
                    $record['images'] = $images;
                    return $record;
                }, $restaurants);

                $result = null;

                if (!$request->has('tag')) {
                    /**
                     * RESTAURANT MAPPING TAG WISE
                     */
                    $tagsArr = Tag::select('id', 'name')->get()->toArray();

                    $tagsArr = array_map(function ($record) {

                        $record['restaurants'] = [];
                        return $record;
                    }, $tagsArr);

                    foreach ($restaurants as $restaurant) {

                        foreach ($restaurant['tags'] as $tag) {

                            $index = $this->findValueIndex($tagsArr, $tag['id']);

                            if ($index) {
                                unset($restaurant['tags']);
                                if (isset($tagsArr[$index]['restaurants'])) {
                                    if(count($tagsArr[$index]['restaurants']) <= 2) {
                                        array_push($tagsArr[$index]['restaurants'], $restaurant);
                                    }
                                } else {
                                    $tagsArr[$index]['restaurants'][] = $restaurant;
                                }
                            }
                        }
                    }

                    $result = $tagsArr;
                    
                } else {

                    $restaurants = array_map(function ($record) {
                        $record['tags'] = array_map(function ($r) {
                            return ['id' => $r['id'], 'name' => $r['name']];
                        }, $record['tags']);
                        return $record;
                    }, $restaurants);
                }

                if ($request->has('tag')) {
                    // $restaurants 
                    $restaurants = collect($restaurants);
                    /*** RESTAURANT PAGINATION*/
                    $result = $this->paginate($restaurants);
                }
                return $this->returnSuccessResponse('Restaurant listing', $result);
            }

            return $this->returnSuccessResponse('No Restaurant Found!', []);
        } catch (Exception $exception) {
            report($exception);
            return $this->returnErrorResponse($exception->getMessage());
        }
    }

    /**
     * **************************************************************
     *  My Favorite Restaurant List
     * **************************************************************
     * */
    public function my_favorite_restaurant(Request $request)
    {
        try {

            if (auth()->check()) {
                $user = auth()->user();

                $wishlist_restaurants = $user->wishlist_restaurants()->with('restaurant')->get()->pluck('restaurant');

                $data = $wishlist_restaurants->map(function ($record) {
                    return $record->jsonResponse();
                });

                $data = collect($data);
                    /*** WISHLIST PAGINATION*/
                $result = $this->paginate($data);

                return $this->returnSuccessResponse('My Favorite Restaurant listing', $result);
            }
            return $this->returnErrorResponse('user not found');
        } catch (Exception $exception) {
            report($exception);
            return $this->returnErrorResponse($exception->getMessage());
        }
    }

    public function restaurant_tags_listing(RestaurantListingRequest $request) {

    
        try {

            /**
             * GET ALL BUSINESS HAVING RESTAURANT
             */
            $users = User::where(['user_type' => 'business', 'is_verified' => '1', 'is_suspended' => '0'])
                ->whereHas('restaurant');

            if ($users->count() > 0) {
                $users = $users->get()->filter(function($user) {
                       
                    if($user->has_free_trial == '1') {return true;}
                    if($user->subscription_status) {return true;}
                    return false;
                    
                });;

                $restaurants = Restaurant::active()->whereIn('user_id', $users->pluck('id'));

                if ($request->has('tag')) {
                    $tag = $request->get('tag');
                    $restaurants = $restaurants->whereHas('tags', function ($query) use ($tag) {
                        $query->where('tag_id', $tag);
                    });
                } else {
                    $restaurants = $restaurants->whereHas('tags');
                }

                $restaurants = $restaurants->with(['tags', 'images', 'menus']);


                /**
                 * FILTER BUSINESS BASED ON LATITUDE OR LONGITUDE
                 */
                if ($request->has('type')) {

                    if ($request->get('type') == 'nearby') {

                        if ($request->has('latitude') && $request->has('longitude')) {

                            if (!empty($request->get('latitude')) && !empty($request->get('longitude'))) {

                                $latitude  = $request->get('latitude');
                                $longitude = $request->get('longitude');
                                $restaurants = $restaurants->select(DB::raw('*, ( 6367 * acos( cos( radians(' . $latitude . ') ) * cos( radians( restaurant_latitude ) ) * cos( 
                                    radians( restaurant_longitude ) - radians(' . $longitude . ') ) + sin( radians(' . $latitude . ') ) * sin( radians( restaurant_latitude ) ) ) ) AS 
                                    distance'))
                                    ->orderBy('distance', 'asc');
                            }
                        }
                    }

                    if ($request->get('type') == 'recomended') {
                        $restaurants = $restaurants->inRandomOrder();
                    }
                }

                $restaurants = $restaurants->get()->toArray();

                /**
                 * RESTAURANT FILTER BASED ON SEARCHING
                 */
                $search = $request->get('search');
                if (!is_null($search) && !empty($search)) {
                    $search = trim(strtolower($search));

                    $restaurants = array_filter($restaurants, function ($record) use ($search) {

                        if (stristr($record['restaurant_name'], $search)) {
                            return true;
                        }
                        // if (stristr($record['restaurant_description'], $search)) {
                        //     return true;
                        // }
                        return false;
                    });
                }

                /**
                 * RESTAURANT MAPPING IMAGES AND MENU'S
                 */
                $restaurants = array_map(function ($record) {
                    $images = collect($record['images'])->pluck('restaurant_image')->toArray();
                    $record['images'] = $images;
                    return $record;
                }, $restaurants);

                $result = null;

                if (!$request->has('tag')) {
                    /**
                     * RESTAURANT MAPPING TAG WISE
                     */
                    $tagsArr = Tag::select('id', 'name')->get()->toArray();

                    $tagsArr = array_map(function ($record) {

                        $record['restaurants'] = [];
                        return $record;
                    }, $tagsArr);

                    foreach ($restaurants as $restaurant) {

                        foreach ($restaurant['tags'] as $tag) {

                            // $index = $this->findValueIndex($tagsArr, $tag['id']);
                            $index = null;
                            
                            foreach($tagsArr as $i => $tArr) {

                                if($tArr['id'] == $tag['id']) {
                                    $index = $i;
                                    break;
                                }
                            }

                            if (!is_null($index)) {
                                unset($restaurant['tags']);
                                if (isset($tagsArr[$index]['restaurants'])) {
                                    if(count($tagsArr[$index]['restaurants']) <= 2) {
                                        array_push($tagsArr[$index]['restaurants'], $restaurant);
                                    }
                                } else {
                                    $tagsArr[$index]['restaurants'][] = $restaurant;
                                }
                            }
                        }
                    }

                    $result = $tagsArr;

                    if ($request->has('type')) {
                        if ($request->get('type') == 'nearby') {
                            if ($request->has('latitude') && $request->has('longitude')) {
                                $newRequest = new RestaurantListingRequest();
                                $newRequest->merge([
                                    'type' => 'nearby',
                                    'latitude' => $request->get('latitude'),
                                    'longitude' => $request->get('longitude')
                                ]);
                                $response = $this->restaurant_listing($newRequest);
                                $dataRes  = $response->getData();
                                if($dataRes){
                                    $arr = $dataRes->data->data;
                                    array_unshift($result,['id' => -1,'name' => 'Restaurants near you','restaurants' => $arr]);    
                                }

                            }
                        }   
                    } 
                    
                } else {

                    $restaurants = array_map(function ($record) {
                        $record['tags'] = array_map(function ($r) {
                            return ['id' => $r['id'], 'name' => $r['name']];
                        }, $record['tags']);
                        return $record;
                    }, $restaurants);
                }

                if ($request->has('tag')) {
                    // $restaurants 
                    $restaurants = collect($restaurants);
                    /*** RESTAURANT PAGINATION*/
                    $result = $this->paginate($restaurants);
                }
                return $this->returnSuccessResponse('Restaurant listing', $result);
            }

            return $this->returnSuccessResponse('No Restaurant Found!', []);
        } catch (Exception $exception) {
            report($exception);
            return $this->returnErrorResponse($exception->getMessage());
        }
    }

    public function restaurant_timing(Request $request) {
        
        $rules = [ 
                   'restaurant_id' => ['required','integer','exists:restaurants,id'],
                   'date'          => ['nullable', 'date_format:Y-m-d'],
                ];
        $this->validate($request,$rules);

        try {
            $restaurant  = Restaurant::findorFail($request->get('restaurant_id'));
            $business_detail = $restaurant->business_detail;
            $currentDate = Carbon::now()->format('Y-m-d');
            $date        = $request->has('date') ? $request->get('date') : $currentDate;

            $restaurant_opening_time =  $restaurant->restaurant_opening_time;
            $restaurant_closing_time =  $restaurant->restaurant_closing_time;
           

            if($date != $currentDate) {
                $available_slot = BusinessAvailabilityAndTimeSlot::where('business_id',$business_detail->id)
                                                                   ->whereDate('availability_date',$date)->first();
                if($available_slot) {
                    $restaurant_opening_time =  $available_slot->time_slot_from;
                    $restaurant_closing_time =  $available_slot->time_slot_to;
                }                                       
            }

            return $this->returnSuccessResponse('Restaurant Timing!', [
                 
                'restaurant_opening_time' =>  $restaurant_opening_time,
                'restaurant_closing_time' =>  $restaurant_closing_time

            ]);
           
        } catch (Exception $exception) {
            report($exception);
            return $this->returnErrorResponse($exception->getMessage());
        }
    }
    
    /**
     * **************************************************************
     *  FEATURED RESTAURANT LIST
     * **************************************************************
     * */
    public function featured_restaurant_list() {
        
        $records = UserFeaturedSubscriptionStatus::where('status','active')->take(3)->orderBy('created_at','desc')->get();
        $list = array();
        if($records->count()) {

            $user_id     = $records->pluck('user_id');
            $business    = Business::whereIn('id',$user_id)->get();
            $restaurants = Restaurant::active()->whereIn('user_id', $business->pluck('id'))
                                  ->with(['images']);
            $restaurants = $restaurants->get()->toArray();
            
            foreach($restaurants as $restaurant) {
                $restaurant_images = $restaurant['images'];
                $image = $restaurant_images[0]['restaurant_image'];
                $data = [

                    'restaurant_image'  => $image,
                    'restaurant_detail' => $restaurant
                ];
                array_push($list,$data);   
            }

            // $restaurants = Restaurant::whereIn('user_id', $business->pluck('id'))
            //         ->with(['images', 'menus']);
            // $restaurants = $restaurants->get()->toArray();       
            // /**
            //  * RESTAURANT MAPPING IMAGES AND MENU'S
            //  */
            // $restaurants = array_map(function ($record) {
            //     $images = collect($record['images'])->pluck('restaurant_image')->toArray();
            //     $record['images'] = $images;
            //     return $record;
            // }, $restaurants);
            // return $this->returnSuccessResponse('Featured Restaurant listing', $restaurants);        
        }
        
        if(count($list) < 3) {

            $banners = BannerImage::take(3 - count($list))->get();
            if($banners->count()) {
                $banner_images = $banners->pluck('banner_image');
                foreach($banner_images as $banner_image) {
                    $data = [

                        'restaurant_image'  => $banner_image,
                        'restaurant_detail' => null
                    ];
                    array_push($list,$data);
                }
            }
        }
        
        return $this->returnSuccessResponse('Featured Restaurant listing', $list);
    }

    /**
     * **************************************************************
     *  CHECK IF VALUE EXITS THEN RETURN INDEX
     * **************************************************************
     * */
    protected function findValueIndex($array, $value, &$index = null)
    {
        foreach ($array as $key => $item) {
            if ($item === $value) {
                $index = $key;
                return $key;
            } elseif (is_array($item) && $this->findValueIndex($item, $value, $index)) {
                return $key;
            }
        }

        return false;
    }
}
