<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiMethodsTrait;
use Exception;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\Auth;
use App\Models\UserFeaturedSubscriptionStatus;
use App\Models\SubscriptionStatus;

class SubscriptionPlanController extends Controller
{
    use ApiMethodsTrait, ApiResponser;

    public function __construct() {

    }

    public function featured_subscription_availability() {
    
        try {
          $count = UserFeaturedSubscriptionStatus::where('status','active')->count();
          $featured_subscription = UserFeaturedSubscriptionStatus::where('user_id',auth()->id())->where('status','active')->first();
          $plan_subscription = SubscriptionStatus::where('user_id',auth()->id())->where('status','active')->first();
          $availability = false;
          if($count < 3) {
            $availability = true;
          }
          return $this->returnSuccessResponse('Featured Subscription Availability', [
            'availability' => $availability,
            'featured_subscription' => $featured_subscription ? true : false,
            'subscription' => !is_null($plan_subscription) ? true : false,
        ]);
        } catch (Exception $exception) {
            report($exception);
            return $this->returnErrorResponse($exception->getMessage());
        }
    }
}
