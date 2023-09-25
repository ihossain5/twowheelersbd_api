<?php

namespace App\Services;

use App\Models\Product;

class ProductService {

    public $product;

    public function __construct(Product $product) {
        $this->product = $product;
    }

    public function select(){
        return $this->product->query() ->select('id','sub_category_id', 'brand_id','shop_id', 'brand_model_id', 'additional_names', 'colors', 'description', 'video', 'sizes','catelogue_pdf', 'name','quantity','discount_type','discount','regular_price','discounted_price','is_available','images','status','sku','additional_name_1','additional_name_2','additional_name_3','additional_name_4','additional_name_5','average_rating');
    }

    public function condition(){
        return $this->select()->where('status','APPROVED')
        ->where('is_visible',1)
        ->where('is_motorbike',0);
    }

    public function getResults($pagination){
        return $this->condition()->latest()->paginate($pagination);
    }
}