<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\RestaurantTableBooking;

class BookingConfirmRejectRequest extends FormRequest
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
            
            'booking_number' => ['required','string','min:5','max:500'],
            'status'         => ['required','in:'.RestaurantTableBooking::BOOKING_CANCELLED.','.RestaurantTableBooking::BOOKING_CONFIRMED],
        ];
    }

}
