<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SubscriptionStatus;
use App\Helpers\CommonHelper;

class SubscriptionExpiredEveryDayCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription-expired-every-day-cron:notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'notification every day for subscription expiration ';

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
        $expiredSubscriptions = SubscriptionStatus::Where('status','expired')->get();
        foreach ($expiredSubscriptions as $expiredSubscription) {
            $business = $expiredSubscription->business;
            if (filled($business->fcm_token)) {
                $notificationMessage = "Dear {$business->name}, your subscription is expired. Please renew or extend your subscription to continue enjoying our services. Thank you."; 
                $additionalData = [
                    'user_id'          => $business->id,
                    'title'            => 'Subscription Expired',
                    'body'             => $notificationMessage,
                    'type'             => 1,
                    'role'             => 'business',
                    'notificationType' => 'subscription_expired',
                ];
                CommonHelper::sendCurlPushNotification($business->fcm_token, $additionalData);
            }
        }
    }
}
