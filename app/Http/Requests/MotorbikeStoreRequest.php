<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MotorbikeStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|max:255',
            'is_visible'=> 'required',
            'discount_type'=> 'required',
            'brand_id'=> 'required',
            'brand_model_id'=> 'required',
            'sub_category_id'=> 'required',
            'quantity'=> 'required',
            'regular_price'=> 'required',
            'description'=> 'required',
            'video' => 'mimes:mp4|max:5120',
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpg,jpeg,png|max:1024',
        ];
    }
}
