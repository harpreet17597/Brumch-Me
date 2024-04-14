<?php

namespace App\Models;
use App\Scopes\CustomerScope;
use App\Models\User;

class Customer extends User
{
    protected $table = 'users';
    protected static function boot()
	{
		parent::boot();
		static::addGlobalScope(new CustomerScope);
	}
}    
