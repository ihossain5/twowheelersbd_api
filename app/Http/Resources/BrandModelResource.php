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
            'catelogue_pdf' => $this->catelogue_pdf,
            'catelogues' => $this->catelogues,
            'colors' => $this->colors,
            'specifications' => $this->specifications,
            
        ];
    }
}
