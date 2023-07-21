<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Slider extends Model
{
    use HasFactory;

    protected function photo(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => 'http://127.0.0.1:8000/admin/images/' . $value,
        );
    }
}
