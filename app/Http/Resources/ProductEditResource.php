<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductEditResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        return [
            'id'                => $this->id,
            'sku'               => $this->sku,
            'category'          => $this->subcategory->category->name,
            'sub_category'      => $this->subcategory->name,
            'brand'             => $this->brand->name,
            'model'             => $this->model->name,
            'additional_name_1' => $this->additional_name_1,
            'additional_name_2' => $this->additional_name_2,
            'additional_name_3' => $this->additional_name_3,
            'additional_name_4' => $this->additional_name_4,
            'additional_name_5' => $this->additional_name_5,
            'is_visible'        => $this->is_visible,
            'is_available'      => $this->is_available,
            'description'       => $this->description,
            'colors'            => json_decode($this->colors),
            'sizes'             => json_decode($this->sizes),
            'specifications'    => ProductSpecificationResource::collection($this->specifications),
            'catelogue_pdf'     => $this->catelogue_pdf,
            'catelogues'        => ProductCatelogueResource::collection($this->catelogues),
            'quantity'          => $this->quantity,
            'discount_type'     => $this->discount_type,
            'discount'          => $this->discount,
            'regular_price'     => $this->regular_price,
            'discounted_price'  => $this->discounted_price,
            'images'            => addUrl(collect(json_decode($this->images))),
            'video'             => $this->video,
            'similar_motor'     => ProductMotorResource::collection($this->motors),
        ];
    }
}
