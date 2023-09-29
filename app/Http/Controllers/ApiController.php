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
