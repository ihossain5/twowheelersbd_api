<?php

namespace App\Services;

use App\Models\Product;

class ProductStoreService{
    public $product;

    public function __construct(Product $product) {
        $this->product = $product;
    }

    public function store($data){
      
    }
}