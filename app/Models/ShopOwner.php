<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Casts\Attribute;

class ShopOwner extends Model implements JWTSubject, AuthenticatableContract
{
    use HasFactory, Authenticatable;

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function shop(){
        return $this->hasOne(Shop::class);
    }

    protected function photo(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => BASE_URL() . $value ,
        );
    }
}
