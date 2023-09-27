<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'images'        => addUrl(collect(json_decode($this->images))->take(1)),
            'selling_price' => $this->selling_price,
            'rating'        => $this->average_rating,

            // 'category'          => $this->subcategory->category->name,
            // 'sub_category'      => $this->subcategory->name,
            // 'shop'              => $this->shop->name,
            // 'shop_logo'         => $this->shop->logo,
            // 'brand'             => $this->brand_id !== null ? $this->brand->name : 'ALL',
            // 'model'             => $this->brand_model_id !== null ? $this->model->name : 'ALL',
            // 'description'       => $this->description,
            // 'sku'               => $this->sku,
            // 'additional_name_1' => $this->additional_name_1,
            // 'additional_name_2' => $this->additional_name_2,
            // 'additional_name_3' => $this->additional_name_3,
            // 'additional_name_4' => $this->additional_name_4,
            // 'additional_name_5' => $this->additional_name_5,
            // 'colors'            => json_decode($this->colors),
            // 'sizes'             => json_decode($this->sizes),
            // 'catelogue_pdf'     => $this->catelogue_pdf,
            // 'images'            => addUrl(collect(json_decode($this->images))),
            // 'video'             => $this->video,
            // 'quantity'          => $this->quantity,
            // 'discount_type'     => $this->discount_type,
            // 'discount'          => $this->discount,
            // 'regular_price'     => $this->regular_price,
            // 'discounted_price'  => $this->discounted_price,

            // 'status'            => $this->status,
            // 'is_available'      => $this->is_available == 1 ? 'Available' : 'Not Avaialable',
            // 'catelogues'        => ProductCatelogueResource::collection($this->catelogues),
            // 'specifications'    => ProductSpecificationResource::collection($this->specifications),
            // 'motors'            => ProductMotorResource::collection($this->motors),

        ];
    }
}
