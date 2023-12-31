<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class HotDeal extends Model
{
    use HasFactory;

    public function shop(){
        return $this->belongsTo(Shop::class);
    }

    protected function banner(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => BASE_URL() . $value,
        );
    }

    public function products(){
        return $this->hasMany(HotDealProduct::class);
    }
}
