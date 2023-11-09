<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BrandModelResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        if ($request->routeIs('brands')) {
            return [
                'id'             => $this->id,
                'name'           => $this->name,
                'start_year'     => Carbon::parse($this->year_form)->format('Y-m-d'),
                'end_year'       => Carbon::parse($this->year_to)->format('Y-m-d'),
                'images'         => addUrl(collect(json_decode($this->images))->take(1)),
                'total_products' => $this->products->count(),
            ];
        }

        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'images'         => addUrl(collect(json_decode($this->images))),
            'total_products' => $this->products->count(),

            'description'    => $this->info,
            'video'          => $this->video,
            'catelogue_pdf'  => BASE_URL() . $this->catelogue_pdf,
            'catelogues'     => BrandModelCatelogueResource::collection($this->catelogues),
            'colors'         => BrandModelColorResource::collection($this->colors),
            'specifications' => BrandModelSpicificationResource::collection($this->specifications),
            'products'       => $this->when($request->routeIs('model.products'), ProductResource::collection($this->products)),

        ];
    }
}
