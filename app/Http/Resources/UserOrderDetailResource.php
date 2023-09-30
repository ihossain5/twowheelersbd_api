<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserOrderDetailResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        return [
            'order_id'               => $this->order_id,
            'order_note'             => $this->note,
            'status'                 => $this->status,
            'payment_method'         => $this->payment_method,
            'order_date'             => formatDate($this->created_at),
            'estimate_delivery_date' => formatDate($this->created_at) . ' - ' . formatDate($this->estimate_delivery_time),
            'shop'                   => $this->shop?->name,
            'items'                  => OrderItemResource::collection($this->items),
            'sub_total'              => $this->sub_total,
            'delivery_charge'        => $this->delivery_charge,
            'discount'               => $this->discount,
            'total'                  => $this->total,
            'shipping_address'       => new OrderUserResource($this->user->address->first()),
        ];
    }
}
