<?php

namespace App\Http\Requests\Api;

use App\Helpers\CommonHelper;
use App\Rules\Digits;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\LatitudeLongitude;

class RegisterRequestNew extends FormRequest
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
            'user_type'         => ['required','in:customer,business'],
            'name'              => ['required', 'string','min:2','max:500'],
            'email'             => ['required', 'unique:users,email', 'email','min:3','max:500'],
            'country_code_text' => ['required_with:phone', 'string'],
            'phone'             => ['required', 'unique:users,phone', 'numeric'],//, 'phone:country_code_text', new Digits($this->country_code_text)
            'phone_country'     => ['required_with:phone', 'string','min:2','max:10'],
            'street_address'    => ['required','string','min:1','max:1000'],
            'lat'               => ['required', new LatitudeLongitude],
            'lng'               => ['required', new LatitudeLongitude],      
            'fcm_token'         => ['required'],
            'profile_image'     => ['nullable','image','mimes:jpeg,png,jpg,gif,svg','max:10240']

        ];

        if(request()->has('user_type')) {
           $user_type = request()->get('user_type');
           if($user_type == 'customer') {
                $rules = array_merge($rules,[
                    'dob' => ['required', 'date_format:Y-m-d'],
                ]);
           }
           
           if($user_type == 'business') {
                $rules = array_merge($rules,[
                   'restaurant_opening_time' => ['required','string', 'date_format:H:i'],
                   'restaurant_closing_time' => ['required','string', 'date_format:H:i','after:restaurant_opening_time'],
                   'restaurant_max_table'    => ['required','integer','min:1','max:1000000'],
                   
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

            'name.required' => 'Please enter your first name.',
            'name.min' => 'First name should be at least :min characters.',
            'name.max' => 'First name must not be more than :max characters.',

            'phone_country.required_with' => 'Please select the country code',
            'country_code_text.required_with' => 'Please provide country code',
            'phone.required' => 'Please enter your Cell phone number.',
            'phone.numeric' => 'Cell phone number must contain only numbers.',
            'phone.digits' => 'Cell phone number must be :digits digits.',
            'phone.unique' => 'The mobile number already exists in the system. ',
            'phone.phone' => 'Please enter valid mobile number.',
            'email.unique' => 'This email address has already been taken. Please try another one.',
            'email.required' => 'Please enter email.',
            'phone.email' => 'Please enter valid email address',
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
            'user_type',
            'name',
            'email',
            'phone',
            'dob',
            'phone_country',
            'country_code_text',
            'fcm_token',
            'street_address',
            'lat',
            'lng',
            'restaurant_opening_time',
            'restaurant_closing_time',
            'restaurant_max_table'
        ]);

        $formData['password'] = Hash::make($this->password);
        $formData['remember_token'] = Str::random(10);
        // $formData['timezone'] = 'America/New_York';

        return $formData;
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'username' => Str::lower($this->username),
        ]);
    }

    public function all($keys = null)
    {
        $data = parent::all();
        $data['phone'] = CommonHelper::updatePhone($data['phone']);
        return $data;
    }
}
