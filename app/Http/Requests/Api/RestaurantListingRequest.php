<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\LatitudeLongitude;

class RestaurantListingRequest extends FormRequest
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
        $rules = [
            'tag'  => 'sometimes|exists:tags,id',
            'type' => 'sometimes|in:recomended,nearby',  
        ];

        if(request()->has('type') && request()->get('type') == 'nearby') {
            $rules = array_merge($rules,[

                'latitude'  => ['required', new LatitudeLongitude],
                'longitude' => ['required', new LatitudeLongitude]
            ]);
        }

        return $rules;
    } 

}
