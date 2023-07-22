<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    public function subcategory(){
        return $this->belongsTo(SubCategory::class,'sub_category_id');
    }

    public function brand(){
        return $this->belongsTo(Brand::class,'brand_id');
    }

    public function model(){
        return $this->belongsTo(BrandModel::class,'brand_model_id');
    }

    public function catelogues(){
        return $this->hasMany(ProductCatelogue::class);
    }

    public function specifications(){
        return $this->hasMany(ProductSpecification::class);
    }
    
}
