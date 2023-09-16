<?php

namespace App\Http\Controllers;

use App\Http\Resources\BrandCategoryResource;
use App\Http\Resources\BrandModelResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\MotorbikeResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ShopResource;
use App\Http\Resources\SubcategoryResource;
use App\Models\BrandCategory;
use App\Models\BrandModel;
use App\Models\Category;
use App\Models\Product;
use App\Models\Shop;
use App\Models\SubCategory;
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

    public function productsByCategory(Request $request, $id){
        if($request->pagination) $this->pagination = $request->pagination;

        $sub_category_ids = SubCategory::query()->where('category_id',$id)->pluck('id');

        $products = Product::query()
                ->select('id','sub_category_id', 'brand_id','shop_id', 'brand_model_id', 'additional_names', 'colors', 'description', 'video', 'sizes','catelogue_pdf', 'name','quantity','discount_type','discount','regular_price','discounted_price','is_available','images','status','sku','additional_name_1','additional_name_2','additional_name_3','additional_name_4','additional_name_5','average_rating')
                ->whereIn('sub_category_id',$sub_category_ids)
                ->where('status','APPROVED')
                ->where('is_visible',1)
                ->where('is_motorbike',0)
                ->paginate($this->pagination);

        if($products->count() < 1) {
            return $this->productErrorResponse($id, 'Category');
        }

        return $this->success(ProductResource::collection($products)->response()->getData(true));
    }

    public function productsBySubCategory(Request $request, $id){
        if($request->pagination) $this->pagination = $request->pagination;

        $products = $this->productQuery($id, 'sub_category_id', $this->pagination); 

        if($products->count() < 1) {
            return $this->productErrorResponse($id, 'Sub Category');
        }  

        return $this->success(ProductResource::collection($products)->response()->getData(true));
    }

    public function productsByShop(Request $request, $id){
        if($request->pagination) $this->pagination = $request->pagination;

        $products = $this->productQuery($id, 'shop_id', $this->pagination);
        
        if($products->count() < 1) {
            return $this->productErrorResponse($id, 'Shop');
        }  

        return $this->success(ProductResource::collection($products)->response()->getData(true));
    }
    public function models(Request $request){
        if($request->pagination) $this->pagination = $request->pagination;

        $model = BrandModel::query()
            ->with('catelogues','colors','specifications')
             ->paginate($this->pagination);

        return $this->success(BrandModelResource::collection($model)->response()->getData(true));
    }
    
    public function productsByModel(Request $request, $id){
        if($request->pagination) $this->pagination = $request->pagination;

        $products = $this->productQuery($id, 'brand_model_id', $this->pagination);
        
        if($products->count() < 1) {
            return $this->productErrorResponse($id, 'Model');
        }  

        return $this->success(ProductResource::collection($products)->response()->getData(true));
    }

    public function accessories(Request $request){
        if($request->pagination) $this->pagination = $request->pagination;

        $subcategories = SubCategory::query()->where('category_id', 8)->pluck('id');
        
        $products = Product::query()
             ->whereIn('sub_category_id', $subcategories)
             ->where('status','APPROVED')
             ->where('is_motorbike',0)
             ->where('is_visible',1)
             ->paginate($this->pagination);

        return  $this->success(ProductResource::collection($products)->response()->getData(true));
    }

    public function motorbikes(Request $request){
        if($request->pagination) $this->pagination = $request->pagination;
   
        $motorbikes = Product::query()
             ->where('status','APPROVED')
             ->where('is_motorbike',1)
             ->where('is_visible',1)
             ->with('model','shop','subcategory','subcategory.category')
             ->paginate($this->pagination);

        return  $this->success(MotorbikeResource::collection($motorbikes)->response()->getData(true));
    }

    public function motorbikeDetails($id){
        $motorbikes = Product::query()
             ->where('status','APPROVED')
             ->where('is_motorbike',1)
             ->where('is_visible',1)
             ->with('model','shop','subcategory','subcategory.category')
             ->findOrFail($id);

        return  $this->success(new MotorbikeResource($motorbikes));
    }

    private function productQuery($id, $column_name, $pagination){
       return Product::query()
            ->select('id','sub_category_id', 'brand_id','shop_id', 'brand_model_id', 'additional_names', 'colors', 'description', 'video', 'sizes','catelogue_pdf', 'name','quantity','discount_type','discount','regular_price','discounted_price','is_available','images','status','sku')
            ->where($column_name,$id)
            ->where('status','APPROVED')
            ->where('is_visible',1)
            ->where('is_motorbike',0)
            ->paginate($pagination);
    }

    private function productErrorResponse($id, $name){
        return response()->json([
            'status' => false,
            'errors' => 'Not Found',
            'message' => 'No product found with '. $name. ' ID: '.$id,
       ],404);
    }
}
