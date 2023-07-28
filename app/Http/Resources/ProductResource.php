<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
          
            // 'category' => $this->subcategory->category,
            'name' => $this->name,
            'sku' => $this->sku,
            'additional_names' => json_decode($this->additional_names),
            'colors' => json_decode($this->colors),
            'sizes' => json_decode($this->sizes),
            'quantity' => $this->quantity,
            'discount_type' => $this->discount_type,
            'discount' => $this->discount,
            'regular_price' => $this->regular_price,
            'discounted_price' => $this->discounted_price,
            'is_available' => $this->is_available == 1 ? 'Available' : 'Not Avaialable',
            'sub_category' => $this->subcategory,
            'brand' => new BrandResource($this->brand),
            'images' =>  addUrl(collect(json_decode($this->images))),
        ];
    }
}
