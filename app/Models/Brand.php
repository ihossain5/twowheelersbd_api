<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Brand extends Model
{
    use HasFactory;

    protected function photo(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => BASE_URL() . $value,
        );
    }

    public function models(){
        return $this->hasManyThrough(BrandModel::class, BrandCategory::class)->where('brand_models.status',1);
    } 
}
