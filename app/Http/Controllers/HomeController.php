<?php

namespace App\Http\Controllers;

use App\Http\Resources\BlogResource;
use App\Http\Resources\BrandCollection;
use App\Http\Resources\BrandResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\HotDealResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ShopResource;
use App\Http\Resources\SliderResource;
use App\Models\Blog;
use App\Models\Brand;
use App\Models\Category;
use App\Models\HotDeal;
use App\Models\Product;
use App\Models\Shop;
use App\Models\Slider;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function sliders(){
        try {
            $sliders = Slider::query()->select('id','photo')->where('status',1)->get();
        
            return  $this->success(SliderResource::collection($sliders));
        } catch (\Throwable $th) {
                return response()->json([
                    'status' => false,
                    'message' => $th->getMessage(),
            ]);
        }
    }

    public function hotDeals(){
        try {
            $deals = HotDeal::query()->select('id','shop_id','banner','title')
                ->with('shop:id,name,logo')
                ->where('status',1)
                ->get();
            
            return  $this->success(HotDealResource::collection($deals));
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ]);
        }
    }

    public function categories(){
        try {
            $categories = Category::query()->select('id','photo','name','is_shown_navbar','is_shown_sidebar')
            ->with('subcategories:id,category_id,name,photo')
            ->where('status',1)
            ->get();
        
        return  $this->success(CategoryResource::collection($categories));

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ]);
        }

    }

    public function brands(){
        try {
            $brands = Brand::query()->select('id','photo','name','details')
            ->where('status',1)
            ->paginate(1);
            
        return  $this->success(BrandResource::collection($brands)->response()->getData(true));

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ]);
        }

    }
    public function shops(){
        try {
            $shops = Shop::query()->select('id','shop_owner_id','logo','name','photo','discription','division','commission_rate')
            ->with('owner')
            ->where('status','APPROVED')
            ->get();
        
        return  $this->success(ShopResource::collection($shops));

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ]);
        }

    }
    public function products(){
        try {
            $products = Product::query()
            ->select('id','sub_category_id','name','quantity','discount_type','discount','regular_price','discounted_price','is_available','images')
            ->with('subcategory:id,category_id,name','subcategory.category:id,name')
            ->where('status','APPROVED')
            ->where('is_visible',1)
            ->get();
        
        return  $this->success(ProductResource::collection($products));

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ]);
        }

    }
    public function blogs(){
        try {
            $blogs = Blog::query()
            ->select('id','title','description','photos','date')
            ->where('status',1)
            ->get();
        
        return  $this->success(BlogResource::collection($blogs));

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ]);
        }

    }
}
