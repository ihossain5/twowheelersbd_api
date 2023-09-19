<?php

namespace App\Services;

use App\Models\HotDeal;
use App\Models\HotDealProduct;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class HotDealService {
    private $model;

    public function __construct(HotDeal $model) {
        $this->model = $model;
    }

    public function store($data, $shop_id, $hot_deal = null){
      
        if($hot_deal){
            $hot_deal = $hot_deal;
            $banner = $hot_deal->banner;
        } else {
            $hot_deal = $this->model;
        } 
        
        if(array_key_exists('banner',$data)){
            if($hot_deal) ( new ImageUoloadService())->deleteImage($hot_deal->banner);
            
            $banner = ( new ImageUoloadService())->storeImage($data['banner'],'shop/hot-deals/',785,330);
        }
        
        DB::transaction(function () use($shop_id, $data ,$banner, $hot_deal){
            $hot_deal->shop_id = $shop_id;
            $hot_deal->title = $data['title'];
            $hot_deal->status = 1;
            $hot_deal->banner = $banner;
            $hot_deal->save();
    
            if(array_key_exists('products',$data)){
                $this->saveHotDealProduct($data, $hot_deal);
            }
        });
    

        return $hot_deal;
    }

    private function setPrice($product_id, $discounted_price, $discount){
        $product = Product::where('id',$product_id)->first();
        $product->discount = $discount;
        $product->discounted_price = $discounted_price;
        $product->selling_price = $discounted_price;
        $product->discount_type = 'PERCENTAGE';
        $product->save();
  
        return $product;
      }
  
      private function saveHotDealProduct($data, $hot_deal){
        foreach (json_decode($data['products']) as $product){
          $existing_product = Product::findOrFail($product->id);
          
          $hot_deal_product = new HotDealProduct();
          $hot_deal_product->hot_deal_id = $hot_deal->id;
          $hot_deal_product->product_id = $product->id;
          $hot_deal_product->percentage = $product->percentage;
          $hot_deal_product->discounted_price = $product->discounted_price;
          $hot_deal_product->old_discount_type = $existing_product->discount_type;
          $hot_deal_product->old_discount = $existing_product->discount;
          $hot_deal_product->old_regular_price = $existing_product->regular_price;
          $hot_deal_product->old_discounted_price = $existing_product->discounted_price;
          $hot_deal_product->old_selling_price = $existing_product->selling_price;
          $hot_deal_product->save();
  
          $this->setPrice($product->id, $product->discounted_price, $product->percentage);
        }
      }
}