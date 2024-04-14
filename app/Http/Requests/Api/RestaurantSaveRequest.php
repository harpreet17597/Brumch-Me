<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class RestaurantSaveRequest extends FormRequest
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
            'business_id'                    => 'nullable|integer|exists:users,id',
            'restaurant_name'                => 'required|string|min:2|max:500',
            'restaurant_description'         => 'required|string|min:2|max:50000',
            'restaurant_menus'               => 'required|array',
            'restaurant_menus.*.name'        => 'required|string|min:2|max:500',
            'restaurant_menus.*.price'       => 'required|integer|min:2|max:100000',
            'restaurant_menus.*.quantity'    => 'required|string|in:full,half',
            'restaurant_menus.*.description' => 'required|string|min:2|max:5000',
            'menu_images'                    => 'nullable|array',
            'restaurant_images'              => 'nullable|array',
            'restaurant_images.*'            => 'mimes:jpeg,png,jpg,gif,svg', 'max:100000',
            'menu_images.*'                  => 'mimes:jpeg,png,jpg,gif,svg', 'max:100000',
            'has_dress_code'                 => 'nullable|in:yes,no',
            'dress_code'                     => 'nullable|required_if:has_dress_code,yes|string|min:2|max:3000',
            'tags'                           => 'required|array',
            'tags.*'                         => 'required|exists:tags,id'
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $data = $this->all();
        // Manipulate the request data
        $data['restaurant_menus'] = is_array($this->restaurant_menus) ? $this->restaurant_menus : json_decode($this->restaurant_menus,true);
        $data['tags']             = is_array($this->tags) ? $this->tags : json_decode($this->tags,true);
        $this->replace($data);
    }

}
