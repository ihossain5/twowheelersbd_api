<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VendorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'access_token' => $this->when($request->routeIs('vendor.profile') == false, $this->token),
            'token_type' => $this->when($request->routeIs('vendor.profile') == false, 'bearer'),
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'photo' => $this->photo,
            'mobile' => $this->mobile,
            'address' => $this->address,
            'status' => $this->status,
        ];
    }
}
