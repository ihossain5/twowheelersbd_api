<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VendoProductResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'category'     => $this->subcategory->category->name,
            'sub_category' => $this->subcategory->name,
            'sku'          => $this->sku,
            'images'       => addUrl(collect(json_decode($this->images))),
            'quantity'     => $this->quantity,
            'price'        => $this->selling_price,
            'status'       => $this->status ?? 'PENDING',
            'is_visible'   => $this->is_visible == 1 ? 'Visible' : 'Not Visible',
        ];  
    }
}
