<?php

namespace App\Http\Requests\Api;

use App\Rules\Digits;
use Illuminate\Foundation\Http\FormRequest;

class VerifyOtpRequest extends FormRequest
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
            'user_type'         => ['required','in:customer,business'],
            'country_code_text' => ['required_with:phone', 'string'],
            'phone' => ['required', 'numeric'],//'phone:country_code_text', new Digits($this->country_code_text)
            'phone_country' => ['required_with:phone', 'string'],
            'otp' => ['required', 'digits:4'],
            'fcm_token' => ['nullable','string'],
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
            'phone_country.required_with' => 'Please select the country code',
            'country_code_text.required_with' => 'Please provide country code',
            'phone.required' => 'Please enter your Cell phone number.',
            'phone.numeric' => 'Cell phone number must contain only numbers.',
            'phone.digits' => 'Cell phone number must be :digits digits.',
            'phone.phone' => 'Please enter valid mobile number.',
            'otp.required' => 'Please enter OTP.',
            'otp.digits' => 'Please enter a valid OTP.',
            'fcm_token.required' => 'Please enter fcm token.',
        ];
    }
}
