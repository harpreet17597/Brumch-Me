<?php

namespace App\Http\Requests\Api;

use App\Helpers\CommonHelper;
use App\Rules\Digits;
use Illuminate\Foundation\Http\FormRequest;

class OtpRequestwithoutSignup extends FormRequest
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
            'phone' => ['required','numeric'], // 'phone:country_code_text', new Digits($this->country_code_text)
            'phone_country' => ['required'],
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

            'phone.required' => 'Please enter your Mobile number.',
            'phone.numeric'  => 'Mobile number must contain only numbers.',
            'phone.digits'  => 'Mobile number must be :digits digits.',
            'phone.unique' => 'The mobile number already exists in the system.',
            'phone.phone' => 'Please enter valid mobile number.',
        ];
    }

    public function all($keys = null)
    {
        $data = parent::all();
        if(isset($data['phone'])){
            $data['phone'] = CommonHelper::updatePhone($data['phone']);
        }

        return $data;
    }
}
