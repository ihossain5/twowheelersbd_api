<?php

namespace App\Http\Controllers;

use App\Http\Resources\BrandCategoryResource;
use App\Http\Resources\BrandModelResource;
use App\Http\Resources\BrandWiseModelResource;
use App\Http\Resources\ProductResource;
use App\Models\Brand;
use App\Models\BrandCategory;
use App\Models\BrandModel;
use App\Models\Product;
use Illuminate\Http\Request;

class ApiController extends Controller
{

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

    public function brandWiseModels(Request $request){
        if($request->pagination) $this->pagination = $request->pagination;

        if($request->brand_id){
            $brands = Brand::query()->select('id','name')
            ->where('status',1)
            ->where('id',$request->brand_id)
            ->with('models');
           
        }else{
            $brands = Brand::query()->select('id','name')
            ->where('status',1)
            ->with('models');
        }

        if ($brands->count() < 1) {
            return $this->errorResponse('brand', 'models');
        }

        $brands = $brands->latest()->paginate($this->pagination);

        // $brand_drop_down = Brand::query()->select('id','name')->where('status',1)->get();

        return $this->success(BrandWiseModelResource::collection($brands)->response()->getData(true));

    }

    public function brandCategoryWiseModels(Request $request, $id){
        if($request->pagination) $this->pagination = $request->pagination;

            $brands = BrandCategory::query()->select('id','name')
            ->where('brand_id',$id)
            ->with('models');
           

        if ($brands->count() < 1) {
            return $this->errorResponse('brand', 'models');
        }

        $brands = $brands->latest()->paginate($this->pagination);

        // $brand_drop_down = Brand::query()->select('id','name')->where('status',1)->get();

        return $this->success(BrandWiseModelResource::collection($brands)->response()->getData(true));

    }
    
}
