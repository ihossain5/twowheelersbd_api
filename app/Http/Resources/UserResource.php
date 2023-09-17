<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'access_token' => $this->when($request->routeIs('user.login') || $request->routeIs('vendor.login') | $request->routeIs('vendor.otp.verify')  , $this->token),
            'token_type' => $this->when($request->routeIs('user.login') || $request->routeIs('vendor.login') | $request->routeIs('vendor.otp.verify')  ,$this->token_type),
            'name' => $this->name,
            'photo' => $this->photo,
            'mobile' => $this->mobile,
        ];
    }
}
