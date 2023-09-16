<?php

namespace App\Http\Controllers;

use App\Http\Resources\ShopResource;
use App\Models\Shop;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function shopHotdeals(){

    }

    public function singleShop($id){
        $shop = Shop::query()->findOrFail($id);

        return  $this->success(new ShopResource($shop));
    }
}
