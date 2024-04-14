<?php

namespace App\Http\Controllers\Api;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Models\Business;
use Illuminate\Http\Request;
use App\Traits\ApiMethodsTrait;
use Exception;
use App\Traits\ApiResponser;
use App\Models\SubscriptionPlan;
use App\Models\SubscriptionStatus;
use App\Models\UserSubscription;
use Illuminate\Support\Carbon;

class SubscriptionController extends Controller
{
    use ApiMethodsTrait, ApiResponser;

    public function __construct() {

    }
    
    /**
     * **************************************************************
     *  All Subscription Plans
     * **************************************************************
    * */
    public function plans() {
       
        try {
            $plans = SubscriptionPlan::all();
            if($plans) {
                return $this->returnSuccessResponse('Plans',$plans);
            }
            return $this->returnErrorResponse('No Plan found');
        } 
        catch (Exception $exception) {
            report($exception);
            return $this->returnErrorResponse($exception->getMessage());
        }
    }
    
    /**
     * **************************************************************
     *  Subscription Plan BUY
     * **************************************************************
    * */
    public function subscription_plan_buy(Request $request) {
        
        $this->validate($request,[

            'planId'             => 'required|integer|exists:subscription_plans,id',
            'productId'          => 'required|string',
            'transactionId'      => 'required|string',
            'transactionDate'    => 'required',
            'transactionReceipt' => 'required',
            'purchaseToken'      => 'nullable|string',
         
        ]);

        try {
            
            $user = auth()->user();
            $user = Business::find($user->id);
            $plan = SubscriptionPlan::where('id',$request->get('planId'))->first();
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
                $subscription = $user->subscription_status;
                if (is_null($subscription) || $subscription->status != 'active') {

                    $startDate = Carbon::now();
                    $startDateOfDay = $startDate->copy()->day;
                    $startDateOfMonth = $startDate->copy()->month;
                    $startDateOfYear = $startDate->copy()->year;
                    
                    $days = null;
                    $plan_interval = $plan->interval;
                    $plan_interval_duration = $plan->interval_duration;

                    if($plan_interval == 'weekly') {
                        $days = 7*$plan_interval_duration;
                    }
                    else if($plan_interval == 'monthly') {
                        $days = 30*$plan_interval_duration;
                    }
                 
                    $endDate = Carbon::now()->addDays($days);
                    $endDateOfDay = $endDate->copy()->day;
                    $endDateOfMonth = $endDate->copy()->month;
                    $endDateOfYear = $endDate->copy()->year;
        
        
                    $startTime = now()->utc();
                    $startHour = $startTime->copy()->hour;
                    $startMin = $startTime->copy()->minute;
                    $startSecond = $startTime->copy()->second;
        
                    $endTime = now()->utc();
                    $endHour = $endTime->copy()->hour;
                    $endMin = $endTime->copy()->minute;
                    $endSecond = $endTime->copy()->second;
        
                    $data['start_date'] = Carbon::create($startDateOfYear, $startDateOfMonth, $startDateOfDay, $startHour, $startMin, $startSecond);
                    $data['end_date'] = Carbon::create($endDateOfYear, $endDateOfMonth, $endDateOfDay, $endHour, $endMin, $endSecond)->endOfDay()->utc();
        
                    $data['start_unix_date'] = $data['start_date']->timestamp;
                    $data['end_unx_date']   = $data['end_date']->timestamp;
                    
                    $user_subscription_status = SubscriptionStatus::where('user_id',$user->id)->first();
                    $subscription_status_data = [

                        'user_id'         => $user->id,
                        'status'          => 'active',
                        'current_plan_id' => $plan->id,
                        'start_date'      => $data['start_date'],
                        'end_date'        => $data['end_date'],
                        'start_date_unix' => $data['start_unix_date'],
                        'end_date_unix'   => $data['end_unx_date']
                    ];

                    if($user_subscription_status) {
                        $user_subscription_status->fill($subscription_status_data)->update();
                    }
                    else {

                        SubscriptionStatus::create($subscription_status_data);
                    }

                    UserSubscription::create([
        
                        'user_id'              => $user->id,
                        'plan_id'              => $plan->id,
                        'product_id'           => $request->get('productId'),
                        'transactionId'        => $request->get('transactionId'),
                        'transactionDate'      => $request->get('transactionDate'),
                        'transactionReceipt'   => $request->get('transactionReceipt'),
                        'purchaseToken'        => $request->get('purchaseToken'),
                        'amount'               => $plan->price,
                        'currency'             => 'USD',
                        'status'               => 'succeeded',
                        'start_date'           => $data['start_date'],
                        'end_date'             => $data['end_date'],
                        'start_date_unix'      => $data['start_unix_date'],
                        'end_date_unix'        => $data['end_unx_date']
                    ]);
                    
                    return $this->returnSuccessResponse('Thanks for subscription');
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
    
    /**
     * **************************************************************
     *  MY SUBSCRIPTION PLAN
     * **************************************************************
    * */
    public function my_subscription_plan() {
           
        try {
            $detail = [];
            $user = auth()->user();
            $user = Business::find($user->id);
            $subscription = $user->subscription_status;
            if($subscription) {
                $plan = SubscriptionPlan::where('id',$subscription->current_plan_id)->first();
                $detail = [
                    'id'         => $plan->id,
                    'name'       => $plan->name,
                    'product_id' => $plan->product_id,
                    'title'      => $plan->title,
                    'amount'     => $plan->price,
                    'status'     => $subscription->status,
                    'start_date' => $subscription->start_date,
                    'end_date'   => $subscription->end_date,
                ];
            }
            return $this->returnSuccessResponse('My Subscription Plan',$detail);
        }
        catch (Exception $exception) {
            report($exception);
            return $this->returnErrorResponse($exception->getMessage());
        }    
    }
    /**
     * **************************************************************
     *  Subscription
     * **************************************************************
    * */
    public function subscription()
    {
        

        $plan = SubscriptionPlan::find(2);
        
        $users = Business::all();

        foreach($users as $user) {

            $startDate = Carbon::now();
            $startDateOfDay = $startDate->copy()->day;
            $startDateOfMonth = $startDate->copy()->month;
            $startDateOfYear = $startDate->copy()->year;
    
            $endDate = Carbon::now()->addDays(30);
            $endDateOfDay = $endDate->copy()->day;
            $endDateOfMonth = $endDate->copy()->month;
            $endDateOfYear = $endDate->copy()->year;
    
    
            $startTime = now()->utc();
            $startHour = $startTime->copy()->hour;
            $startMin = $startTime->copy()->minute;
            $startSecond = $startTime->copy()->second;
    
            $endTime = now()->utc();
            $endHour = $endTime->copy()->hour;
            $endMin = $endTime->copy()->minute;
            $endSecond = $endTime->copy()->second;
    
            $data['start_date'] = Carbon::create($startDateOfYear, $startDateOfMonth, $startDateOfDay, $startHour, $startMin, $startSecond);
            $data['end_date'] = Carbon::create($endDateOfYear, $endDateOfMonth, $endDateOfDay, $endHour, $endMin, $endSecond)->endOfDay()->utc();
    
            $data['start_unix_date'] = $data['start_date']->timestamp;
            $data['end_unix_date']   = $data['end_date']->timestamp;
            SubscriptionStatus::create([
    
                'user_id'         => $user->id,
                'gateway'         => 'stripe',
                'gateway_sub_id'  => 'stripe',
                'status'          => 'active',
                'current_plan_id' => $plan->id,
                'product_sku'     => $plan->sku,
                'start_date'      => $data['start_date'],
                'end_date'        => $data['end_date'],
                'start_date_unix' => $data['start_unix_date'],
                'end_date_unix'   => $data['end_unix_date']
    
            ]);
        }

    }

}
