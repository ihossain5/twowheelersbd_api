<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'product_id' => $this->product_id,
            'product_title' => $this->product->name,
            'product_image' => addUrl(collect(json_decode($this->product->images))),
            'size' => $this->size,
            'color' => $this->color,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'total_price' => $this->total_price,
        ];
    }
}