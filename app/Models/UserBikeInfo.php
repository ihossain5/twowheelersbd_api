<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBikeInfo extends Model
{
    use HasFactory;

    public function brand(){
        return $this->belongsTo(Brand::class);
    }

    public function model(){
        return $this->belongsTo(BrandModel::class);
    }
}
