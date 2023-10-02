<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MotorbikeResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        return [
            'id'               => $this->id,
            'category'         => $this->subcategory->category->name,
            'condition'        => $this->subcategory->name,
            'shop_id'             => $this->shop->id,
            'shop'             => $this->shop->name,
            'shop_logo'        => $this->shop->logo,
            'brand'            => $this->brand_id !== null ? $this->brand->name : 'ALL',
            'model'            => $this->brand_model_id !== null ? $this->model->name : 'ALL',
            'name'             => $this->name,
            'description'      => $this->description,
            'sku'              => $this->sku,
            'mileage'          => $this->mileage,
            'catelogue_pdf'    => $this->model->catelogue_pdf,
            'images'           => addUrl(collect(json_decode($this->images))),
            'video'            => $this->video,
            'quantity'         => $this->quantity,
            'discount_type'    => $this->discount_type,
            'discount'         => $this->discount,
            'regular_price'    => $this->regular_price,
            'discounted_price' => $this->discounted_price,
            'status'           => $this->status,
            'is_available'     => $this->is_available == 1 ? 'Available' : 'Not Avaialable',
            'catelogues'       => ProductCatelogueResource::collection($this->model->catelogues),
            'specifications'   => ProductSpecificationResource::collection($this->model->specifications),

        ];
    }
}
