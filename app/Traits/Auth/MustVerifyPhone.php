<?php

namespace App\Traits\Auth;

trait MustVerifyPhone
{
    /**
     * Determine if the user has verified their phone.
     *
     * @return bool
     */
    public function hasVerifiedPhone()
    {
        return null !== $this->phone_verified_at;
    }

    /**
     * Mark the given user's phone as verified.
     *
     * @return bool
     */
    public function markPhoneAsVerified()
    {
        return $this->forceFill([
            'phone_verified_at' => $this->freshTimestamp(),
        ])->save();
    }

    /**
     * Send the phone verification notification.
     *
     * @return void
     */
    public function sendPhoneVerificationNotification()
    {
        //need to implement logic
    }

    /**
     * Determine if the user is logged in
     *
     * @return bool
     */
    public function isLoggedIn()
    {
        return $this->is_login == '1';
    }

    /**
     * Determine if the user is logged in
     *
     * @return bool
     */
    public function setLoggedIn()
    {
        return $this->forceFill([
            'is_login' => '1',
        ])->save();
    }

    public function setLoggedOut()
    {
        return $this->forceFill([
            'is_login' => '0',
        ])->save();
    }
}
