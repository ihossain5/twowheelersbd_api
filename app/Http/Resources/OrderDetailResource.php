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
            'id'                      => $this->id,
            'order_date'              => Carbon::parse($this->created_at)->format('F d, Y'),
            'order_id'                => $this->order_id,
            'status'                  => $this->status,
            'order_note'              => $this->note ?? 'N/A',
            'order_refund_note'       => $this->refund_cause ?? 'N/A',
            'order_cancelation_cause' => $this->cancelation_cause ?? 'N/A',
            'paytment_method'         => $this->payment_method,  
            'items'                   => OrderItemResource::collection($this->items),
            'user'                    => new OrderUserResource($this->user->address->first()),
            'sub_total'              => $this->sub_total,
            'delivery_charge'        => $this->delivery_charge,
            'discount'               => $this->discount,
            'total'                  => $this->total,
        ];
    }
}
