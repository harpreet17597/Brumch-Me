<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFeaturedSubscription extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','payment_id','amount','currency','charge_id','payment_intent','payment_method','balance_transaction','status','start_date','end_date','start_date_unix','end_date_unix'];
}
