<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Business;

class FeaturedSubscriptionExpirationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $business;

    public function __construct(Business $business)
    {
        $this->business = $business;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
         return $this->to($this->business->email, $this->business->name)
                    ->subject('Featured Subscription expired')
                    ->markdown('email.business.featured-subscription-expired');
    }
}
