<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Restaurant;
use App\Models\User;

class RestaurantWishlist extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','restaurant_id'];

    public function user() {
        return $this->belongsTo(User::class,'user_id');
    }

    public function restaurant() {
        return $this->belongsTo(Restaurant::class,'restaurant_id');
    }

}
