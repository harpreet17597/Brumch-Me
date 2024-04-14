<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class RestaurantWishlistRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            
            'restaurant_id' => ['required','integer','exists:restaurants,id'],
        ];
    }

    /**
     * Get error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [

            'restaurant_id.required'  => 'restaurant id is required.',
            'restaurant_id.integer'   => 'restaurant id must be integer',
            'restaurant_id.exists'    => 'invalid restaurant id.',
  
        ];
    }

}
