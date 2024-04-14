<?php

namespace App\Http\Controllers\Api;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\OtpRequestwithoutSignup;
use App\Models\User;
use App\Models\UserOtp;
use App\Traits\ApiMethodsTrait;
use Exception;
use App\Traits\ApiResponser;

class SendOtpController extends Controller
{
    use ApiMethodsTrait,ApiResponser;

    /**
     * send the OTP to the customer.
     *
     * @param  \App\Http\Requests\Api\OtpRequestwithoutSignup  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function send(OtpRequestwithoutSignup $request)
    {
        try {
            $otp = '';
            $country = $request->phone_country;
            $phone = $request->phone;
            if (preg_match('/^0/', $phone) == 1) {
                $phone = ltrim($phone, 0);
            }
            $phone = str_replace(' ', '', $phone);

            $user = User::getUserByPhone($phone);
           
            if($user) {
                if($user->account_status == User::STATUS_PROFILE_DELETED) {
                    return $this->returnErrorResponse('Your account is deactivated.');
                }              
                if($user->role() != $request->user_type) {
                    return $this->returnErrorResponse('Credentails does not matched.');
                }
                if($user->isProfileSuspended()) {
                    return $this->returnErrorResponse('Your profile is suspended please contact admin.');
                }
                if($user->role() == 'business') {
                    if($user->restaurant && !$user->hasVerifiedProfile()){
                        return $this->returnErrorResponse('Your profile is not approved yet.');
                    }
                }
            }


            $smsOtp = UserOtp::where(function ($query) use ($country, $phone) {
                $query->where('phone_country', $country)
                    ->where('phone', $phone);
            })
            ->where('status', UserOtp::STATUS_ACTIVE)
            ->first();

            if (!$smsOtp) {
                $smsOtp = CommonHelper::generateOtp($country, $phone);
            }

            CommonHelper::sendSms($smsOtp, $country.$phone);

            return $this->returnSuccessResponse('We have sent a verification code to your mobile number.', [
                'phone_country' => $country,
                'phone' => $phone,
            ]);

        } catch (Exception $exception) {
            report($exception);
            return $this->returnErrorResponse($exception->getMessage());
        }
    }
}
