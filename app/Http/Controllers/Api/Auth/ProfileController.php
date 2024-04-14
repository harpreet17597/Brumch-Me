<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\Api\UpdateProfileRequest;

class ProfileController extends Controller
{
    use ApiResponser;

    public function __construct()
    {
    }

    /**
     * **************************************************************
     *  MY PROFILE START
     * **************************************************************
     * */
    public function myprofile()
    {
        try {

            $user = auth()->user();
            $role = $user->role();
            if ($user) {
                if (!is_null($user) && !empty($user)) {
                    if ($user->isProfileSuspended()) {
                        return $this->returnErrorResponse('Your profile is suspended please contact admin.');
                    }
                    if ($role == 'business') {
                        if (!$user->hasVerifiedProfile()) {
                            return $this->returnErrorResponse('Your profile is not approved yet.');
                        }
                    }
                }
                $response = $user->jsonResponse();
                $response['subscription'] = false;
                if($role == 'business' && $user->subscription_status) {
                    $response['subscription'] = true;
                }
                return $this->returnSuccessResponse('My profile', $response);
            }
            return $this->returnErrorResponse('User not found.');
        } catch (Exception $exception) {
            report($exception);
            return $this->returnErrorResponse($exception->getMessage());
        }
    }
    /**
     * **************************************************************
     *  MY PROFILE END
     * **************************************************************
     * */

    /**
     * **************************************************************
     *  UPDATE PROFILE START
     * **************************************************************
     * */
    public function update_profile(UpdateProfileRequest $request)
    {

        try {
            $user = auth()->user();

            if ($user) {

                /**
                 * CHECK IF USER IS SUSPENDED OR HAVE NOT VERIFIED PROFILE
                 */
                if (!is_null($user) && !empty($user)) {
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
                 * UPDATE USER
                 */
                if ($request->has('name')) {
                    $user->name = $request->get('name');
                }
                if ($request->has('email')) {
                    $user->email = $request->get('email');
                }
                if ($request->has('street_address')) {
                    $user->street_address = $request->get('street_address');
                }
                if ($request->has('dob')) {
                    $user->dob = $request->get('dob');
                }
                if ($request->has('lat') && $request->has('lng')) {
                    $user->lat = $request->get('lat');
                    $user->lng = $request->get('lng');   
                }
               
                if ($user->role() == 'business') {

                       if($request->has('restaurant_opening_time')){
                           $user->restaurant_opening_time = $request->get('restaurant_opening_time');              
                       }
                       if($request->has('restaurant_closing_time')){
                           $user->restaurant_closing_time = $request->get('restaurant_closing_time');
                       }
                       if($request->has('restaurant_max_table')){
                         $user->restaurant_max_table    = $request->get('restaurant_max_table');
                       }
                        
                        if( $user->restaurant ) {
                            $user->restaurant->restaurant_address       = $user->street_address;
                            $user->restaurant->restaurant_latitude      = $user->lat;
                            $user->restaurant->restaurant_longitude     = $user->lng;
                            $user->restaurant->restaurant_opening_time  = $user->restaurant_opening_time;
                            $user->restaurant->restaurant_closing_time  = $user->restaurant_closing_time;
                            $user->restaurant->save();
                        }
                               
                }

                if (!$user->save()) {
                    return $this->returnErrorResponse('Error while updating user profile.');
                }

                if ($request->hasFile('profile_image')) {
                    $user->deleteProfileImage();
                    $user->updateProfileImage($user->id, $request->file('profile_image'));
                }

                return $this->returnSuccessResponse('profile updated successfully.', $user->fresh()->jsonResponse());
            }
            return $this->returnErrorResponse('User not found.');
        } catch (Exception $exception) {
            report($exception);
            return $this->returnErrorResponse($exception->getMessage());
        }
    }
    /**
     * **************************************************************
     *  UPDATE PROFILE END
     * **************************************************************
     * */


    /**
     * **************************************************************
     *  MY PROFILE START
     * **************************************************************
    * */
    public function profile(Request $request) {
       
        $rules = [
            'user_id' => 'required|integer|exists:users,id'
        ];

        $this->validate($request, $rules);

        try {

            $user = auth()->user();
            if ($user) {
                if (!is_null($user) && !empty($user)) {
                    if ($user->isProfileSuspended()) {
                        return $this->returnErrorResponse('Your profile is suspended please contact admin.');
                    }
                    if ($user->role() == 'business') {
                        if (!$user->hasVerifiedProfile()) {
                            return $this->returnErrorResponse('Your profile is not approved yet.');
                        }
                    }
                }

                $otherUser = User::findorFail($request->get('user_id'));

                return $this->returnSuccessResponse('User profile', $otherUser->jsonResponse());
            }
            return $this->returnErrorResponse('User not found.');
        } catch (Exception $exception) {
            report($exception);
            return $this->returnErrorResponse($exception->getMessage());
        }
    }
    /**
     * **************************************************************
     *  MY PROFILE END
     * **************************************************************
    * */
    
    /**
     * **************************************************************
     *  ACCOUNT DEACTIVATE
     * **************************************************************
    * */
    public function account_deactivate()
    {
        try {

            $user = auth()->user(); 
            if (!$user) {
                return $this->notAuthorizedResponse('You are not authenticated.');
            }
           
            $user->tokens()->delete();
            $user->account_status = User::STATUS_PROFILE_DELETED;
            $user->deleted_at = $user->freshTimestamp();
            $user->save();    
            $user->setLoggedOut();

            if($user->role() == 'business') {
                $user->restaurant()->update([
                    'active_status' => '0'
                ]);
            }

            return $this->returnSuccessResponse('Account deactivated successfully!', $user);
        }
        catch (Exception $exception) {
            report($exception);
            return $this->returnErrorResponse($exception->getMessage());
        }
    }

}
