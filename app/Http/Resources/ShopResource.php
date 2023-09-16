<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShopResource extends JsonResource
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
            'logo' => $this->logo,
            'photo' => $this->photo,
            'description' => $this->discription,
            'division' => $this->division,
            'owner_name' => $this->owner->name,
            'delivery_charge' => $this->delivery_charge,
            'commission_rate' => $this->commission_rate,
            'rating' => $this->average_rating,

            'hotdeals' => $this->when($request->routeIs('shop.details'), HotDealResource::collection($this->hotDeals)),

            'products' => $this->when($request->routeIs('shop.products'), ProductResource::collection($this->products)),
        ];
    }
}
