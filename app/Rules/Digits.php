<?php

namespace App\Rules;

use App\Helpers\CommonHelper;
use Illuminate\Contracts\Validation\Rule;

class Digits implements Rule
{
    public $country_code_text;

    public $landline;
    public $country_code;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($country_code_text = 'au')
    {
        $this->landline = ['3', '2', '7', '8'];
        $this->country_code = $country_code_text;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if ($this->country_code === 'au' || $this->country_code === 'AU') {
            $mobile = str_replace(' ', '', str_replace(')', '', str_replace('(', '', str_replace('-', '', $value))));
            $mobile = CommonHelper::updatePhone($mobile);
            $first_char = mb_substr($mobile, 0, 1);
            return ! (in_array($first_char, $this->landline));
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Please enter valid mobile number.';
    }
}
