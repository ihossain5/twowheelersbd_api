<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CatelogueProductResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        return [
            'catelogue_title' => $this->catelogue->title,
            'catelogue_image' => BASE_URL() . $this->catelogue->image,
            'serial_no'       => $this->serial_no,
            'product'         => new ProductResource($this->product),
        ];
    }
}
