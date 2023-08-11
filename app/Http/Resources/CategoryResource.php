<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
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
            'photo' => $this->photo,
            'name' => $this->name,
            'is_shown_navbar' => $this->is_shown_navbar,
            'is_shown_sidebar' => $this->is_shown_sidebar,
            'products' => ProductResource::collection($this->products),
            'subcategories' => SubcategoryResource::collection($this->subcategories),

        ];
    }
}
