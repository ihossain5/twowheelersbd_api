<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BrandModel extends Model
{
    use HasFactory;

    protected $withCount  = ['motorbikes'];

    public function brandCategory(){
        return $this->belongsTo(BrandCategory::class);
    }

    public function catelogues(){
        return $this->hasMany(BrandModelCatelogue::class);
    }  
    
    public function colors(){
        return $this->hasMany(BrandModelColor::class);
    }  

    public function specifications(){
        return $this->hasMany(BrandModelSpecification::class);
    }  
    
    public function products(){
        return $this->hasMany(Product::class)->where('status','APPROVED')->where('is_motorbike',0)->where('is_visible',1);
    }
    
    public function motorbikes(){
        return $this->hasMany(Product::class)->where('status','APPROVED')->where('is_motorbike',1)->where('is_visible',1);
    }

    public function getTotalMotorbikesAttribute()
    {
    
        return $this->motorbikes()->count();
    
    }

    protected function video(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? BASE_URL() . $value : null,
        );
    }

    protected function catelogue_pdf(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? BASE_URL() . $value : null,
        );
    }

}
