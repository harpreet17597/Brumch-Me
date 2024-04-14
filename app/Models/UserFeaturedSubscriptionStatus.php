<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFeaturedSubscriptionStatus extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','status','start_date','end_date','start_date_unix','end_date_unix'];

    public function user() {
        return $this->belongsTo(User::class,'user_id');
    }
}
