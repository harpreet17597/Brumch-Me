<?php

namespace App\Http\Requests\Api;

use App\Helpers\CommonHelper;
use App\Rules\Digits;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\LatitudeLongitude;

class UpdateProfileRequest extends FormRequest
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
        $rules =  [
            'name'              => ['nullable', 'string','min:2','max:500'],
            'email'             => ['nullable','email','min:3','max:500','unique:users,email,'.auth()->user()->id.',id'],
            'street_address'    => ['nullable','string','min:1','max:1000'],
            'lat'               => ['nullable', new LatitudeLongitude],
            'lng'               => ['required_with:lat', new LatitudeLongitude],      
            'profile_image'     => ['nullable','image','mimes:jpeg,png,jpg,gif,svg','max:10240']

        ];

        if(request()->has('user_type')) {
           $user_type = request()->get('user_type');
           if($user_type == 'customer') {
                $rules = array_merge($rules,[
                    'dob' => ['nullable', 'date_format:Y-m-d'],
                ]);
           }
           
           if($user_type == 'business') {
                $rules = array_merge($rules,[
                   'restaurant_opening_time' => ['naullable','string', 'date_format:H:i'],
                   'restaurant_closing_time' => ['required_with:restaurant_opening_time','string', 'date_format:H:i','after:restaurant_opening_time'],
                   'restaurant_max_table'    => ['nullable','integer','min:1','max:1000000'],
                ]);
           }
        }

        return $rules;
    }

    /**
     * Error messages for defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [

            'name.required'      => 'Please enter your first name.',
            'name.min'           => 'First name should be at least :min characters.',
            'name.max'           => 'First name must not be more than :max characters.',
            'email.unique'       => 'This email address has already been taken. Please try another one.',
            'email.required'     => 'Please enter email.',
            'phone.email'        => 'Please enter valid email address',
            'fcm_token.required' => 'FCM token is required.',
        ];
    }

    /**
     * get validated input
     *
     * @return array
     */
    public function getValidatedData()
    {
        $formData = $this->only([
     
            'name',
            'email',
            'dob',
            'phone_country',
            'country_code_text',
            'street_address',
            'lat',
            'lng',
            'restaurant_opening_time',
            'restaurant_closing_time',
            'restaurant_max_table'
        ]);

        return $formData;
    }

}
