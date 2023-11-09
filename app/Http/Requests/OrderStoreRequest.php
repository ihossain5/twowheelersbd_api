<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderStoreRequest extends FormRequest
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
            'name' => 'required|max:100',
            'mobile' => 'required|max:11|min:11',
            'email' => 'required|max:50|email',
            'division' => 'required|max:50',
            'district' => 'required|max:50',
            'address' => 'required',
            'shops' => 'array',
            'shops.*.*.product_id' => 'required|numeric',
            'shops.*.*.price' => 'required|numeric',
            'shops.*.*.quantity' => 'required|numeric',
            'shops.*.*.total_price' => 'required|numeric',
            'shops.*.*.size' => 'nullable',
            'shops.*.*.color' => 'nullable',
        ];
    }

}
