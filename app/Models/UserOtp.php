<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class UserOtp extends Model
{
    use HasFactory;
  
    protected $table = 'user_otps';

    public const STATUS_ACTIVE   = 1;
    public const STATUS_INACTIVE = 2;
    public const DEFAULT_OTP     = 111111;

    /**
     * Fields can be mass assigned.
     */
    protected $fillable = [
        'user_id',
        'phone_country',
        'phone',
        'otp',
        'status',
        'sent_at'
    ];

    protected $casts = [
        'sent_at' => 'datetime'
    ];

    /**
     * get user who requested OTP.
     *
     *
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
