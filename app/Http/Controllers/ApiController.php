<?php

namespace App\Http\Controllers;

use App\Http\Resources\BrandCategoryResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\SubcategoryResource;
use App\Models\BrandCategory;
use App\Models\Category;
use App\Models\Product;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function products(Request $request){
        try {
            if($request->pagination) $pagination = $request->pagination;

            $products = Product::query()
            ->select('id','sub_category_id', 'brand_id','shop_id', 'brand_model_id', 'additional_names', 'colors', 'description', 'video', 'sizes','catelogue_pdf', 'name','quantity','discount_type','discount','regular_price','discounted_price','is_available','images','status')
            ->with('subcategory:id,category_id,name','subcategory.category:id,name')
            ->where('status','APPROVED')
            ->where('is_visible',1)
            ->paginate($pagination ?? $this->pagination);
        
        return  $this->success(ProductResource::collection($products)->response()->getData(true));

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ]);
        }

    }

    public function getProductById($id){
        try {
            $product = Product::query()
            ->select('id','sub_category_id', 'brand_id','shop_id', 'brand_model_id', 'additional_names', 'colors', 'description', 'video', 'sizes','catelogue_pdf', 'name','quantity','discount_type','discount','regular_price','discounted_price','is_available','images','status')
            ->with('subcategory:id,category_id,name','subcategory.category:id,name')
            ->findOrFail($id);
        
        return  $this->success(new ProductResource($product));

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ]);
        }
    }

    public function brandCategories(Request $request){
        if($request->pagination) $this->pagination = $request->pagination;

        $brand_categories = BrandCategory::query()
            ->with('models')
            ->paginate($this->pagination);
        
        return  $this->success (BrandCategoryResource::collection($brand_categories)->response()->getData(true));
    }

    public function productsByCategory(Request $request,$id){
        $category = Category::query()
            ->with('products','subcategories')
            ->findOrFail($id);

        return  $this->success(new CategoryResource($category));
    }

    public function productsBySubCategory(Request $request,$id){
        $subcategory = SubCategory::query()
            ->with('products')
            ->findOrFail($id);

        return  $this->success(new SubcategoryResource($subcategory));
    }
}
