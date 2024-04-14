<?php

namespace App\Traits\Auth;

trait MustVerifyProfile
{
    /**
     * Determine if the admin has verified user profile.
     *
     * @return bool
     */
    public function hasVerifiedProfile()
    {
        return $this->is_verified == 1;
    }

    /**
     * Determine if the profile is suspended by admin
     *
     * @return bool
     */ 
    public function isProfileSuspended()
    {
        return $this->is_suspended == 1;
    }

    /**
     * Determine if the profile is deleted
     *
     * @return bool
     */ 
    public function isAccountDeleted()
    {
        return !is_null($this->deleted_at);
    }

    /**
     * Mark the given user's phone as verified.
     *
     * @return bool
     */
    public function markProfileAsVerified()
    {
        return $this->forceFill([
            'is_verified' => 1,
            'verified_at' => $this->freshTimestamp(),
        ])->save();
    }

    /**
     * Mark the given user's profile suspended
     *
     * @return bool
     */
    public function markProfileAsSuspended()
    {
        return $this->forceFill([
            'is_suspended' => 1,
            'suspended_at' => $this->freshTimestamp(),
        ])->save();
    }

    /**
     * Mark the given user's profile unsuspended
     *
     * @return bool
     */
    public function markProfileAsUnSuspended()
    {
        return $this->forceFill([
            'is_suspended' => 0,
            'suspended_at' => NULL,
        ])->save();
    }

}
