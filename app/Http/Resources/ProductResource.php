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
            'catelogue_pdf' => $this->catelogue_pdf,
            'images' =>  addUrl(collect(json_decode($this->images))),
            'video' => $this->video,
            'quantity' => $this->quantity,
            'discount_type' => $this->discount_type,
            'discount' => $this->discount,
            'regular_price' => $this->regular_price,
            'discounted_price' => $this->discounted_price,
            'is_available' => $this->is_available == 1 ? 'Available' : 'Not Avaialable',
            'sub_category' => $this->subcategory,
            'brand' => $this->brand_id !== null ? new BrandResource($this->brand) : 'ALL',
            'model' => $this->brand_model_id !== null ? new BrandModelResource($this->model) : 'ALL',
            'catelogues' => $this->catelogues,
            'specifications' => $this->specifications,
            
        ];
    }
}
