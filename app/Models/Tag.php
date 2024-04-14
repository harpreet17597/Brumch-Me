<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;
    protected $fillable = ['name'];

    /**
     * **************************************************************
     *  ACCESSOR TO GET FULL IMAGE PATH
     * **************************************************************
    * */
    public function getIsSelectedAttribute($value) {
       
        return $value ? true : false;
    }
}
