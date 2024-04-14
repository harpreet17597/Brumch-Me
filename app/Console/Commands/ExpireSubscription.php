<?php

namespace App\Console\Commands;

use App\Models\SubscriptionStatus;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Jobs\SendSubscriptionExpiredMailJob;
use Illuminate\Support\Carbon;

class ExpireSubscription extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'expire_subscription:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'mark the status of subscription expired which expire time is past now';

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
        $expiredSubscriptions = SubscriptionStatus::WhereRaw("(end_date_unix +600) < unix_timestamp() and status!='expired'")->get();

        foreach ($expiredSubscriptions as $expiredSubscription) {
            $expiredSubscription->status = 'expired';
            $expiredSubscription->save();
            $business = $expiredSubscription->business;
            if($business->email) {
                dispatch(new SendSubscriptionExpiredMailJob($business))->delay(Carbon::now()->addSeconds(5));;
            }
        }
    }
}
