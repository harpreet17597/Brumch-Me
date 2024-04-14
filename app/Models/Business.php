<?php

namespace App\Models;
use App\Scopes\BusinessScope;
use App\Models\User;

class Business extends User
{
    protected $table = 'users';
    protected static function boot()
	{
		parent::boot();
		static::addGlobalScope(new BusinessScope);
	}
}    
