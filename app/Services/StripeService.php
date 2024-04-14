<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Traits\ConsumesExternalServices;
use App\Traits\ApiMethodsTrait;
use Exception;
use App\Traits\ApiResponser;
use App\Models\UserFeaturedSubscriptionStatus;
use App\Models\UserFeaturedSubscription;
use Carbon\Carbon;

class StripeService
{
    use ConsumesExternalServices,ApiMethodsTrait, ApiResponser;

    protected $baseUri;
    protected $key;
    protected $secret;

    public function __construct()
    {
        $this->baseUri = config('services.stripe.base_uri');
        $this->key     = config('services.stripe.key');
        $this->secret  = config('services.stripe.secret');    
    }

    public function resolveAuthorization(&$queryParams, &$formParams, &$headers)
    {
        $headers['Authorization'] = $this->resolveAccessToken();
    }

    public function decodeResponse($response)
    {
        return json_decode($response);
    }

    public function resolveAccessToken()
    {
        return "Bearer {$this->secret}";
    }

    public function handlePayment(Request $request)
    {
        $request->validate([
            'payment_method' => 'required',
        ]);
        $intent = $this->createIntent($request->value, $request->currency, $request->payment_method);
        if($intent->id) {
            return redirect()->route('approval',['paymentPlatformId'  =>  $request->payment_platform,'paymentIntentId' => $intent->id]);
            // return $this->returnSuccessResponse("Payment Approval",[
            //     'approve_url' => url()->route('approval',['paymentPlatformId'  =>  $request->payment_platform,'paymentIntentId' => $intent->id],false)
            // ]);
        }
        return $this->returnErrorResponse("We are unable to create payment intent. Try again, please");
        // session()->put('paymentIntentId', $intent->id);
        // return redirect()->route('approval');
    }

    public function handleApproval(Request $request)
    {
        if ($request->has('paymentIntentId')) {
            $paymentIntentId = $request->get('paymentIntentId');
    
            $confirmation = $this->confirmPayment($paymentIntentId);

            if ($confirmation->status === 'requires_action') {
                $clientSecret = $confirmation->client_secret;

                return view('stripe.3d-secure')->with([
                    'clientSecret' => $clientSecret,
                ]);
            }

            if ($confirmation->status === 'succeeded') {
                
                $currency = strtoupper($confirmation->currency);
                $amount = $confirmation->amount / $this->resolveFactor($currency);

                $payment_id          = $confirmation->id;
                $name                = $confirmation->charges->data[0]->billing_details->name;
                $charge_id           = $confirmation->charges->data[0]->id;
                $payment_intent      = $confirmation->charges->data[0]->payment_intent;
                $payment_method      = $confirmation->charges->data[0]->payment_method;
                $status              = $confirmation->charges->data[0]->status;
                $balance_transaction = $confirmation->charges->data[0]->balance_transaction;

                $startDate = Carbon::now();
                $startDateOfDay = $startDate->copy()->day;
                $startDateOfMonth = $startDate->copy()->month;
                $startDateOfYear = $startDate->copy()->year;

                $endDate = Carbon::now()->addDays(2);
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


                /*create subscription*/
                $user_info = session()->get('user_info');
                $subs = UserFeaturedSubscriptionStatus::where('user_id',$user_info['id'])->first();
                $data_to_store = [
                          
                    'user_id'         => $user_info['id'],
                    'status'          => 'active',
                    'start_date'      => $data['start_date'],
                    'end_date'        => $data['end_date'],
                    'start_date_unix' => $data['start_unix_date'],
                    'end_date_unix'   => $data['end_unx_date']

                ];

                if(!$subs) {
                    $record = UserFeaturedSubscriptionStatus::create($data_to_store);
                }
                else {
                    $record = $subs->fill($data_to_store)->update();
                }
                
                /*save payment details*/
                UserFeaturedSubscription::create([
                     
                    'user_id'              => $user_info['id'],
                    'payment_id'           => $payment_id,
                    'amount'               => $amount,
                    'currency'             => $currency,
                    'charge_id'            => $charge_id,
                    'payment_intent'       => $payment_intent,
                    'payment_method'       => $payment_method,
                    'balance_transaction'  => $balance_transaction,
                    'status'               => $status,
                    'start_date'           => $data['start_date'],
                    'end_date'             => $data['end_date'],
                    'start_date_unix'      => $data['start_unix_date'],
                    'end_date_unix'        => $data['end_unx_date']

                ]);

                return redirect()->route('payment.success',['status' => true,'message' => "Thanks, {$name}. We received your {$amount}{$currency} payment."]);  
                // return $this->returnSuccessResponse("Thanks, {$name}. We received your {$amount}{$currency} payment.");
            }
            else {
                return redirect()->route('cancelled',['status' => false,'message' => "payment not completed"]);  
            }
        }
        
        return $this->returnErrorResponse("We are unable to confirm your payment. Try again, please");
    }


    public function createIntent($value, $currency, $paymentMethod)
    {
        return $this->makeRequest(
            'POST',
            '/v1/payment_intents',
            [],
            [
                'amount' => round($value * $this->resolveFactor($currency)),
                'currency' => strtolower($currency),
                'payment_method' => $paymentMethod,
                'confirmation_method' => 'manual',
            ],
        );
    }

    public function confirmPayment($paymentIntentId)
    {
        return $this->makeRequest(
            'POST',
            "/v1/payment_intents/{$paymentIntentId}/confirm",
        );
    }

    public function createCustomer($name, $email, $paymentMethod)
    {
        return $this->makeRequest(
            'POST',
            '/v1/customers',
            [],
            [
                'name' => $name,
                'email' => $email,
                'payment_method' => $paymentMethod,
            ],
        );
    }

    public function resolveFactor($currency)
    {
        $zeroDecimalCurrencies = ['JPY'];

        if (in_array(strtoupper($currency), $zeroDecimalCurrencies)) {
            return 1;
        }

        return 100;
    }

    public function createPaymentMetho()
    {
        return $this->makeRequest(
            'POST',
            '/v1/payment_methods',
            [],
            [
                "type" =>  "card",
                "card" => [
                  "number" => "4242424242424242",
                  "exp_month" => 12,
                  "exp_year" => 2024,
                  "cvc" => "123"
                ]
            ],
        );
    }

}