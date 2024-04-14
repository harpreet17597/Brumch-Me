<?php

namespace App\Http\Controllers\Api\Auth;

use Exception;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\MobileRegisterRequest;
use App\Http\Requests\Api\RegisterRequestNew;
use App\Helpers\CommonHelper;
use App\Models\UserOtp;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Traits\ApiMethodsTrait;
use App\Traits\ApiResponser;

class RegisterController extends Controller
{
    use ApiMethodsTrait,ApiResponser;

    public function __construct() {

    }

    public function store(RegisterRequestNew $request)
    {
        try {

            $formData = $request->getValidatedData();
            $formData['country_code'] = $formData['country_code_text'];
            $new_phone = CommonHelper::updatePhone($formData['phone']);
           
            $rememberToken = Str::random(60);
            $formData['remember_token'] = $rememberToken;

            $user = User::create($formData);

            $user->updateCustomerStatus($user->id, User::STATUS_ACCOUNT_CREATED);
            if($request->hasFile('profile_image')) {
                $user->updateProfileImage($user->id,$request->file('profile_image'));
            }
            $user->markPhoneAsVerified();
          
                Auth::login($user);
                $user->refresh();
                $user = Auth::user();
                
                $returnArr = $user->jsonResponse();

                if($user->role() == 'customer') {
                    
                    $token = $user->createToken('admin-api-skeleton')->plainTextToken;
                    $returnArr['token'] = $token;
                }
                
                if($user->role() == 'business') {
                    $returnArr['restaurant_detail_filled'] = false;
                }

                return $this->returnSuccessResponse('Account created successfully.',$returnArr);

            // return $this->respondCreated('Account created successfully.', [
            //     'user'  => $user,
            //     'token' => $token,
            // ]);
        } catch (Exception $exception) {
            report($exception);
            return $this->returnErrorResponse($exception->getMessage());
            // return $this->respondInternalError($exception->getMessage());
        }
    }
    
    /**
     * REGISTER USING PHONE
    */
    public function mobileRegister(MobileRegisterRequest $request)
    {
        try {
            $formData = $request->getValidatedData();
            $user = User::create($formData);
            $user->generateSendOtp();
            return $this->respondCreated('We have sent an OTP to your number.');
        } catch (Exception $exception) {
            report($exception);
            return $this->returnErrorResponse($exception->getMessage());
            // return $this->respondInternalError($exception->getMessage());
        }
    }
}
