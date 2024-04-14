<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

class RestaurantMenu extends Model
{
    use HasFactory;
    protected $imageFolderPath = '/uploads/restaurants/menus/';
    protected $fillable = ['restaurant_id','restaurant_menu_name','restaurant_menu_price','restaurant_menu_quantity','restaurant_menu_description','restaurant_menu_image'];

    /**
     *  ACCESSOR TO GET FULL IMAGE PATH
    */
    public function getRestaurantMenuImageAttribute($value) {
        if(!is_null($value) && !empty($value)) {
            $folder = $this->imageFolderPath;
            $file_path = public_path($folder.$value);
            if (File::exists($file_path)) {
                return asset($folder.$value);    
            }      
        }
        return asset('uploads/menu-default.png');
    }
}
