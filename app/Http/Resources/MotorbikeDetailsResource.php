<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MotorbikeDetailsResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        return [
            'id'               => $this->id,
            'model_id'         => $this->model?->id,
            'name'             => $this->name,
            'images'           => addUrl(collect(json_decode($this->images))),
            'regular_price'    => $this->regular_price,
            'discounted_price' => $this->selling_price,
            'discount'         => $this->discount,
            'rating'           => $this->average_rating,
            'total_reviews'    => $this->reviews_count,
            'category'         => $this->subcategory?->category->name,
            'condition'        => $this->subcategory?->name,
            'shop_id'          => $this->shop?->id,
            'shop'             => $this->shop?->name,
            'shop_logo'        => $this->shop?->logo,
            'description'      => $this->description,
            'publish_date'     => formatDate($this->created_at),
            'sku'              => $this->sku,
            'mileage'          => $this->mileage,
            'catelogue_pdf'    => $this->model?->catelogue_pdf,
            'video'            => $this->video,
            'quantity'         => $this->quantity,
            'catelogues'       => ProductCatelogueResource::collection($this->model->catelogues),
            'specifications'   => ProductSpecificationResource::collection($this->model->specifications),
            'reviews'          => ReviewResource::collection($this->reviews),
            // 'accessories'      => ProductResource::collection($this->model->products),
        ];
    }
}
