<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\RestaurantTableBooking;

class AvailabilityTimeSlotRequest extends FormRequest
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
            
            'availability_date' => ['required', 'date_format:Y-m-d','after_or_equal:today'],
            'time_slot_from'    => ['required', 'date_format:H:i'],
            'time_slot_to'      => ['required', 'date_format:H:i','after:time_slot_from'],
        ];
    }

}
