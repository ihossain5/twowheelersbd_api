<?php

namespace App\Http\Controllers;

use App\Http\Resources\HotDealResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ShopResource;
use App\Http\Resources\VideoResource;
use App\Models\HotDeal;
use App\Models\HotDealProduct;
use App\Models\Shop;
use App\Models\ShopVideo;
use App\Services\ProductService;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    
    public function singleShop($vendor_id){
        $shop = Shop::query()->where('shop_owner_id',$vendor_id)->firstOrFail();

        return  $this->success(new ShopResource($shop));
    }

    public function shopVideos(Request $request, $id){
        if($request->pagination) $this->pagination = $request->pagination;

        $videos = ShopVideo::query()->where('shop_id',$id); 

        if($videos->count() < 1){
            return $this->errorResponse($id,'Shop');
        }
        $videos =  $videos->paginate($this->pagination);

        return $this->success(VideoResource::collection($videos)->response()->getData(true));
    }

    public function shopDeals(Request $request, $id){
        if($request->pagination) $this->pagination = $request->pagination;

        $deals = HotDeal::query()->where('shop_id',$id); 

        if($deals->count() < 1){
            return $this->errorResponse($id,'Shop');
        }
        $deals =  $deals->paginate($this->pagination);

        return $this->success(HotDealResource::collection($deals)->response()->getData(true));
    }

    public function dealsProducts($deal_id, ProductService $productService){
        $ids = HotDealProduct::query()->where('hot_deal_id',$deal_id)->pluck('product_id');

        $products = $productService->select()->whereIn('id',$ids);

        if($products->count() < 1){
            return $this->errorResponse($deal_id,'Hot Deal');
        }
        
        $products =  $products->paginate($this->pagination);
       
        return $this->success(ProductResource::collection($products)->response()->getData(true));
    }
}
