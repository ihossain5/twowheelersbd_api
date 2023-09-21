<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CouponRequest extends FormRequest
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
        if($this->routeIs('vendor.shop.update')){
            $rules = [
                'coupon_code' => 'required|string|max:255|unique:coupons,coupon_code, '. $this->id,
                'minimum_purchase_amount' => 'required',
                'coupon_type' => 'required',
                'amount' => 'required',
                'status' => 'required',
            ];
        }else{
            $rules = [
                'coupon_code' => 'required|string|max:255|unique:coupons',
                'minimum_purchase_amount' => 'required',
                'coupon_type' => 'required',
                'amount' => 'required',
                'status' => 'required',
            ];
        }

        return $rules;
    }
}
