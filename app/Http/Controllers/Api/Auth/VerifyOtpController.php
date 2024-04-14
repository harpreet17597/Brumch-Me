<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\VerifyOtpRequest;
use App\Http\Resources\CustomerResource;
use App\Models\User;
use App\Models\UserOtp;
use App\Models\SmsOtp;
use App\Traits\ApiMethodsTrait;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Traits\ApiResponser;

class VerifyOtpController extends Controller
{
    use ApiMethodsTrait,ApiResponser;

    /**
     * Check the OTP is valid or not.
     *
     * @param  \App\Http\Requests\Api\VerifyOtpRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyOtp(VerifyOtpRequest $request)
    {
        try {
            $phone = $request->phone;
            if (preg_match('/^0/', $phone) == 1) {
                $phone = ltrim($phone, 0);
            }
            $country = $request->phone_country;
            $user = User::where(DB::raw('REPLACE(phone," ","")'), str_replace(' ', '', $phone))
                ->newQuery();

            if ($request->has('phone_country')) {
                $user->where('phone_country', $country);
            }

            $user = $user->first();
            
            if(!is_null($user) && !empty($user)) {
                
                if($user->account_status == User::STATUS_PROFILE_DELETED) {
                    return $this->returnErrorResponse('Your account is deactivated.');
                } 
                
                if($user->isProfileSuspended()) {
                    return $this->returnErrorResponse('Your profile is suspended please contact admin.');
                }
                if($user->role() != $request->user_type) {
                    return $this->returnErrorResponse('Credentails does not matched.');
                }
                
                // if($user->role() == 'business') {
                //     if(!$user->hasVerifiedProfile()){
                //         return $this->returnErrorResponse('Your profile is not approved yet.');
                //     }
                // }
                if($request->has('fcm_token')) {
                    $user->fcm_token = $request->get('fcm_token');
                    $user->save();
                }
            }

            
            $smsOtp = UserOtp::where(function ($query) use ($country, $phone, $user) {
                $query->where('phone_country', $country)
                    ->where('phone', $phone);
                    if(!is_null($user)) {
                        $query->orWhere('user_id', $user->id);
                    }
                    
            })
            ->where('status', UserOtp::STATUS_ACTIVE)
            ->first();

            $otp = (int) $request->otp;

            if ( (!$smsOtp || $smsOtp->otp != $otp) && $otp != SmsOtp::DEFAULT_OTP) {
                return $this->returnErrorResponse('The verification code you have entered is invalid or has expired. Please click on Resend now to get a new verification code and enter it again.');
            }
            
            if ($smsOtp) {
                $smsOtp->update([
                    'status' => SmsOtp::STATUS_INACTIVE,
                ]);
            }

            $returnArr = [];
            $message = 'Verified successfully.';
            $returnUser  = true;
            $returnToken = true;
            
            
            if($user) {
                
                $message = 'Login successfully.';

                if($user->isProfileSuspended()) {
                   $returnUser  = false;
                   $returnToken = false; 
                }
                
                if($user->role() == 'business') {
                    if(!$user->restaurant) {
                        $message = 'Fill restaurant detail.';
                        $returnArr['restaurant_detail_filled'] = false;
                        $returnToken = false;
                    } else {
                        $returnArr['restaurant_detail_filled'] = true;
                        if(!$user->hasVerifiedProfile()){
                            return $this->returnErrorResponse('Your profile is not approved yet.');
                        }
                    }
                }

                if($returnUser) {

                    Auth::login($user);
                    $user = Auth::user();
                    $user->tokens()->delete();
                   
                    $returnArr = $user->jsonResponse() + $returnArr;

                    if($user->role() == 'business') {
                        $returnArr['subscription'] = false;
                        if($user->subscription_status) {
                            $returnArr['subscription'] = true;
                        }
                    }

                    if($returnToken) {
                        $token = $user->createToken('admin-api-skeleton')->plainTextToken;
                        $returnArr['token'] = $token;
                        $user->setLoggedIn();
                    }

                }   
            }

            if(count($returnArr) == 0) {
                $returnArr = null;
            }
            return $this->returnSuccessResponse($message,$returnArr);
          
        
        } catch (Exception $exception) {
            report($exception);
            return $this->returnErrorResponse($exception->getMessage());
        }
    }
}
