<?php

namespace App\Http\Controllers;

use App\Http\Resources\BlogResource;
use App\Http\Resources\BrandCategoryResource;
use App\Http\Resources\BrandCollection;
use App\Http\Resources\BrandResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\HotDealResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ShopResource;
use App\Http\Resources\SliderResource;
use App\Models\Blog;
use App\Models\Brand;
use App\Models\BrandCategory;
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
            ->paginate($this->pagination);
            
        return  $this->success(BrandResource::collection($brands)->response()->getData(true));
    }

    public function shops(Request $request){
        if($request->pagination) $this->pagination = $request->pagination;

            $shops = Shop::query()->select('id','shop_owner_id','logo','name','photo','discription','division','commission_rate')
            ->with('owner')
            ->where('status','APPROVED')
            ->paginate($this->pagination);
        
        return  $this->success(ShopResource::collection($shops)->response()->getData(true));
    }
    // public function products(Request $request){
    //     try {
    //         if($request->pagination) $this->pagination = $request->pagination;

    //         $products = Product::query()
    //         ->select('id','sub_category_id', 'brand_id','shop_id', 'brand_model_id', 'additional_names', 'colors', 'description', 'video', 'sizes','catelogue_pdf', 'name','quantity','discount_type','discount','regular_price','discounted_price','is_available','images','status')
    //         ->with('subcategory:id,category_id,name','subcategory.category:id,name')
    //         ->where('status','APPROVED')
    //         ->where('is_visible',1)
    //         ->paginate($pagination ?? $this->pagination);
        
    //     return  $this->success(ProductResource::collection($products)->response()->getData(true));

    //     } catch (\Throwable $th) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => $th->getMessage(),
    //         ]);
    //     }

    // }

    public function blogs(Request $request){
        if($request->pagination) $this->pagination = $request->pagination;

        $blogs = Blog::query()
            ->select('id','title','description','photos','date')
            ->where('status',1)
            ->paginate($this->pagination);
        
        return  $this->success(BlogResource::collection($blogs)->response()->getData(true));
    }
}
