<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MotorbikeEditResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        return [
            'id'               => $this->id,
            'sku'              => $this->sku,
            'category'         => $this->subcategory->category->name,
            'condition'        => $this->subcategory->name,
            'brand_id'         => $this->brand->id ?? 'NULL',
            'brand'            => $this->brand->name ?? 'ALL',
            'model'            => $this->model->name ?? 'ALL',
            'model_id'         => $this->model->id ?? 'NULL',
            'is_visible'       => $this->is_visible,
            'mileage'          => $this->mileage,
            'description'      => $this->description,
            'quantity'         => $this->quantity,
            'discount_type'    => $this->discount_type,
            'discount'         => $this->discount,
            'regular_price'    => $this->regular_price,
            'discounted_price' => $this->discounted_price,
            'images'           => addUrl(collect(json_decode($this->images))),
            'video'            => $this->video,
        ];
    }
}
