<?php

namespace App\Http\Controllers;

use App\Http\Resources\BlogResource;
use App\Http\Resources\BrandCategoryResource;
use App\Http\Resources\BrandCollection;
use App\Http\Resources\BrandResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\HotDealResource;
use App\Http\Resources\MotorbikeModelResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ShopResource;
use App\Http\Resources\SliderResource;
use App\Models\Blog;
use App\Models\Brand;
use App\Models\BrandCategory;
use App\Models\BrandModel;
use App\Models\Category;
use App\Models\HotDeal;
use App\Models\Product;
use App\Models\Shop;
use App\Models\Slider;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function sliders(){
        $sliders = Slider::query()->select('id','photo')->where('status',1)->get();
        
        return  $this->success(SliderResource::collection($sliders));
    }

    public function hotDeals(Request $request){
        if($request->pagination) $this->pagination = $request->pagination;

        $deals = HotDeal::query()->select('id','shop_id','banner','title')
                ->with('shop:id,name,logo')
                ->where('status',1)
                ->paginate($this->pagination);
            
        return  $this->success(HotDealResource::collection($deals)->response()->getData(true));

    }

    public function categories(Request $request){
        if($request->pagination) $this->pagination = $request->pagination;

        $categories = Category::query()->select('id','photo','name','is_shown_navbar','is_shown_sidebar')
            ->with('subcategories:id,category_id,name,photo')
            ->where('status',1)
            ->paginate($this->pagination);
        
        return  $this->success(CategoryResource::collection($categories));

    }

    public function brands(Request $request){
        if($request->pagination) $this->pagination = $request->pagination;

        $brands = Brand::query()->select('id','photo','name','details')
            ->where('status',1)
            ->with('models')
            ->paginate($this->pagination);
            
        return  $this->success(BrandResource::collection($brands)->response()->getData(true));
    }    
    public function allBrands(Request $request){
        if($request->pagination) $this->pagination = $request->pagination;

        $brands = Brand::query()
            ->select('id','photo','name')
            ->where('status',1);
    
        return $this->success(BrandResource::collection($brands));
    }

    public function shops(Request $request){
        if($request->pagination) $this->pagination = $request->pagination;

            $shops = Shop::query()->select('id','shop_owner_id','logo','name','photo','discription','division','commission_rate')
            ->with('owner')
            ->where('status','APPROVED')
            ->paginate($this->pagination);
        
        return  $this->success(ShopResource::collection($shops)->response()->getData(true));
    }

    public function motorbikeModels(Request $request){
        if($request->pagination) $this->pagination = $request->pagination;

        $models = BrandModel::query()
        ->select('id','name','images')
            ->withCount('motorbikes');

        if ($models->count() < 1) {
            return $this->errorResponse('Category', 'accessories');
        }

        $models = $models->latest()->paginate($this->pagination);

        // $filtered_collection = $models->filter(function ($item) {
        //     return $item->motorbikes_count > 0;
        // })->values();

        return $this->success(MotorbikeModelResource::collection($models)->response()->getData(true));
    }

  
}
