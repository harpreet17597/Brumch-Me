<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

class RestaurantImage extends Model
{
    use HasFactory;

    protected $imageFolderPath = '/uploads/restaurants/';
    protected $fillable = ['restaurant_id','restaurant_image'];
    
    /**
     *  ACCESSOR TO GET FULL IMAGE PATH
    */
    public function getRestaurantImageAttribute($value) {
        if(!is_null($value) && !empty($value)) {
            $folder = $this->imageFolderPath;
            $file_path = public_path($folder.$value);
            if (File::exists($file_path)) {
                return asset($folder.$value);    
            }
            
        }
        return asset('uploads/banner-default.png');
    }
}
