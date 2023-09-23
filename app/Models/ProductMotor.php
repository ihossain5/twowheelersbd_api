<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductMotor extends Model
{
    use HasFactory;

    public function model(){
        return $this->belongsTo(BrandModel::class,'brand_model_id');
    }

}
