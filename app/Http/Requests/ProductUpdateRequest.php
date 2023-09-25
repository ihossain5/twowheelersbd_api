<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductUpdateRequest extends FormRequest
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
            'name' => 'required',
            'discount_type' => 'required',
            'sub_category_id'=> 'required',
            'quantity'=> 'required',
            'regular_price'=> 'required',
            'description'=> 'required',
            'is_available'=> 'required',
            'is_visible'=> 'required',
            'colors' => 'array',
            'sizes' => 'array',
            'specifications' => 'array',
            'catelogues' => 'array',
            'catelogues.*.image' => 'image|mimes:jpg,jpeg,png|max:1024',
            'motors' => 'array',
            'images' => 'array',
            'images.*' => 'image|mimes:jpg,jpeg,png|max:1024',
            'catelogue_pdf' => 'mimes:pdf|max:3072',
            'video' => 'mimes:mp4|max:5120',
          
        ];
    }
}
