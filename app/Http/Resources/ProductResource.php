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
            'sub_category' => $this->subcategory,
            // 'category' => $this->subcategory->category,
            'name' => $this->name,
            'quantity' => $this->quantity,
            'discount_type' => $this->discount_type,
            'discount' => $this->discount,
            'regular_price' => $this->regular_price,
            'discounted_price' => $this->discounted_price,
            'is_available' => $this->is_available == 1 ? 'Available' : 'Not Avaialable',
            'image' =>  $this->addUrl(collect(json_decode($this->images))->take(1)),
        ];
    }

    private function addUrl($images){
        $data = [];
        foreach ($images as $image){
            $data[] = BASE_URL().$image;
        }
        return $data;
    }
}
