<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HotDealProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'hot_deal_id' => $this->hot_deal_id,
            'product_name' => $this->product->name,
            'regular_price' => $this->product->regular_price,
            'percentage' => $this->percentage,
            'discounted_price' => $this->discounted_price,
        ];
    }
}
