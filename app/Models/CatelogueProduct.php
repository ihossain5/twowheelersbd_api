<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatelogueProduct extends Model
{
    use HasFactory;

    public function model(){
        return $this->belongsTo(BrandModel::class,'brand_model_id');
    }
    
    public function catelogue(){
        return $this->belongsTo(BrandModelCatelogue::class,'brand_model_catelogue_id');
    } 
    public function product(){
        return $this->belongsTo(Product::class,'product_id');
    }
}
