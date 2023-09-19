<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopVideo extends Model
{
    use HasFactory;

    protected function previewImage(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => BASE_URL() . $value,
        );
    }
}
