<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Str;

class ProductStoreService{
    public $product;

    public function __construct(Product $product) {
        $this->product = $product;
    }

    public function store($data , $shop_id, $product =null){
        if($product){
            $product = $product;
            $image_arr = collect(json_decode($product->images))->toArray();
            if (array_key_exists('images', $data)) {
                foreach ($data['images'] as $key => $photo) {
                    if (array_key_exists($key, $image_arr)) {
                        (new ImageUoloadService())->deleteImage($image_arr[$key]);
                        unset($image_arr[$key]);
                    }
                    $image_arr[$key] = (new ImageUoloadService())->storeImage($photo, 'product/motorbike/images/',568,570);
                }
            }
            ksort($image_arr);
            $image_arr = array_combine(range(1, count($image_arr)), array_values($image_arr));

           
        } else {
            $product = $this->product;
            $image_arr = [];
            if (array_key_exists('images', $data)){
                foreach ($data['images'] as $key => $image) {
                    $image_arr[$key] = (new ImageUoloadService())->storeImage($image, 'product/motorbike/images/', 568, 570);
                }
            }
        } 

        $product->name = $data['name'];
        $product->slug = Str::slug($data['name'] .' '.$product->id);
        $product->shop_id = $shop_id;
        $product->sub_category_id = $data['sub_category_id'];
        $product->brand_id = $data['brand_id'];
        $product->brand_model_id = $data['brand_model_id'];


        $product->sku = $data['sku'] ?? Str::slug($data['name']);

        if (array_key_exists('mileage', $data)){
            $product->mileage = $data['mileage'];
        }
        

        $product->is_motorbike = 1;

        $product->is_visible = $data['is_visible'];
        $product->description = $data['description'];
        $product->regular_price = $data['regular_price'];
        $product->quantity = $data['quantity'];
        $product->discount_type = $data['discount_type'];
        $product->discount = $data['discount'] ?? null ;
        $product->discounted_price = $data['discounted_price'] ?? null;
        $product->selling_price = $data['discounted_price'] ?? $data['regular_price'];

        $video = null;
        if (array_key_exists('video', $data)){
        
            if($product) ( new ImageUoloadService())->deleteFile($product->video);
            $video = (new ImageUoloadService())->storeFile($data['video'], 'product/motorbike/video/');
        }
    
        $product->images = json_encode($image_arr);

        $product->video = $video;
        $product->save();

        return $product;
    }
}