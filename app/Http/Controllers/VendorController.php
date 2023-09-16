<?php

namespace App\Http\Controllers;

use App\Http\Resources\ShopResource;
use App\Http\Resources\VideoResource;
use App\Models\Shop;
use App\Models\ShopVideo;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    
    public function singleShop($vendor_id){
        $shop = Shop::query()->where('shop_owner_id',$vendor_id)->firstOrFail();

        return  $this->success(new ShopResource($shop));
    }

    public function shopVideos(Request $request, $id){
        if($request->pagination) $this->pagination = $request->pagination;

        $videos = ShopVideo::query()->where('shop_id',$id)->paginate($this->pagination); 

        return $this->success(VideoResource::collection($videos)->response()->getData(true));
    }
}
