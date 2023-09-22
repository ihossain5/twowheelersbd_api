<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\VendoCategoryResource;
use App\Models\Brand;
use App\Models\BrandCategory;
use App\Models\BrandModel;
use App\Models\Category;
use App\Models\Specification;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function categories(){
        return $this->success(VendoCategoryResource::collection(Category::query()->select('id','name')->get()));
    }

    public function subcategories($id){
        return $this->success(SubCategory::query()->select('id','name')->where('category_id',$id)->get());
    }

    public function brands(){
        return $this->success(Brand::query()->select('id','name')->get());
    }

    public function brandModels($id){
        $brand_category_ids = BrandCategory::query()->where('brand_id',$id)->pluck('id');

        return $this->success(BrandModel::query()->select('id','name')->whereIn('brand_category_id',$brand_category_ids)->get());
    }

    
    public function specifications(){
        return $this->success(Specification::query()->select('id','name')->get());
    }
}
