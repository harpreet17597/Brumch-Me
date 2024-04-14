<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;

class SmsOtp extends Model
{
    use HasFactory;
    use Prunable;

    public const STATUS_ACTIVE = 1;

    public const STATUS_INACTIVE = 2;

    public const DEFAULT_OTP = 111111;

    /**
     * Fields can be mass assigned.
     */
    protected $fillable = [

        'type_id',

        'otp',

        'status',

        'type',

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

    /**
     * Get the prunable model query.
     *
     *
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function prunable()
    {
        return static::where('created_at', '<=', now()->subMonth());
    }
}
