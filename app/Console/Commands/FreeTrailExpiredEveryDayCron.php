<?php

namespace App\Console\Commands;

use App\Helpers\CommonHelper;
use App\Models\Business;
use App\Models\SubscriptionStatus;
use Illuminate\Console\Command;

class FreeTrailExpiredEveryDayCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'free-trail-expired-every-day-cron:notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'notification every day for free trail expiration ';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $expiredUsers = Business::where('has_free_trial','0')->get();
        foreach ($expiredUsers as $business) {
            if (filled($business->fcm_token)) {

                $subscription_status = SubscriptionStatus::where('user_id',$business->id)->first();
                if(!$subscription_status) {

                    $notificationMessage = "Dear {$business->name}, your free trial peroid is expired. Subscribe now to continue enjoying our services. Thank you."; 
                    $additionalData = [
                        'user_id'          => $business->id,
                        'title'            => 'Subscription Expired',
                        'body'             => $notificationMessage,
                        'type'             => 1,
                        'role'             => 'business',
                        'notificationType' => 'free_trail_subscription_expired',
                    ];
          
                    CommonHelper::sendCurlPushNotification($business->fcm_token, $additionalData);
                }
            }
        }
    }
}
