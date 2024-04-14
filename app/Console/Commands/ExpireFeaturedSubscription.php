<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserFeaturedSubscriptionStatus;
use Illuminate\Support\Carbon;
use App\Jobs\SendFeaturedSubscriptionExpiredMailJob;

class ExpireFeaturedSubscription extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'expire_featured_subscription:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'mark the status of featured subscription expired which expire time is past now';

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
        $expiredSubscriptions = UserFeaturedSubscriptionStatus::WhereRaw("(end_date_unix +600) < unix_timestamp() and status!='expired'")->get();
        foreach ($expiredSubscriptions as $expiredSubscription) {
            $expiredSubscription->status = 'expired';
            $expiredSubscription->save();
            $business = $expiredSubscription->business;
            if($business->email) {
                dispatch(new SendFeaturedSubscriptionExpiredMailJob($business))->delay(Carbon::now()->addSeconds(5));;
            }
        }

    }
}
