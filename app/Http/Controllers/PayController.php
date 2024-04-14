<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Business;
use App\Traits\ApiMethodsTrait;
use Exception;
use App\Traits\ApiResponser;
use App\Models\UserFeaturedSubscriptionStatus;
use Illuminate\Support\Carbon;

class PayController extends Controller
{
    use ApiMethodsTrait, ApiResponser;
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
    */
    public function pay(Request $request, $user_id = null)
    {
        try {
        
            $user  = null;
            $token = $request->bearerToken();
            if ($token) {
                $token = $request->bearerToken();
                $user  =  auth('sanctum')->user();
            } elseif (!is_null($user_id)) {
                $user = Business::find($user_id);
            }
            if ($user) {
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
                 * CHECK IF USER ALREADY HAVE ACTIVE FEATURED SUBSCRIPTION
                */
                $featured_subscription_status = $user->featured_subscription_status;
                if (is_null($featured_subscription_status) || $featured_subscription_status->status != 'active') {
                    /**
                     * CHECK USER COUNT ALREADY HAVE ACTIVE FEATURED SUBSCRIPTION
                     */
                    $count = UserFeaturedSubscriptionStatus::where(['status' => 'active'])->count();
                    if ($count < 3) {
                        session()->put('user_info', ['id' => $user->id, 'name' => $user->name, 'email' => $user->email]);
                        return view('payment.pay')->with(['user' => $user]);
                    } else {
                        return $this->returnErrorResponse('No Slot Available');
                    }
                } 
                else {
                    return $this->returnErrorResponse('Alreday have active subscription!');
                }
            } else {
                return $this->returnErrorResponse('No User Found!');
            }
        } 
        catch (Exception $exception) {
            report($exception);
            return $this->returnErrorResponse($exception->getMessage());
        }
    }

   
}
