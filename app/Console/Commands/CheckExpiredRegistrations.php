<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use App\Models\Business;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendTrialExpirationMail;

class CheckExpiredRegistrations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'registration:check-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and handle expired user registrations';

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
        $sevenDaysAgo = Carbon::now()->subDays(7);
        $expiredUsers = Business::where('created_at', '<=', $sevenDaysAgo)->where('has_free_trial','1')->get();
        foreach ($expiredUsers as $user) {
            $user->has_free_trial = '0';
            $user->save();
            if($user->email) {
                Mail::to($user->email)->send(new SendTrialExpirationMail($user));
            }
        }
    }
}
