<?php

namespace App\Http\Controllers;

use App\Http\Resources\SliderResource;
use App\Models\Slider;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function sliders(){
        $sliders = Slider::query()->select('id','photo')->where('status',1)->get();
        return  $this->success(SliderResource::collection($sliders));
    }

    public function hotDeals(){
        $sliders = Slider::query()->select('id','photo')->where('status',1)->get();
        return  $this->success(SliderResource::collection($sliders));
    }
}
