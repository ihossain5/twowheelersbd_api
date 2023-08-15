<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BrandModelResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->info,
            'images' =>  addUrl(collect(json_decode($this->images))),
            'video' => $this->video,
            'catelogue_pdf' => BASE_URL(). $this->catelogue_pdf,
            'catelogues' => BrandModelCatelogueResource::collection($this->catelogues),
            'colors' => BrandModelColorResource::collection($this->colors),
            'specifications' => BrandModelSpicificationResource::collection($this->specifications),
            'products' => $this->when($request->routeIs('model.products'), ProductResource::collection($this->products)),
            'total_products' => $this->products->count(),
        ];
    }
}
