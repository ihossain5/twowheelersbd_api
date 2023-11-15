<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BrandModelCatelogue extends Model
{
    use HasFactory;

    public function catelogueProducts(){
        return $this->hasMany(CatelogueProduct::class,'brand_model_catelogue_id')->orderBy('serial_no');
    }

    public function model(){
        return $this->belongsTo(BrandModel::class,'brand_model_id');
    }
}
