<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class SubCategory extends Model
{
    use HasFactory;

    
    protected function photo(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => BASE_URL() . $value,
        );
    }
    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function products(){
        return $this->hasMany(Product::class)->where('status','APPROVED');
    }

}
