<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Business;

class SubscriptionStatus extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'subscription_status';

    public function business() {
        return $this->belongsTo(Business::class,'user_id');
    }
}
