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
use App\Services\ImageUoloadService;
use App\Services\ProductService;
use App\Services\VideoService;
use Illuminate\Http\Request;

class VendorController extends Controller
{

    protected $vendor_id, $shop_id;

    public function __construct() {
        $this->shop_id = auth('vendor')->user()?->shop?->id;
        $this->vendor_id = auth('vendor')->user()?->id;
    }
    
    public function shopDetails(){
        $shop = Shop::query()->where('shop_owner_id',$this->vendor_id)->firstOrFail();

        return  $this->success(new ShopResource($shop));
    }

    public function shopVideos(Request $request){
        if($request->pagination) $this->pagination = $request->pagination;

        $videos = ShopVideo::query()->where('shop_id',$this->shop_id); 

        if($videos->count() < 1){
            return $this->errorResponse($this->shop_id,'Shop');
        }
        $videos =  $videos->paginate($this->pagination);

        return $this->success(VideoResource::collection($videos)->response()->getData(true));
    }

    public function shopVideoCreate(Request $request, VideoService $videoService){
        $request->validate([
            'preview_image' => 'required|image|max:1024|mimes:jpg,jpeg,png',
            'link' => 'required|string',
            'status' => 'required|boolean',
        ]);

        $video = $videoService->store($request->all(), $this->shop_id);

        return $this->success(new VideoResource($video));

    }

    public function shopVideoUpdate(Request $request, VideoService $videoService, $id){
        // dd($request->all());
        $request->validate([
            'preview_image' => 'image|max:1024|mimes:jpg,jpeg,png',
            'link' => 'required|string',
            'status' => 'required|boolean',
        ]);

        $video = ShopVideo::findOrFail($id);

        $video = $videoService->store($request->all(), $this->shop_id ,$video);

        return $this->success(new VideoResource($video));

    }

    public function shopVideoEdit($id){
        $video = ShopVideo::findOrFail($id);

        return $this->success(new VideoResource($video));
    }

    public function shopVideoDelete($id){
        $video = ShopVideo::findOrFail($id);

        ( new ImageUoloadService())->deleteImage($video->preview_image);

        $video->delete();
        
        return $this->success('Video has deleted');
    }

    public function shopDeals(Request $request){
        if($request->pagination) $this->pagination = $request->pagination;

        $deals = HotDeal::query()
        ->where('shop_id',$this->shop_id)
        ->when(!empty($request->search), function ($query) use($request){
            $query->where('title', 'like', '%'.$request->search.'%');
        });

        if($deals->count() < 1){
            return $this->errorResponse(null,'Shop');
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
