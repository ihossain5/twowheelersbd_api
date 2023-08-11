<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Category extends Model
{
    use HasFactory;

    public function subcategories(){
        return $this->hasMany(SubCategory::class)->where('status',1);
    }

    public function products(){
        return $this->hasManyThrough(Product::class, SubCategory::class);
    }

    protected function photo(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => BASE_URL() . $value,
        );
    }
}
