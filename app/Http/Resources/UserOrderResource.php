<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserOrderResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        return [
            'order_id'               => $this->order_id,
            'status'                 => $this->status,
            'order_date'             => formatDate($this->created_at),
            'estimate_delivery_date' => $this->when($request->routeIs('order.track'), formatDate($this->created_at) . ' - ' . formatDate($this->estimate_delivery_time)),
            'order_ship_date'        => $this->when($request->routeIs('order.track'), $this->shipped_at ? formatDate($this->shipped_at) : 'N/A'),
            'order_deliver_date'        => $this->when($request->routeIs('order.track'), $this->delivered_at ? formatDate($this->delivered_at) : 'N/A'),
            'order_cancel_date'        => $this->when($request->routeIs('order.track'), $this->canceled_at ? formatDate($this->canceled_at) : 'N/A'),
            'shipping_info'        => $this->when($request->routeIs('order.track'), $this->info ),
        ];
    }
}
