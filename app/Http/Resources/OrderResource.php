<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        return [
            'id'                   => $this->id,
            'order_id'             => $this->order_id,
            'sub_total'            => $this->sub_total,
            'delivery_charge'      => $this->delivery_charge,
            'discount'             => $this->discount,
            'two_wheel_commission' => $this->two_wheel_commission,
            'total'                => $this->total,
            'status'               => $this->status,
            'is_paid'              => $this->is_paid == 1 ? 'Yes' : 'No',
            'is_deliver_by_admin'  => $this->is_deliver_by_admin == 1 ? 'Yes' : 'No',
        ];
    }
}
