<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class LatitudeLongitude implements Rule
{
    public function passes($attribute, $value)
    {
        // Validate latitude
    
        if (strpos($attribute, 'latitude') !== false) {
            return is_numeric($value) && $value >= -90 && $value <= 90;
        }

        // Validate longitude
        if (strpos($attribute, 'longitude') !== false) {
            return is_numeric($value) && $value >= -180 && $value <= 180;
        }

        if (strpos($attribute, 'lat') !== false) {
            return is_numeric($value) && $value >= -90 && $value <= 90;
        }

        // Validate longitude
        if (strpos($attribute, 'lng') !== false) {
            return is_numeric($value) && $value >= -180 && $value <= 180;
        }

        return false;
    }

    public function message()
    {
        return 'The :attribute must be a valid latitude or longitude.';
    }
}
