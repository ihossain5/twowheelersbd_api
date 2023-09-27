<?php

namespace App\Http\Controllers;

use App\Http\Resources\ShopResource;
use App\Http\Resources\ShopVideoResourece;
use App\Models\Shop;
use App\Models\ShopVideo;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function shopHotdeals(){

    }

    public function singleShop($id){
        $shop = Shop::query()->findOrFail($id);

        return  $this->success(new ShopResource($shop));
    }

    public function shopVideos(Request $request, $id){
       
        if ($request->pagination) {
            $this->pagination = $request->pagination;
        }

        $videos = ShopVideo::query()->where('shop_id',$id)->where('status',1);
      
        if ($videos->count() < 1) {
            return $this->errorResponse($id, 'Shop');
        }

        $videos = $videos->latest()->paginate($this->pagination);

        return  $this->success(ShopVideoResourece::collection($videos)->response()->getData(true));
    }
}
