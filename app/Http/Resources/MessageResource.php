<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        return [
            'send_by' => $this->send_by,
            'message' => $this->message,
            'image'   => $this->when($request->routeIs('user.send.message'),$this->image ),
        ];
    }
}
