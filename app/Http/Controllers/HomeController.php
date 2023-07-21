<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Http\Resources\HotDealResource;
use App\Http\Resources\SliderResource;
use App\Models\Category;
use App\Models\HotDeal;
use App\Models\Slider;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function sliders(){
        $sliders = Slider::query()->select('id','photo')->where('status',1)->get();
       
        return  $this->success(SliderResource::collection($sliders));
    }

    public function hotDeals(){
        $deals = HotDeal::query()->select('id','shop_id','banner','title')
            ->with('shop:id,name,logo')
            ->where('status',1)
            ->get();
        
        return  $this->success(HotDealResource::collection($deals));
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
}
