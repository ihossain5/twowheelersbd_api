<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HotDealResource extends JsonResource
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
            'title' => $this->title,
            'banner' => $this->banner,
            'shop_name' => $this->shop->name,
            'shop_logo' => $this->shop->logo,
            'products' => $this->when($request->routeIs('vendor.edit.deal'), HotDealProductResource::collection($this->products)),
        ];
    }
}
