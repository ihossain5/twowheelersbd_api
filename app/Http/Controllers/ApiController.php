<?php

namespace App\Http\Controllers;

use App\Http\Resources\BrandCategoryResource;
use App\Http\Resources\BrandModelResource;
use App\Http\Resources\ProductResource;
use App\Models\BrandCategory;
use App\Models\BrandModel;
use App\Models\Product;
use Illuminate\Http\Request;

class ApiController extends Controller
{


    public function getProductById($id){
        $product = Product::query()
            ->select('id','sub_category_id', 'brand_id','shop_id', 'brand_model_id', 'additional_names', 'colors', 'description', 'video', 'sizes','catelogue_pdf', 'name','quantity','discount_type','discount','regular_price','discounted_price','is_available','images','status','sku')
            ->with('subcategory:id,category_id,name','subcategory.category:id,name')
            ->where('status','APPROVED')
            ->where('is_visible',1)
            ->where('is_motorbike',0)
            ->findOrFail($id);
        
        return  $this->success(new ProductResource($product));
    }

    public function brandCategories(Request $request){
        if($request->pagination) $this->pagination = $request->pagination;

        $brand_categories = BrandCategory::query()
            ->with('models')
            ->paginate($this->pagination);
        
        return  $this->success (BrandCategoryResource::collection($brand_categories)->response()->getData(true));
    }

    public function models(Request $request){
        if($request->pagination) $this->pagination = $request->pagination;

        $model = BrandModel::query()
            ->with('catelogues','colors','specifications')
             ->paginate($this->pagination);

        return $this->success(BrandModelResource::collection($model)->response()->getData(true));
    }
    
}
