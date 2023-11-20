<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        return [

            'access_token' => $this->when($request->routeIs('user.profile') == false, $this->token),
            'token_type'   => $this->when($request->routeIs('user.profile') == false, 'bearer'),
            'id'           => $this->id,
            'name'         => $this->name,
            'photo'        => $this->photo,
            'mobile'       => $this->mobile,
            'email'        => $this->email,
            'address'      => new UserAddressResource($this->address()->first()),
        ];
    }
}
