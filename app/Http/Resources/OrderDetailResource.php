<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        return [
            'id'              => $this->id,
            'order_date'      => Carbon::parse($this->created_at)->format('F d, Y'),
            'paytment_method' => $this->payment_method,
            'order_id'        => $this->order_id,
            'status'          => $this->status,
            'items'           => OrderItemResource::collection($this->items),
            'user'            => new OrderUserResource($this->user->address->first()),
        ];
    }
}
