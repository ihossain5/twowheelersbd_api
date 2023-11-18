<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        return [
            'id'         => $this->id,
            'user_name'  => $this->user?->name,
            'user_photo' => $this->user?->photo,
            'rating'     => $this->rating,
            'review'     => $this->review,
            'status'     => $this->status == 1 ? (int) 1 : (int) 0,
            'date'       => formatDate($this->created_at),
        ];
    }
}
