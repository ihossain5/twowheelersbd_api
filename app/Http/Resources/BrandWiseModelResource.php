<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BrandWiseModelResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        if ($request->routeIs('brand.category.models')) {
            return [
                'brand_category_id'   => $this->id,
                'brand_category_name' => $this->name,
                'models'              => MotorbikeModelResource::collection($this->models),
            ];
        }
        return [
            'brand_id'   => $this->id,
            'brand_name' => $this->name,
            'models'     => MotorbikeModelResource::collection($this->models),
        ];
    }
}
