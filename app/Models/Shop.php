<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Shop extends Model
{
    use HasFactory;

    protected function logo(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => BASE_URL(). $value,
        );
    }
    protected function photo(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => BASE_URL() . $value,
        );
    }

    public function owner(){
        return $this->belongsTo(ShopOwner::class,'shop_owner_id');
    }

    public function products(){
        return $this->hasMany(Product::class);
    }
}
