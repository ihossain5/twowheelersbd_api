<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        if ($request->routeIs('vendor.all.chat')) {
            return [
                'chat_id'    => $this->id,
                'user_name'  => $this->user?->name,
                'user_photo' => $this->user?->photo,
            ];
        }

        return [
            'chat_id'   => $this->id,
            'shop_name' => $this->owner?->shop?->name,
            'shop_logo' => $this->owner?->shop?->logo,
        ];
    }
}
