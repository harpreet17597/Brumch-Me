<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class RestaurantTableBookingRequest extends FormRequest
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
            
            'restaurant_id'     => ['required','integer','exists:restaurants,id'],
            'booking_date'      => ['required', 'date_format:Y-m-d','after_or_equal:today','before:'.now()->addMonths(3)->format('Y-m-d')],
            'booking_from_time' => ['required', 'date_format:H:i'],
            'booking_to_time'   => ['required', 'date_format:H:i'],//'after:booking_from_time'],
            'number_of_persons' => ['required','integer','min:1','max:1000']
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

            'booking_date.required'       => 'booking date is required.',
            'booking_date.date_format'    => 'booking date must be of fomat Y-m-d',
            'booking_date.after_or_equal' => 'booking date must be equal or greater than today',

            'booking_time.required'       => 'booking time is required.',
            'booking_time.date_format'    => 'booking time must be of fomat H:i',
            'booking_time.after_or_equal' => 'booking time must be equal or greater than current time',

            'number_of_persons.required'  => 'please specify number of persons.',
            'number_of_persons.integer'   => 'number of persons must be of integer type.',
            'number_of_persons.min'       => 'minimun 1 value is required',
            'number_of_persons.max'       => 'minimun 1000 value is required',
  
        ];
    }

}
