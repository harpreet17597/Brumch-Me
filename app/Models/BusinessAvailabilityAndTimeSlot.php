<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessAvailabilityAndTimeSlot extends Model
{
    use HasFactory;

    protected $fillable = ['business_id','availability_date','time_slot_from','time_slot_to'];
}
