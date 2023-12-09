<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductMotorResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        return [
            'motor_id' => $this->id ?? '',
            'brand_id' => $this->model->brandCategory->brand->id ?? '',
            'brand'    => $this->model->brandCategory->brand->name ?? '',
            'model_id' => $this->model->id ?? '',
            'model'    => $this->model->name ?? '',
        ];
    }
}
