<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        return [
            'id'                      => $this->id,
            'coupon_code'             => $this->coupon_code,
            'minimum_purchase_amount' => $this->minimum_purchase_amount,
            'coupon_type'             => $this->coupon_type,
            'amount'                  => $this->amount,
            'status'                  => $this->status,
        ];
    }
}
