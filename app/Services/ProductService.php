<?php

namespace App\Services;

use App\Models\Product;

class ProductService {

    public $product;

    public function __construct(Product $product) {
        $this->product = $product;
    }

    public function select(){
        return $this->product->query()->select('id', 'name','images', 'selling_price', 'average_rating','regular_price','discount','brand_id','quantity','is_motorbike');
    }

    public function condition($motorbike = 0){
        return $this->select()->where('status','APPROVED')
        ->where('is_visible',1)
        ->where('is_motorbike',$motorbike);
    }

    public function getResults($pagination){
        return $this->condition()->latest()->paginate($pagination);
    }

    public function allProduct(){
        return $this->select()->where('status','APPROVED')
        ->where('is_visible',1);
    }
}