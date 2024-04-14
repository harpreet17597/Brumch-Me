<?php

namespace App\Http\Requests\Api;

use Illuminate\Support\Str;
use Illuminate\Foundation\Http\FormRequest;

class MobileRegisterRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'phone' => ['required', 'unique:users,phone', 'numeric', 'phone:US,IN'],
            'phone_country' => ['required_with:phone', 'string'],

        ];
    }

    /**
     * Error messages for defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'phone_country.required_with' => 'Please select the country code',
            'phone.required'              => 'Please enter your Cell phone number.',
            'phone.numeric'               => 'Cell phone number must contain only numbers.',
            'phone.digits'                => 'Cell phone number must be :digits digits.',
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
            'phone',
            'phone_country',
        ]);
        $formData['remember_token'] = Str::random(10);

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
}
