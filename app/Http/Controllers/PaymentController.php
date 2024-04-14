<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Resolvers\PaymentPlatformResolver;
use App\Traits\ApiMethodsTrait;
use Exception;
use App\Traits\ApiResponser;
use App\Models\Business;

class PaymentController extends Controller
{
    protected $paymentPlatformResolver;
    use ApiMethodsTrait, ApiResponser;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(PaymentPlatformResolver $paymentPlatformResolver)
    {

        $this->paymentPlatformResolver = $paymentPlatformResolver;
    }

    /**
     * Obtain a payment details.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function pay(Request $request, $user_id = null)
    {
        $request->merge([

            'value'            => 100,
            'currency'         => 'usd',
            'payment_platform' => 1
        ]);

        $paymentPlatform = $this->paymentPlatformResolver
                                ->resolveService($request->payment_platform);
         
        // session()->put('paymentPlatformId', $request->payment_platform);

        return $paymentPlatform->handlePayment($request);
         
        // session()->put('paymentPlatformId', $request->payment_platform); 
    }

    public function approval(Request $request,$user_id = null)
    {
        $request->validate([
            'paymentPlatformId' => 'required',
            'paymentIntentId'   => 'required'
        ]);

        if ($request->has('paymentPlatformId')) {

            $paymentPlatform = $this->paymentPlatformResolver
                ->resolveService($request->get('paymentPlatformId'));

            return $paymentPlatform->handleApproval($request);
           
        }

        return $this->returnErrorResponse('unable to found payment platform or payment intent');
    }

    public function cancelled()
    {
        return view('payment.cancelled',['status' => false ,'message' => 'You cancelled the payment.']);
    }
}