<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BrandCategory extends Model
{
    use HasFactory;

    public function models(){
        return $this->hasMany(BrandModel::class)->where('status',1);
    }
    public function brand(){
        return $this->belongsTo(Brand::class);
    }
}
