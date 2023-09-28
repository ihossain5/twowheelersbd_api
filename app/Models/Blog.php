<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Blog extends Model
{
    use HasFactory;

    protected function date(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => Carbon::parse($value)->format('F d, Y') ,
        );
    }

    public function admin(){
        return $this->belongsTo(Admin::class);
    }

    public function comments(){
        return $this->hasMany(BlogComment::class)
        ->where('status',1)
        ->orderBy('id','desc');
    }

}
